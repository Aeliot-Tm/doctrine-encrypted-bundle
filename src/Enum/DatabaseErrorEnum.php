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

namespace Aeliot\Bundle\DoctrineEncrypted\Enum;

final class DatabaseErrorEnum
{
    public const EMPTY_ENCRYPTION_KEY = 'AEKEY';

    private function __construct()
    {
    }
}
