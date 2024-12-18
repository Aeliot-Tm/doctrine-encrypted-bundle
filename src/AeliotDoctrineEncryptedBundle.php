<?php

/*
 * This file is part of the Doctrine Encrypted Bundle.
 *
 * (c) Anatoliy Melnikov <5785276@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Aeliot\Bundle\DoctrineEncrypted;

use Aeliot\Bundle\DoctrineEncrypted\DependencyInjection\Compiler\EncryptionSQLWalkerCompilerPass;
use Aeliot\DoctrineEncrypted\Contracts\CryptographicSQLFunctionNameProviderInterface;
use Aeliot\DoctrineEncrypted\Contracts\CryptographicSQLFunctionWrapper;
use Aeliot\DoctrineEncrypted\Query\AST\Functions\AbstractSingleArgumentFunction;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AeliotDoctrineEncryptedBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new EncryptionSQLWalkerCompilerPass());
    }

    public function boot(): void
    {
        /** @var CryptographicSQLFunctionNameProviderInterface $functionNameProvider */
        $functionNameProvider = $this->container->get(CryptographicSQLFunctionNameProviderInterface::class);
        CryptographicSQLFunctionWrapper::setFunctionNameProvider($functionNameProvider);
        AbstractSingleArgumentFunction::setFunctionNameProvider($functionNameProvider);
    }
}
