<?php

/**
 * FakeBadCacheDriver.php
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
 * @since 2020-10-03
 */

declare(strict_types=1);

namespace CoffeePhp\Cache\Test\Fake;

use CoffeePhp\Cache\Contract\CacheDriverInterface;
use Psr\Cache\CacheItemInterface;
use RuntimeException;

/**
 * Class FakeBadCacheDriver
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-03
 */
final class FakeBadCacheDriver implements CacheDriverInterface
{
    /**
     * @inheritDoc
     */
    public function get(string $key): CacheItemInterface
    {
        throw new RuntimeException('test get');
    }

    /**
     * @inheritDoc
     */
    public function getMultiple(string ...$keys): iterable
    {
        throw new RuntimeException('test get multiple');
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        throw new RuntimeException('test has');
    }

    /**
     * @inheritDoc
     */
    public function deleteAll(): bool
    {
        throw new RuntimeException('test delete all');
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): bool
    {
        throw new RuntimeException('test delete');
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple(string ...$keys): bool
    {
        throw new RuntimeException('test delete multiple');
    }

    /**
     * @inheritDoc
     */
    public function set(CacheItemInterface $item): bool
    {
        throw new RuntimeException('test set');
    }

    /**
     * @inheritDoc
     */
    public function setDeferred(CacheItemInterface $item): bool
    {
        throw new RuntimeException('test set deferred');
    }

    /**
     * @inheritDoc
     */
    public function commitDeferred(): bool
    {
        throw new RuntimeException('test commit deferred');
    }
}
