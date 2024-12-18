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

namespace Aeliot\Bundle\DoctrineEncrypted\Service\PlatformFunctionProviderInterface;

use Aeliot\Bundle\DoctrineEncrypted\Service\PlatformFunctionProviderInterface;
use Aeliot\DoctrineEncrypted\Contracts\CryptographicSQLFunctionNameProviderInterface;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class DefaultMysqlFunctionProvider implements PlatformFunctionProviderInterface
{
    public const FUNC_GET_ENCRYPTION_KEY = 'APP_GET_ENCRYPTION_KEY';

    public function __construct(private CryptographicSQLFunctionNameProviderInterface $functionNameProvider)
    {
    }

    public function getDefinitions(): array
    {
        $decryptFunctionName = $this->functionNameProvider->getDecryptFunctionName();
        $encryptFunctionName = $this->functionNameProvider->getEncryptFunctionName();

        return [
            $decryptFunctionName => sprintf(
                'CREATE
                        FUNCTION %1$s(source_data LONGBLOB) RETURNS LONGTEXT
                        DETERMINISTIC
                        SQL SECURITY DEFINER
                    BEGIN
                        RETURN AES_DECRYPT(source_data, %2$s());
                    END;',
                $decryptFunctionName,
                self::FUNC_GET_ENCRYPTION_KEY
            ),
            $encryptFunctionName => sprintf(
                'CREATE
                        FUNCTION %1$s(source_data LONGTEXT) RETURNS LONGBLOB
                        DETERMINISTIC
                        SQL SECURITY DEFINER
                    BEGIN
                        RETURN AES_ENCRYPT(source_data, %2$s());
                    END;',
                $encryptFunctionName,
                self::FUNC_GET_ENCRYPTION_KEY
            ),
            self::FUNC_GET_ENCRYPTION_KEY => sprintf(
                'CREATE
                        FUNCTION %1$s() RETURNS TEXT
                        DETERMINISTIC
                        SQL SECURITY DEFINER
                    BEGIN
                        IF (@encryption_key IS NULL OR LENGTH(@encryption_key) = 0) THEN
                            SIGNAL SQLSTATE \'PEKEY\'
                                SET MESSAGE_TEXT = \'Encryption key not found\';
                        END IF;
                        RETURN @encryption_key;
                    END;',
                self::FUNC_GET_ENCRYPTION_KEY,
            ),
        ];
    }

    public function supports(AbstractPlatform $platform): bool
    {
        return $platform instanceof AbstractMySQLPlatform;
    }
}
