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

#[AsCommand(name: 'doctrine-encrypted:fields:decrypt')]
final class FieldsDecryptCommand extends FieldsTransformCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Decrypt fields');
    }

    protected function getFunction(): string
    {
        return $this->functionNameProvider->getDecryptFunctionName();
    }
}
