<?php

declare(strict_types=1);

/*
 * This file is part of the TransMaintain.
 *
 * (c) Anatoliy Melnikov <5785276@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Aeliot\Bundle\DoctrineEncrypted\Tests\App;

use Aeliot\Bundle\DoctrineEncrypted\AeliotDoctrineEncryptedBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;

final class Kernel extends SymfonyKernel
{
    private const CONFIG_EXTENSIONS = '.{php,xml,yaml,yml}';

    private string $cacheDir;
    private string $logDir;

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new AeliotDoctrineEncryptedBundle(),
        ];
    }

    public function getRootDir(): string
    {
        return __DIR__;
    }

    public function getCacheDir(): string
    {
        if (!isset($this->cacheDir)) {
            $this->cacheDir = \dirname($this->getProjectDir()) . '/var/cache/' . $this->environment;
        }

        return $this->cacheDir;
    }

    public function getLogDir(): string
    {
        if (!isset($this->logDir)) {
            $this->logDir = \dirname($this->getProjectDir()) . '/var/log';
        }

        return $this->logDir;
    }

    public function getProjectDir(): string
    {
        return parent::getProjectDir() . '/app';
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load($this->getProjectDir() . '/config/packages/*' . self::CONFIG_EXTENSIONS, 'glob');
        $loader->load($this->getProjectDir() . '/config/services' . self::CONFIG_EXTENSIONS, 'glob');
    }
}
