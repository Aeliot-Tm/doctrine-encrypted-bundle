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

namespace Aeliot\Bundle\DoctrineEncrypted\Tests;

use Aeliot\Bundle\DoctrineEncrypted\AeliotDoctrineEncryptedBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;

final class Kernel extends SymfonyKernel
{
    private const CONFIG_EXTENSIONS = '.{php,xml,yaml,yml}';

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new AeliotDoctrineEncryptedBundle(),
        ];
    }

    public function getRootDir(): string
    {
        return __DIR__;
    }

    public function getCacheDir(): string
    {
        return \dirname($this->getProjectDir()).'/var/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        return \dirname($this->getProjectDir()).'/var/log';
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/packages/*'.self::CONFIG_EXTENSIONS, 'glob');
        $loader->load(__DIR__.'/config/services'.self::CONFIG_EXTENSIONS, 'glob');
    }

    protected function getContainerClass(): string
    {
        return parent::getContainerClass().'Tmp';
    }
}
