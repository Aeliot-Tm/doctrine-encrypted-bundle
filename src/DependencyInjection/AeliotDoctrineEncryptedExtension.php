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

use Aeliot\Bundle\DoctrineEncrypted\Exception\RequiredPackageInstallationException;
use Aeliot\DoctrineEncrypted\Query\AST\Functions\AbstractSingleArgumentFunction;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class AeliotDoctrineEncryptedExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            'aeliot.doctrine_encrypted.encrypted_connections',
            $config['encrypted_connections'],
        );

        $loader = new YamlFileLoader($container, new FileLocator(sprintf('%s/../../config', __DIR__)));
        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig(
            'doctrine',
            [
                'dbal' => [
                    'types' => $this->getColumnTypes(),
                ],
                'orm' => [
                    'dql' => [
                        'string_functions' => $this->getFunctions(),
                    ],
                ],
            ]
        );
    }

    /**
     * @return array<string,class-string>
     */
    private function getColumnTypes(): array
    {
        $types = [];
        $directory = $this->getVendorDir() . '/aeliot/doctrine-encrypted-types/src/Types';
        foreach ($this->getPHPFileNames($directory) as $name) {
            $className = 'Aeliot\\DoctrineEncrypted\\Types\\Types\\' . $name;
            if (!is_subclass_of($className, Type::class)) {
                continue;
            }
            $types[(new $className())->getName()] = $className;
        }

        return $types;
    }

    /**
     * @return array<string,class-string>
     */
    private function getFunctions(): array
    {
        $functions = [];
        $directory = $this->getVendorDir() . '/aeliot/doctrine-encrypted-query/src/AST/Functions';
        foreach ($this->getPHPFileNames($directory) as $name) {
            $className = 'Aeliot\\DoctrineEncrypted\\Query\\AST\\Functions\\' . $name;
            if (!is_subclass_of($className, AbstractSingleArgumentFunction::class)) {
                continue;
            }

            $functions[$className::getSupportedFunctionName()] = $className;
        }

        return $functions;
    }

    /**
     * @return string[]
     */
    private function getPHPFileNames(string $directory): array
    {
        $names = [];
        foreach (new \DirectoryIterator($directory) as $file) {
            /** @var \SplFileInfo $file */
            if (!$file->isFile() || !$file->isReadable() || 'php' !== $file->getExtension()) {
                continue;
            }
            $names[] = $file->getBasename('.php');
        }

        return $names;
    }

    private function getVendorDir(): string
    {
        $dirLevelShifts = [2, 5];
        foreach ($dirLevelShifts as $dirLevelShift) {
            $rootDir = \dirname(__DIR__, $dirLevelShift);
            $vendorDir = $rootDir . '/vendor';
            if (file_exists($vendorDir) && is_dir($vendorDir)) {
                return $vendorDir;
            }
        }

        throw new RequiredPackageInstallationException('Can not find vendor dir');
    }
}
