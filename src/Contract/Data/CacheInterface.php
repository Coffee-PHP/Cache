<?php

/**
 * CacheInterface.php
 *
 * Copyright 2020 Danny Damsky
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-09-30
 */

declare(strict_types=1);

namespace CoffeePhp\Cache\Contract\Data;

use Psr\SimpleCache\CacheInterface as Psr_SimpleCache_CacheInterface;

/**
 * Interface CacheInterface
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-09-30
 */
interface CacheInterface extends Psr_SimpleCache_CacheInterface
{
    /**
     * @inheritDoc
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null): bool;

    /**
     * @inheritDoc
     */
    public function delete($key): bool;

    /**
     * @inheritDoc
     */
    public function clear(): bool;

    /**
     * @inheritDoc
     * @param iterable|string[] $keys
     * @psalm-param iterable<string> $keys
     * @phpstan-param iterable<string> $keys
     * @psalm-return iterable<string, mixed>
     * @phpstan-return iterable<string, mixed>
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function getMultiple($keys, $default = null): iterable;

    /**
     * @inheritDoc
     * @param iterable $values
     * @psalm-param iterable<string, mixed> $values
     * @phpstan-param iterable<string, mixed> $values
     * @psalm-suppress MoreSpecificImplementedParamType
     * @noinspection PhpMissingParamTypeInspection
     */
    public function setMultiple($values, $ttl = null): bool;

    /**
     * @inheritDoc
     * @param iterable|string[] $keys
     * @psalm-param iterable<string> $keys
     * @phpstan-param iterable<string> $keys
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function deleteMultiple($keys): bool;

    /**
     * @inheritdoc
     */
    public function has($key): bool;
}
