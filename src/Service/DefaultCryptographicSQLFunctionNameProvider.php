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

use Aeliot\DoctrineEncrypted\Contracts\CryptographicSQLFunctionNameProviderInterface;
use Aeliot\DoctrineEncrypted\Query\Enum\FunctionEnum;

class DefaultCryptographicSQLFunctionNameProvider implements CryptographicSQLFunctionNameProviderInterface
{
    public function getDecryptFunctionName(): string
    {
        return FunctionEnum::DECRYPT;
    }

    public function getEncryptFunctionName(): string
    {
        return FunctionEnum::DECRYPT;
    }
}
