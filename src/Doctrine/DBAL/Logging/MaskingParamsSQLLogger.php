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

namespace Aeliot\Bundle\DoctrineEncrypted\Doctrine\DBAL\Logging;

use Doctrine\DBAL\Logging\SQLLogger;

final class MaskingParamsSQLLogger implements SQLLogger
{
    /**
     * @param string[] $maskedParams
     */
    public function __construct(private SQLLogger $decorated, private array $maskedParams)
    {
    }

    public function startQuery($sql, ?array $params = null, ?array $types = null): void
    {
        $this->decorated->startQuery($sql, $this->maskParams($params), $types);
    }

    public function stopQuery(): void
    {
        $this->decorated->stopQuery();
    }

    /**
     * @param list<mixed>|array<string, mixed>|null $params Statement parameters
     *
     * @return list<mixed>|array<string, mixed>|null
     */
    private function maskParams(?array $params): ?array
    {
        if (\is_array($params)) {
            foreach ($this->maskedParams as $param) {
                if (\array_key_exists($param, $params)) {
                    $params[$param] = sprintf('<masked:%d>', \strlen($params[$param]));
                }
            }
        }

        return $params;
    }
}
