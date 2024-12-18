<?php

declare(strict_types=1);

namespace Aeliot\Bundle\DoctrineEncrypted\Service;

use Aeliot\DoctrineEncrypted\Contracts\CryptographicSQLFunctionNameProviderInterface;

class DefaultCryptographicSQLFunctionNameProvider implements CryptographicSQLFunctionNameProviderInterface
{
    public function getDecryptFunctionName(): string
    {
        return 'APP_DECRYPT';
    }

    public function getEncryptFunctionName(): string
    {
        return 'APP_DECRYPT';
    }
}
