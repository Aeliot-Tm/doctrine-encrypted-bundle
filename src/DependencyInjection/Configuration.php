<?php

declare(strict_types=1);

namespace Aeliot\Bundle\DoctrineEncryptedField\DependencyInjection;

use Aeliot\Bundle\DoctrineEncryptedField\Service\DefaultEncryptionAvailabilityChecker;
use Aeliot\Bundle\DoctrineEncryptedField\Service\DefaultEncryptionKeyProvider;
use Aeliot\Bundle\DoctrineEncryptedField\Service\DefaultFunctionProvider;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('aeliot_doctrine_encrypted_field');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        $rootChildren = $rootNode->children();

        $this->configEncryptedConnections($rootChildren);
        $this->configEncryptionKeyProvider($rootChildren);
        $this->configEncryptionAvailabilityChecker($rootChildren);
        $this->configFunctionProvider($rootChildren);

        return $treeBuilder;
    }

    private function configEncryptionAvailabilityChecker(NodeBuilder $rootChildren): void
    {
        $rootChildren
            ->scalarNode('encryption_availability_checker')
            ->cannotBeEmpty()
            ->defaultValue(DefaultEncryptionAvailabilityChecker::class);
    }

    private function configEncryptionKeyProvider(NodeBuilder $rootChildren): void
    {
        $rootChildren
            ->scalarNode('encryption_key_provider')
            ->cannotBeEmpty()
            ->defaultValue(DefaultEncryptionKeyProvider::class);
    }

    private function configFunctionProvider(NodeBuilder $rootChildren): void
    {
        $rootChildren
            ->scalarNode('functions_provider')
            ->cannotBeEmpty()
            ->defaultValue(DefaultFunctionProvider::class);
    }

    private function configEncryptedConnections(NodeBuilder $rootChildren): void
    {
        $encryptionConnectionsNode = $rootChildren->arrayNode('encrypted_connections');
        $encryptionConnectionsNode->beforeNormalization()->ifEmpty()->thenEmptyArray();
        $encryptionConnectionsNode
            ->beforeNormalization()
            ->ifString()
            ->then(static fn (string $value): array => [$value]);
        $encryptionConnectionsNode->scalarPrototype();
        $encryptionConnectionsNode->defaultValue(['default']);
    }
}
