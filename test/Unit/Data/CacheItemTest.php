<?php

/**
 * CacheItemTest.php
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

namespace CoffeePhp\Cache\Test\Unit\Data;

use CoffeePhp\Cache\Contract\Validation\CacheKeyValidatorInterface;
use CoffeePhp\Cache\Data\CacheItem;
use CoffeePhp\Cache\Exception\CacheKeyValidatorException;
use CoffeePhp\Cache\Test\Unit\AbstractCacheTest;
use DateInterval;
use DateTime;
use Throwable;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * Class CacheItemTest
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2021-03-27
 * @see CacheItem
 */
final class CacheItemTest extends AbstractCacheTest
{
    private CacheKeyValidatorInterface $cacheKeyValidator;

    /**
     * @before
     */
    public function setupDependencies(): void
    {
        $this->cacheKeyValidator = $this->getClass(CacheKeyValidatorInterface::class);
    }

    /**
     * @throws Throwable
     */
    public function testDataIntegrity(): void
    {
        $expiration = new DateTime();
        $cacheItem = new CacheItem($this->cacheKeyValidator, 'a', 'b', true, $expiration);
        assertSame($expiration, $cacheItem->getExpiration());
        assertSame('a', $cacheItem->getKey());
        assertSame('b', $cacheItem->get());
        assertTrue($cacheItem->isHit());

        $cacheItem = new CacheItem($this->cacheKeyValidator, 'a', 'b', false, $expiration);
        assertSame($expiration, $cacheItem->getExpiration());
        assertSame('a', $cacheItem->getKey());
        assertNull($cacheItem->get());
        assertFalse($cacheItem->isHit());
        assertSame($cacheItem, $cacheItem->set('c'));
        assertSame('c', $cacheItem->get());
        assertTrue($cacheItem->isHit());

        $cacheItem = new CacheItem($this->cacheKeyValidator, 'a', 'b', false, $expiration);
        $cacheItem->expiresAfter(2000);
        assertSame($expiration->getTimestamp() + 2000, $cacheItem->getExpiration()->getTimestamp());

        $cacheItem = new CacheItem($this->cacheKeyValidator, 'a', 'b', false, $expiration);
        $cacheItem->expiresAfter(new DateInterval('PT2000S'));
        assertSame($expiration->getTimestamp() + 2000, $cacheItem->getExpiration()->getTimestamp());

        $cacheItem = new CacheItem($this->cacheKeyValidator, 'a', 'b', false, $expiration);
        $cacheItem->expiresAfter(null);
        assertNull($cacheItem->getExpiration());

        $cacheItem = new CacheItem($this->cacheKeyValidator, 'a', 'b', false, $expiration);
        $cacheItem->expiresAt(null);
        assertNull($cacheItem->getExpiration());

        $cacheItem = new CacheItem($this->cacheKeyValidator, 'a', 'b', false, $expiration);
        $cacheItem->expiresAt(new DateTime('@' . ($expiration->getTimestamp() + 2000)));
        assertSame($expiration->getTimestamp() + 2000, $cacheItem->getExpiration()->getTimestamp());

        self::assertException(
            fn() => new CacheItem($this->cacheKeyValidator, 2, 'b', false, $expiration),
            CacheKeyValidatorException::class,
            'The given key is not a string'
        );

        self::assertException(
            fn() => new CacheItem($this->cacheKeyValidator, '', 'b', false, $expiration),
            CacheKeyValidatorException::class,
            'The given key is empty'
        );
    }
}
