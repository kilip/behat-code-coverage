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

require __DIR__.'/../../../vendor/autoload.php';

use Doyo\Behat\Coverage\Bridge\CodeCoverage\Controller\RemoteController;
use Doyo\Behat\Coverage\Bridge\CodeCoverage\Session\LocalSession;

LocalSession::startSession('coverage');
RemoteController::create()->getResponse()->send();