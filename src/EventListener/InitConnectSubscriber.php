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

namespace Aeliot\Bundle\DoctrineEncrypted\EventListener;

use Aeliot\Bundle\DoctrineEncrypted\Service\EncryptedConnectionsRegistry;
use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;

final class InitConnectSubscriber implements EventSubscriber
{
    public function __construct(
        private EncryptedConnectionsRegistry $encryptedConnectionsRegistry,
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postConnect,
        ];
    }

    /**
     * @todo: implement another way to determine default character set and collation for connection
     */
    public function postConnect(ConnectionEventArgs $event): void
    {
        $connection = $event->getConnection();
        if (!$this->encryptedConnectionsRegistry->isEncrypted($connection)) {
            return;
        }

        $connectionParameters = $connection->getParams();

        $characterSet = $connectionParameters['charset'] ?? 'utf8mb4';
        $collation = $connectionParameters['defaultTableOptions']['collate'] ?? 'utf8mb4_unicode_ci';

        $sql = 'SET NAMES :character_set COLLATE :collation';

        $connection->prepare($sql)
            ->executeStatement([
                'character_set' => $characterSet,
                'collation' => $collation,
            ]);
    }
}
