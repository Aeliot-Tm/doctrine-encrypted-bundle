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

namespace Aeliot\Bundle\DoctrineEncrypted\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('aeliot_doctrine_encrypted');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        $rootChildren = $rootNode->children();

        $this->configEncryptedConnections($rootChildren);

        return $treeBuilder;
    }

    private function configEncryptedConnections(NodeBuilder $rootChildren): void
    {
        $encryptionConnectionsNode = $rootChildren->arrayNode('encrypted_connections');
        $encryptionConnectionsNode
            ->beforeNormalization()
            ->ifEmpty()
            ->thenEmptyArray();
        $encryptionConnectionsNode
            ->beforeNormalization()
            ->ifString()
            ->then(static fn (string $value): array => [$value]);
        $encryptionConnectionsNode->scalarPrototype();
    }
}
