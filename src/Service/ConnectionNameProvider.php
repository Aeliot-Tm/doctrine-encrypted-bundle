<?php

declare(strict_types=1);

namespace Aeliot\Bundle\DoctrineEncrypted\Service;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ConnectionRegistry;

class ConnectionNameProvider
{
    public function __construct(
        private ConnectionRegistry $registry,
    ) {
    }

    public function getName(Connection $currentConnection): string
    {
        foreach ($this->registry->getConnections() as $name => $connection) {
            if ($connection === $currentConnection) {
                return $name;
            }
        }

        throw new \LogicException('Cannot find name of connection.');
    }
}
