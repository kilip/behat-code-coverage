<?php

/*
 * This file is part of the doyo/behat-coverage-extension project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Doyo\Behat\Coverage\Listener;

use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Testwork\EventDispatcher\Event\AfterTested;
use Behat\Testwork\EventDispatcher\Event\ExerciseCompleted;
use Behat\Testwork\Tester\Result\TestResult;
use Doyo\Behat\Coverage\Bridge\CodeCoverage\TestCase;
use Doyo\Behat\Coverage\Bridge\Symfony\EventDispatcher;
use Doyo\Behat\Coverage\Event\CoverageEvent;
use Doyo\Behat\Coverage\Event\RefreshEvent;
use Doyo\Behat\Coverage\Event\ReportEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BehatEventListener implements EventSubscriberInterface
{
    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @var CoverageEvent
     */
    protected $coverageEvent;

    public function __construct(
        EventDispatcher $dispatcher
    ) {
        $this->dispatcher    = $dispatcher;
        $this->coverageEvent = new CoverageEvent();
    }

    public static function getSubscribedEvents()
    {
        return [
            ExerciseCompleted::BEFORE => 'refreshCoverage',
            ScenarioTested::BEFORE    => 'startCoverage',
            ExampleTested::BEFORE     => 'startCoverage',
            ScenarioTested::AFTER     => 'stopCoverage',
            ExampleTested::AFTER      => 'stopCoverage',
            ExerciseCompleted::AFTER  => 'generateReport',
        ];
    }

    public function refreshCoverage()
    {
        $dispatcher      = $this->dispatcher;
        $event           = new RefreshEvent();
        $coverageEvent   = $this->coverageEvent;

        $coverageEvent->setTestCase(null);
        $coverageEvent->setCoverage([]);
        $dispatcher->dispatch($event, CoverageEvent::BEFORE_REFRESH);
        $dispatcher->dispatch($event, CoverageEvent::REFRESH);
    }

    public function startCoverage($scope)
    {
        $scenario      = $scope->getScenario();
        $id            = $scope->getFeature()->getFile().':'.$scenario->getLine();
        $dispatcher    = $this->dispatcher;
        $coverageEvent = $this->coverageEvent;
        $coverageId    = new TestCase($id);

        $coverageEvent->setTestCase($coverageId);
        $dispatcher->dispatch($coverageEvent, CoverageEvent::BEFORE_START);
        $dispatcher->dispatch($coverageEvent, CoverageEvent::START);
        $this->coverageEvent = $coverageEvent;
    }

    public function stopCoverage(AfterTested $testedEvent)
    {
        $dispatcher = $this->dispatcher;
        $coverageEvent      = $this->coverageEvent;
        $result = $testedEvent->getTestResult();

        $map = [
            TestResult::PASSED => TestCase::RESULT_PASSED,
            TestResult::FAILED => TestCase::RESULT_FAILED,
            TestResult::SKIPPED => TestCase::RESULT_SKIPPED,
            TestResult::PENDING => TestCase::RESULT_SKIPPED
        ];
        $result = $map[$result->getResultCode()];
        $coverageEvent->getTestCase()->setResult($result);
        $dispatcher->dispatch($coverageEvent, CoverageEvent::BEFORE_STOP);
        $dispatcher->dispatch($coverageEvent, CoverageEvent::STOP);
    }

    public function generateReport()
    {
        $dispatcher = $this->dispatcher;
        $event      = new ReportEvent();

        $dispatcher->dispatch($event, ReportEvent::BEFORE_PROCESS);
        $dispatcher->dispatch($event, ReportEvent::PROCESS);
        $dispatcher->dispatch($event, ReportEvent::AFTER_PROCESS);
    }
}
