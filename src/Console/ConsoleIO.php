<?php

/*
 * This file is part of the doyo/behat-code-coverage project.
 *
 * (c) Anthonius Munthi <me@itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Doyo\Behat\Coverage\Console;

use Symfony\Component\Console\Style\StyleInterface as BaseInterface;

interface ConsoleIO extends BaseInterface
{
    public function sessionError($sessionName, $message);

    public function hasError(): bool;

    public function setHasError(bool $flag);
}
