<?php

namespace spec\Doyo\Behat\Coverage\Bridge;

use Doyo\Behat\Coverage\Bridge\Compat;
use Doyo\Behat\Coverage\Bridge\LocalCoverage;
use Doyo\Behat\Coverage\Event\CoverageEvent;
use PhpSpec\ObjectBehavior;
use SebastianBergmann\CodeCoverage\Filter;
use Prophecy\Argument;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LocalCoverageSpec extends ObjectBehavior
{
    function let(
        $dummy,
        CoverageEvent $event
    ){
        $filter = new Filter();
        $filter->addFileToWhitelist(__DIR__.'/TestClass.php');
        $dummy->beADoubleOf(Compat::getDriverClass('Dummy'));
        $coverage = new CodeCoverage($dummy->getWrappedObject(), $filter);
        $this->beConstructedWith($coverage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(LocalCoverage::class);
    }

    function it_should_be_an_event_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_should_subscribe_coverage_event()
    {
        $this->getSubscribedEvents()->shouldHaveKeyWithValue(CoverageEvent::START, 'onCoverageStarted');
        $this->getSubscribedEvents()->shouldHaveKeyWithValue(CoverageEvent::STOP, 'onCoverageStopped');
        $this->getSubscribedEvents()->shouldHaveKeyWithValue(CoverageEvent::REFRESH, 'onCoverageRefresh');
    }

    function it_should_handle_coverage_start_event($dummy, CoverageEvent $event)
    {
        $event->getCoverageId()->willReturn('some-id');
        $dummy->start(true)->shouldBeCalled();
        $this->onCoverageStarted($event);
    }

    function it_should_handle_coverage_stop_event($dummy, CoverageEvent $event)
    {
        $data = [ __DIR__.'/TestClass.php' => [
            9 => 1,
            10 => 1,
            11 => 1,
            12 => 1,
            13 => 1,
        ]];
        $event->updateCoverage(Argument::any())->shouldBeCalled();
        $event->getCoverageId()->willReturn('some-id');
        $dummy->start(true)->shouldBeCalled();
        $dummy->stop(Argument::cetera())->shouldBeCalled()->willReturn($data);

        $this->onCoverageStarted($event);
        $this->onCoverageStopped($event);
    }

    private function shouldCovered()
    {
        $thisShouldBeCovered = true;
        return;
    }
}
