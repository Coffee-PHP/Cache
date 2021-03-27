<?php

/**
 * FakeBadCacheDriver2.php
 *
 * Copyright 2021 Danny Damsky
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
 * @since 2021-03-27
 */

declare(strict_types=1);

namespace CoffeePhp\Cache\Test\Fake;

use CoffeePhp\Cache\Contract\CacheDriverInterface;
use Psr\Cache\CacheItemInterface;
use RuntimeException;

/**
 * Class FakeBadCacheDriver2
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2021-03-27
 */
final class FakeBadCacheDriver2 implements CacheDriverInterface
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
        return false;
    }

    /**
     * @inheritDoc
     */
    public function deleteAll(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple(string ...$keys): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function set(CacheItemInterface $item): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function setDeferred(CacheItemInterface $item): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function commitDeferred(): bool
    {
        return false;
    }
}
