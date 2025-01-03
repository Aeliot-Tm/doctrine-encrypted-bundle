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

final class DefaultSecretProvider implements SecretProviderInterface
{
    public function getKey(string|Connection $connection): string
    {
        return 'encryption_key';
    }

    public function getSecret(string|Connection $connection): string
    {
        return (string) getenv('DB_ENCRYPTION_KEY');
    }
}
