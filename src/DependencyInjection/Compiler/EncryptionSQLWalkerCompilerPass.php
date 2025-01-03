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

namespace Aeliot\Bundle\DoctrineEncrypted\DependencyInjection\Compiler;

use Aeliot\DoctrineEncrypted\Query\EncryptionSQLWalker;
use Doctrine\ORM\Query;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class EncryptionSQLWalkerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $connections = $container->getParameter('aeliot.doctrine_encrypted.encrypted_connections');
        foreach ($connections as $connection) {
            $definition = $container->getDefinition(sprintf('doctrine.orm.%s_configuration', $connection));
            $definition->addMethodCall(
                'setDefaultQueryHint',
                [Query::HINT_CUSTOM_OUTPUT_WALKER, EncryptionSQLWalker::class],
            );
        }
    }
}
