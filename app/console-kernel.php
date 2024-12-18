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

use Aeliot\Bundle\DoctrineEncrypted\Tests\App\Kernel;

require_once __DIR__ . '/bootstrap.php';

return new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
