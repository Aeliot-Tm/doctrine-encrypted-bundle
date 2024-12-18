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

use Aeliot\Bundle\DoctrineEncrypted\Doctrine\DBAL\Logging\MaskingParamsSQLLogger;
use Aeliot\Bundle\DoctrineEncrypted\Exception\ConnectionException;
use Aeliot\Bundle\DoctrineEncrypted\Exception\SecurityConfigurationException;
use Aeliot\Bundle\DoctrineEncrypted\Service\ConnectionPreparerInterface;
use Aeliot\Bundle\DoctrineEncrypted\Service\EncryptedConnectionsRegistry;
use Aeliot\Bundle\DoctrineEncrypted\Service\SecretProviderInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Exception as DBALException;

final class EncryptionKeyInjectorSubscriber implements EventSubscriber
{
    private const ENCRYPTION_KEY_PARAMETER = 'app_encryption_key';

    public function __construct(
        private ConnectionPreparerInterface $connectionPreparer,
        private EncryptedConnectionsRegistry $encryptedConnectionsRegistry,
        private SecretProviderInterface $secretProvider,
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postConnect,
        ];
    }

    public function postConnect(ConnectionEventArgs $event): void
    {
        $connection = $event->getConnection();
        if (!$this->encryptedConnectionsRegistry->isEncrypted($connection)) {
            return;
        }

        $key = $this->secretProvider->getKey($connection);
        if (!$key) {
            throw new SecurityConfigurationException('Project encryption key is undefined.');
        }

        $secret = $this->secretProvider->getSecret($connection);
        if (!$secret) {
            throw new SecurityConfigurationException('Project encryption secret is undefined.');
        }

        $this->maskParamsLogging($connection);
        $this->prepareConnection($connection, $key, $secret);
    }

    private function maskParamsLogging(Connection $currentConnection): void
    {
        $logger = $currentConnection->getConfiguration()->getSQLLogger();
        if (!$logger) {
            return;
        }

        $currentConnection
            ->getConfiguration()
            ->setSQLLogger(new MaskingParamsSQLLogger($logger, [self::ENCRYPTION_KEY_PARAMETER]));
    }

    private function prepareConnection(
        Connection $currentConnection,
        string $key,
        #[\SensitiveParameter] string $secret,
    ): void {
        $this->connectionPreparer->prepareConnection($currentConnection);
        $param = $this->connectionPreparer->wrapParameter(sprintf(':%s', self::ENCRYPTION_KEY_PARAMETER));
        $sql = sprintf('SET @%s = %s;', $key, $param);
        $statement = $currentConnection->prepare($sql);

        try {
            $statement->executeStatement([self::ENCRYPTION_KEY_PARAMETER => $secret]);
        } catch (DBALException $exception) {
            throw new ConnectionException('Failed to inject encryption key.', 0, $exception);
        }
    }
}
