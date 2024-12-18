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

namespace App\Doctrine\Encryption;

use Aeliot\Bundle\DoctrineEncrypted\Enum\FunctionEnum;
use Aeliot\Bundle\DoctrineEncrypted\Service\PlatformFunctionProviderInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Use split key with one part set in connection and second on obtained from secret table of database.
 *
 * There:
 * - secret_database is the database name,
 * - secret_table is table name.
 */
class FunctionProvider implements PlatformFunctionProviderInterface
{
    public const FUNCTION_NAME = 'APP_BUILD_KEY';
    public const PARAMETER_NAME = 'app_encryption_key';

    public function __construct(private PlatformFunctionProviderInterface $decoratedFunctionProvider)
    {
    }

    public function getDefinitions(): array
    {
        $definitions = $this->decoratedFunctionProvider->getDefinitions();

        $definitions[FunctionEnum::GET_ENCRYPTION_KEY] = sprintf(
            'CREATE
                FUNCTION %1$s() RETURNS TEXT
                DETERMINISTIC
                SQL SECURITY DEFINER
            BEGIN
                IF (@encryption_key IS NULL)
                THEN
                    SET @encryption_key = %2$s(@%3$s);
                END IF;

                IF (@encryption_key IS NULL OR LENGTH(@encryption_key) = 0)
                THEN
                    SIGNAL SQLSTATE \'PEKEY\'
                        SET MESSAGE_TEXT = \'Encryption key not found\';
                END IF;

                RETURN @encryption_key;
            END;',
            FunctionEnum::GET_ENCRYPTION_KEY,
            self::FUNCTION_NAME,
            self::PARAMETER_NAME,
        );

        $definitions[self::FUNCTION_NAME] = sprintf(
            'CREATE
                FUNCTION %1$s(env_key TEXT) RETURNS TEXT
                LANGUAGE SQL
                DETERMINISTIC
                READS SQL DATA
                SQL SECURITY DEFINER
            BEGIN
                DECLARE db_key varchar(64) DEFAULT NULL;
                DECLARE exist_secrets_table INT DEFAULT NULL;
                SET db_key = NULL;

                SELECT COUNT(1) INTO exist_secrets_table FROM INFORMATION_SCHEMA.TABLES
                     WHERE TABLE_SCHEMA = "secret_database" AND TABLE_NAME = "secret_table";

                IF (exist_secrets_table > 0)
                THEN
                    SELECT secret INTO db_key
                      FROM secret_database.secret_table WHERE id = "db_secret";
                END IF;

                IF (exist_secrets_table > 0 AND (db_key IS NULL OR LENGTH(db_key) != 64))
                THEN
                    SIGNAL SQLSTATE \'PEKEY\'
                        SET MESSAGE_TEXT = \'Cannot build key\';
                END IF;

                RETURN CONCAT(db_key, env_key);
            END;',
            self::FUNCTION_NAME,
        );

        return $definitions;
    }

    public function supports(AbstractPlatform $platform): bool
    {
        return $this->decoratedFunctionProvider->supports($platform);
    }
}
