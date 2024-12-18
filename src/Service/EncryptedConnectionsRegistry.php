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

class EncryptedConnectionsRegistry
{
    /**
     * @param string[] $encryptedConnections
     */
    public function __construct(
        private array $encryptedConnections,
        private ConnectionRegistry $registry,
    ) {
    }

    /**
     * @return string[]
     */
    public function getNames(): array
    {
        return $this->encryptedConnections ?: $this->registry->getConnectionNames();
    }

    public function isEncrypted(string|Connection $connection): bool
    {
        if ($connection instanceof Connection) {
            $connection = $this->getName($connection);
        }

        return \in_array($connection, $this->getNames(), true);
    }

    private function getName(Connection $currentConnection): string
    {
        foreach ($this->registry->getConnections() as $name => $connection) {
            if ($connection === $currentConnection) {
                return $name;
            }
        }

        throw new \LogicException('Cannot find name of connection.');
    }
}
