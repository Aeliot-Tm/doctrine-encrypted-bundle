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

namespace Aeliot\Bundle\DoctrineEncrypted\Service;

use Aeliot\Bundle\DoctrineEncrypted\Exception\NotSupportedPlatformException;
use Doctrine\DBAL\Connection;

class AwareFunctionProvider implements FunctionProviderInterface
{
    /**
     * @param iterable<ConnectionFunctionProviderInterface> $functionProviders
     */
    public function __construct(
        private iterable $functionProviders,
        private ConnectionNameProvider $connectionNameProvider,
    ) {
    }

    public function getDefinitions(Connection $connection): array
    {
        return $this->getFunctionProvider($connection)->getDefinitions();
    }

    public function getNames(Connection $connection): array
    {
        return array_keys($this->getFunctionProvider($connection)->getDefinitions());
    }

    private function getFunctionProvider(Connection $connection): ConnectionFunctionProviderInterface
    {
        foreach ($this->functionProviders as $functionProvider) {
            if ($functionProvider->supports($connection)) {
                return $functionProvider;
            }
        }

        try {
            $name = $this->connectionNameProvider->getConnectionName($connection);
        } catch (\Exception) {
            $name = '';
        }

        throw new NotSupportedPlatformException(sprintf('Cannot get functions of not supported connection %s.', $name));
    }
}
