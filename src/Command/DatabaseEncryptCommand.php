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

namespace Aeliot\Bundle\DoctrineEncrypted\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'doctrine-encrypted:database:encrypt')]
final class DatabaseEncryptCommand extends DatabaseTransformCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Encrypt database');
    }

    protected function transform(string $managerName, OutputInterface $output): void
    {
        $this->encryptionService->encrypt($managerName, $output);
    }
}
