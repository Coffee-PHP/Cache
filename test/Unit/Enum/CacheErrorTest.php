<?php

/**
 * CacheErrorTest.php
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

namespace CoffeePhp\Cache\Test\Unit\Enum;

use CoffeePhp\Cache\Enum\CacheError;
use CoffeePhp\QualityTools\TestCase;

use function PHPUnit\Framework\assertSame;

/**
 * Class CacheErrorTest
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-03
 * @see CacheError
 */
final class CacheErrorTest extends TestCase
{
    public function testDataIntegrity(): void
    {
        assertSame('GET', CacheError::GET()->name);
        assertSame(32, CacheError::GET()->value);
        assertSame('CACHESTATE[32]: Failed to fetch a value from cache', CacheError::GET()->getMessage());

        assertSame('GET_MULTIPLE', CacheError::GET_MULTIPLE()->name);
        assertSame(33, CacheError::GET_MULTIPLE()->value);
        assertSame(
            'CACHESTATE[33]: Failed to fetch multiple values from cache',
            CacheError::GET_MULTIPLE()->getMessage()
        );

        assertSame('SET', CacheError::SET()->name);
        assertSame(64, CacheError::SET()->value);
        assertSame('CACHESTATE[64]: Failed to set a value in cache', CacheError::SET()->getMessage());

        assertSame('SET_MULTIPLE', CacheError::SET_MULTIPLE()->name);
        assertSame(65, CacheError::SET_MULTIPLE()->value);
        assertSame('CACHESTATE[65]: Failed to set multiple values in cache', CacheError::SET_MULTIPLE()->getMessage());

        assertSame('SET_DEFERRED', CacheError::SET_DEFERRED()->name);
        assertSame(66, CacheError::SET_DEFERRED()->value);
        assertSame('CACHESTATE[66]: Failed to set a deferred value in cache', CacheError::SET_DEFERRED()->getMessage());

        assertSame('DELETE', CacheError::DELETE()->name);
        assertSame(128, CacheError::DELETE()->value);
        assertSame('CACHESTATE[128]: Failed to delete a value from cache', CacheError::DELETE()->getMessage());

        assertSame('DELETE_MULTIPLE', CacheError::DELETE_MULTIPLE()->name);
        assertSame(129, CacheError::DELETE_MULTIPLE()->value);
        assertSame(
            'CACHESTATE[129]: Failed to delete multiple values from cache',
            CacheError::DELETE_MULTIPLE()->getMessage()
        );

        assertSame('CLEAR', CacheError::CLEAR()->name);
        assertSame(256, CacheError::CLEAR()->value);
        assertSame(
            'CACHESTATE[256]: Failed to clear cache',
            CacheError::CLEAR()->getMessage()
        );

        assertSame('HAS', CacheError::HAS()->name);
        assertSame(512, CacheError::HAS()->value);
        assertSame(
            'CACHESTATE[512]: Failed to check for the availability of a key in cache',
            CacheError::HAS()->getMessage()
        );

        assertSame('COMMIT', CacheError::COMMIT()->name);
        assertSame(1024, CacheError::COMMIT()->value);
        assertSame(
            'CACHESTATE[1024]: Failed to commit a cache transaction',
            CacheError::COMMIT()->getMessage()
        );
    }
}
