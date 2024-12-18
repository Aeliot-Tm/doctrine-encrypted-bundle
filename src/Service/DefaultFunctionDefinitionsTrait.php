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

use Aeliot\Bundle\DoctrineEncrypted\Enum\DatabaseErrorEnum;
use Aeliot\Bundle\DoctrineEncrypted\Enum\FunctionEnum;
use Aeliot\Bundle\DoctrineEncrypted\Enum\PlatformEnum;
use Doctrine\DBAL\Connection;

trait DefaultFunctionDefinitionsTrait
{
    /**
     * @return array<string,array<string,string>>
     */
    public function getDefinitions(Connection $connection): array
    {
        return [
            FunctionEnum::DECRYPT => [
                PlatformEnum::MYSQL => sprintf(
                    'CREATE
                        FUNCTION %1$s(source_data LONGBLOB) RETURNS LONGTEXT
                        DETERMINISTIC
                        SQL SECURITY DEFINER
                    BEGIN
                        RETURN AES_DECRYPT(source_data, %2$s());
                    END;',
                    FunctionEnum::DECRYPT,
                    FunctionEnum::GET_ENCRYPTION_KEY
                ),
            ],
            FunctionEnum::ENCRYPT => [
                PlatformEnum::MYSQL => sprintf(
                    'CREATE
                        FUNCTION %1$s(source_data LONGTEXT) RETURNS LONGBLOB
                        DETERMINISTIC
                        SQL SECURITY DEFINER
                    BEGIN
                        RETURN AES_ENCRYPT(source_data, %2$s());
                    END;',
                    FunctionEnum::ENCRYPT,
                    FunctionEnum::GET_ENCRYPTION_KEY
                ),
            ],
            FunctionEnum::GET_ENCRYPTION_KEY => [
                PlatformEnum::MYSQL => sprintf(
                    'CREATE
                        FUNCTION %1$s() RETURNS TEXT
                        DETERMINISTIC
                        SQL SECURITY DEFINER
                    BEGIN
                        IF (@encryption_key IS NULL OR LENGTH(@encryption_key) = 0) THEN
                            SIGNAL SQLSTATE \'%2$s\'
                                SET MESSAGE_TEXT = \'Encryption key not found\';
                        END IF;
                        RETURN @encryption_key;
                    END;',
                    FunctionEnum::GET_ENCRYPTION_KEY,
                    DatabaseErrorEnum::EMPTY_ENCRYPTION_KEY
                ),
            ],
        ];
    }
}
