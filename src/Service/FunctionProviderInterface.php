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

namespace Aeliot\Bundle\DoctrineEncrypted\Service;

use Doctrine\DBAL\Connection;

interface FunctionProviderInterface
{
    /**
     * @return array<string,string>
     */
    public function getDefinitions(Connection $connection): array;

    /**
     * @return string[]
     */
    public function getNames(Connection $connection): array;
}
