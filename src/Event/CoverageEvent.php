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

namespace Doyo\Behat\Coverage\Event;

use Doyo\Behat\Coverage\Bridge\Symfony\Event;

class CoverageEvent extends Event
{
    const BEFORE_START   = 'doyo.coverage.start.pre';
    const BEFORE_STOP    = 'doyo.coverage.stop.pre';
    const BEFORE_REFRESH = 'doyo.coverage.refresh.pre';
    const START          = 'doyo.coverage.start';
    const STOP           = 'doyo.coverage.stop';
    const REFRESH        = 'doyo.coverage.refresh';

    /**
     * @var string
     */
    private $coverageId;

    /**
     * @var array
     */
    private $coverage;

    public function __construct($coverageId = null)
    {
        $this->coverageId = $coverageId;
        $this->coverage   = [];
    }

    public function updateCoverage($coverage)
    {
        $aggregate = $this->coverage;

        foreach ($coverage as $class => $counts) {
            if (!isset($this->coverage[$class])) {
                $aggregate[$class] = $counts;
                continue;
            }

            foreach ($counts as $line => $status) {
                $status                   = !$status ? -1 : ($status > 1 ? 1 : $status);
                $aggregate[$class][$line] = $status;
            }
        }

        $this->coverage = $aggregate;
    }

    public function setCoverage(array $coverage)
    {
        $this->coverage = $coverage;
    }

    /**
     * @return array
     */
    public function getCoverage()
    {
        return $this->coverage;
    }

    /**
     * @return string
     */
    public function getCoverageId(): string
    {
        return $this->coverageId;
    }

    /**
     * @param string|null $coverageId
     */
    public function setCoverageId($coverageId=null)
    {
        $this->coverageId = $coverageId;
    }
}
