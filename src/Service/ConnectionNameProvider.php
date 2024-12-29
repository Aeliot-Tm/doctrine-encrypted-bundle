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
use Doctrine\Persistence\ConnectionRegistry;

class ConnectionNameProvider
{
    public function __construct(
        private ConnectionRegistry $registry,
    ) {
    }

    public function getConnectionName(Connection $currentConnection): string
    {
        foreach ($this->registry->getConnections() as $name => $connection) {
            if ($connection === $currentConnection) {
                return $name;
            }
        }

        throw new \LogicException('Cannot get name of connection');
    }
}
