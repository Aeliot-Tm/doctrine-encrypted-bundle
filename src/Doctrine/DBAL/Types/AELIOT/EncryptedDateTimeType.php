<?php

declare(strict_types=1);

namespace Aeliot\Bundle\EncryptDB\Doctrine\DBAL\Types\AELIOT;

use Aeliot\Bundle\EncryptDB\Enum\EncryptedTypeEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeType;

class EncryptedDateTimeType extends DateTimeType
{
    use EncryptionUtilsTrait;

    public function getName(): string
    {
        return EncryptedTypeEnum::AELIOT_ENCRYPTED_DATETIME;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getBinaryTypeDeclarationSQL($fieldDeclaration);
    }
}