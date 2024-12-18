<?php

declare(strict_types=1);

namespace Aeliot\Bundle\DoctrineEncrypted\Service;

use Doctrine\DBAL\Connection;

class EncryptedConnectionsRegistry
{
    public function __construct(
        private ConnectionNameProvider $connectionNameProvider,
        private array $encryptedConnections,
    ) {
    }

    /**
     * @return string[]
     */
    public function getNames(): array
    {
        return $this->encryptedConnections;
    }

    public function isEncrypted(string|Connection $connection): bool
    {
        if ($connection instanceof Connection) {
            $connection = $this->connectionNameProvider->getName($connection);
        }

        return in_array($connection, $this->encryptedConnections, true);
    }
}
