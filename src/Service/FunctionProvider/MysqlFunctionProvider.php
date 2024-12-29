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

namespace Aeliot\Bundle\DoctrineEncrypted\Service\FunctionProvider;

use Aeliot\Bundle\DoctrineEncrypted\Service\ConnectionFunctionProviderInterface;
use Aeliot\DoctrineEncrypted\Contracts\CryptographicSQLFunctionNameProviderInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class MysqlFunctionProvider implements ConnectionFunctionProviderInterface
{
    public const FUNC_GET_ENCRYPTION_KEY = 'AELIOT_GET_ENCRYPTION_KEY';

    public function __construct(private CryptographicSQLFunctionNameProviderInterface $functionNameProvider)
    {
    }

    public function getDefinitions(): array
    {
        $functions = [];

        $this->addDecryptFunction($functions);
        $this->addEncryptFunction($functions);
        $this->addGetKeyFunction($functions);

        return $functions;
    }

    public function supports(Connection $connection): bool
    {
        $platform = $connection->getDatabasePlatform();
        if (!$platform instanceof AbstractPlatform) {
            throw new \RuntimeException('Platform is not configured');
        }

        return $platform instanceof AbstractMySQLPlatform;
    }

    /**
     * @param array<string,string> $functions
     */
    private function addDecryptFunction(array &$functions): void
    {
        $functionName = $this->functionNameProvider->getDecryptFunctionName();
        $functions[$functionName] = sprintf(
            'CREATE
                FUNCTION %1$s(source_data LONGBLOB) RETURNS LONGTEXT
                DETERMINISTIC
                SQL SECURITY DEFINER
            BEGIN
                RETURN AES_DECRYPT(source_data, %2$s());
            END;',
            $functionName,
            self::FUNC_GET_ENCRYPTION_KEY
        );
    }

    /**
     * @param array<string,string> $functions
     */
    private function addEncryptFunction(array &$functions): void
    {
        $functionName = $this->functionNameProvider->getEncryptFunctionName();
        $functions[$functionName] = sprintf(
            'CREATE
                FUNCTION %1$s(source_data LONGTEXT) RETURNS LONGBLOB
                DETERMINISTIC
                SQL SECURITY DEFINER
            BEGIN
                RETURN AES_ENCRYPT(source_data, %2$s());
            END;',
            $functionName,
            self::FUNC_GET_ENCRYPTION_KEY
        );
    }

    /**
     * @param array<string,string> $functions
     */
    private function addGetKeyFunction(array &$functions): void
    {
        $functionName = self::FUNC_GET_ENCRYPTION_KEY;
        $functions[$functionName] = sprintf(
            'CREATE
                FUNCTION %1$s() RETURNS TEXT
                DETERMINISTIC
                SQL SECURITY DEFINER
            BEGIN
                IF (@encryption_key IS NULL OR LENGTH(@encryption_key) = 0) THEN
                    SIGNAL SQLSTATE \'DEKEY\'
                        SET MESSAGE_TEXT = \'Encryption key is empty or undefined\';
                END IF;
                RETURN @encryption_key;
            END;',
            $functionName,
        );
    }
}
