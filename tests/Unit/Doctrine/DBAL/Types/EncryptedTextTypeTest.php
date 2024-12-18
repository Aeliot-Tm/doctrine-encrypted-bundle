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

namespace Aeliot\Bundle\DoctrineEncrypted\Tests\Unit\Doctrine\DBAL\Types;

use Aeliot\Bundle\DoctrineEncrypted\Doctrine\DBAL\Types\EncryptedTextType;
use Aeliot\Bundle\DoctrineEncrypted\Enum\FieldTypeEnum;
use Aeliot\Bundle\DoctrineEncrypted\Enum\FunctionEnum;
use PHPUnit\Framework\TestCase;

final class EncryptedTextTypeTest extends TestCase
{
    use MockPlatformTrait;

    public function testCanRequireSQLConversion(): void
    {
        $encryptedType = new EncryptedTextType();
        self::assertTrue($encryptedType->canRequireSQLConversion());
    }

    public function testConvertToDatabaseValueSQL(): void
    {
        $platform = $this->mockPlatform($this);

        $encryptedType = new EncryptedTextType();
        self::assertEquals(
            sprintf('%s(sqlExpr)', FunctionEnum::ENCRYPT),
            $encryptedType->convertToDatabaseValueSQL('sqlExpr', $platform)
        );
    }

    public function testConvertToPHPValueSQL(): void
    {
        $platform = $this->mockPlatform($this);

        $encryptedType = new EncryptedTextType();
        self::assertEquals(
            sprintf('%s(sqlExpr)', FunctionEnum::DECRYPT),
            $encryptedType->convertToPHPValueSQL('sqlExpr', $platform)
        );
    }

    public function testGetDefaultFieldLength(): void
    {
        $platform = $this->mockPlatform($this);

        $encryptedType = new EncryptedTextType();
        self::assertNull($encryptedType->getDefaultFieldLength($platform));
    }

    public function testGetName(): void
    {
        $encryptedType = new EncryptedTextType();

        self::assertEquals(FieldTypeEnum::ENCRYPTED_TEXT, $encryptedType->getName());
    }

    public function testGetSQLDeclaration(): void
    {
        $platform = $this->mockPlatform($this);

        $encryptedType = new EncryptedTextType();
        $sqlDeclaration = $encryptedType->getSQLDeclaration([], $platform);
        self::assertEquals('BLOB_TYPE_DECLARATION', $sqlDeclaration);
    }
}
