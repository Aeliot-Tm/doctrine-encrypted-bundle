<?php

declare(strict_types=1);

/*
 * This file is part of the Doctrine Encrypted Bundle.
 *
 * (c) Anatoliy Melnikov <5785276@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Symfony\Bundle\FrameworkBundle\Console\Application;

require_once __DIR__ . '/bootstrap.php';

return new Application(require __DIR__ . '/console-kernel.php');