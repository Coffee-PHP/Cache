<?php

/**
 * CacheTest.php
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

namespace CoffeePhp\Cache\Test\Unit\Data;

use CoffeePhp\Cache\Contract\Data\CacheInterface;
use CoffeePhp\Cache\Data\Cache;
use CoffeePhp\Cache\Data\Factory\CacheFactory;
use CoffeePhp\Cache\Data\Factory\CacheItemFactory;
use CoffeePhp\Cache\Exception\CacheInvalidArgumentException;
use CoffeePhp\Cache\Test\Mock\MockCacheItemPool;
use CoffeePhp\Cache\Validation\CacheKeyValidator;
use CoffeePhp\Event\Data\EventListenerMap;
use CoffeePhp\Event\EventManager;
use CoffeePhp\Event\Handling\EventDispatcher;
use CoffeePhp\Event\Handling\ListenerProvider;
use CoffeePhp\Json\JsonTranslator;
use CoffeePhp\Log\Data\LogMessageFactory;
use CoffeePhp\Log\Event\LogEvent;
use CoffeePhp\Log\Formatting\StringLogFormatter;
use CoffeePhp\Log\Logger;
use CoffeePhp\Log\Output\StandardOutputLogWriter;
use CoffeePhp\Log\PsrLogger;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\InvalidArgumentException;

use function array_fill_keys;
use function array_keys;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * Class CacheTest
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-03
 * @see Cache
 */
final class CacheTest extends TestCase
{
    private array $fakeCache = [];
    private CacheInterface $cache;
    private Generator $faker;

    /**
     * CacheTest constructor.
     * @param string|null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $keyValidator = new CacheKeyValidator();
        $cacheItemFactory = new CacheItemFactory($keyValidator);
        $logger = new PsrLogger(
            new Logger(
                new EventManager(
                    new EventDispatcher(
                        new ListenerProvider(
                            new EventListenerMap()
                        )
                    ),
                    new EventListenerMap(),
                    new ListenerProvider(
                        new EventListenerMap()
                    )
                ),
                new LogEvent(),
                new StandardOutputLogWriter(
                    new StringLogFormatter(
                        new JsonTranslator(),
                        'c'
                    )
                )
            ),
            new LogMessageFactory()
        );
        $cacheFactory = new CacheFactory($cacheItemFactory, $logger);
        $this->cache = $cacheFactory->create(new MockCacheItemPool($cacheItemFactory, $keyValidator, $logger));
        $this->faker = Factory::create();
        for ($i = 0; $i < 50; ++$i) {
            $this->fakeCache[$this->faker->uuid] = $this->faker->paragraph(50);
        }
    }

    /**
     * @inheritDoc
     * @throws InvalidArgumentException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->cache->clear();
        $this->cache->setMultiple($this->fakeCache);
    }

    /**
     * @throws InvalidArgumentException
     * @see Cache::get()
     */
    public function testGet(): void
    {
        foreach ($this->fakeCache as $key => $value) {
            assertSame(
                $value,
                $this->cache->get($key)
            );
        }
        $this->expectException(CacheInvalidArgumentException::class);
        $this->cache->get(2);
    }

    /**
     * @throws InvalidArgumentException
     * @see Cache::set()
     */
    public function testSet(): void
    {
        foreach ($this->fakeCache as $key => $value) {
            assertSame(
                $value,
                $this->cache->get($key)
            );
            $newValue = $this->faker->paragraph(50);
            assertNotSame($value, $newValue);
            assertTrue($this->cache->set($key, $newValue));
            assertSame(
                $newValue,
                $this->cache->get($key)
            );
        }
        $this->expectException(CacheInvalidArgumentException::class);
        $this->cache->set(2, $this->faker->paragraph);
    }

    /**
     * @throws InvalidArgumentException
     * @see Cache::delete()
     */
    public function testDelete(): void
    {
        foreach (array_keys($this->fakeCache) as $key) {
            assertTrue($this->cache->has($key));
            assertTrue($this->cache->delete($key));
            assertFalse($this->cache->has($key));
        }
        $this->expectException(CacheInvalidArgumentException::class);
        $this->cache->delete(2);
    }

    /**
     * @throws InvalidArgumentException
     * @see Cache::clear()
     */
    public function testClear(): void
    {
        assertTrue($this->cache->clear());
        foreach (array_keys($this->fakeCache) as $key) {
            assertFalse($this->cache->has($key));
        }
    }

    /**
     * @throws InvalidArgumentException
     * @see Cache::getMultiple()
     */
    public function testGetMultiple(): void
    {
        $fromCache = $this->cache->getMultiple(array_keys($this->fakeCache));
        foreach ($fromCache as $key => $value) {
            assertSame(
                $value,
                $this->fakeCache[$key]
            );
        }
        try {
            $this->cache->getMultiple(['a', 5, '']);
            assertTrue(false);
        } catch (CacheInvalidArgumentException $e) {
            assertTrue(true);
        }
    }

    /**
     * @throws InvalidArgumentException
     * @see Cache::setMultiple()
     */
    public function testSetMultiple(): void
    {
        foreach ($this->fakeCache as $key => $value) {
            assertSame(
                $value,
                $this->cache->get($key)
            );
        }
        $override = $this->faker->paragraph(50);
        $keys = array_keys($this->fakeCache);
        $result = array_fill_keys($keys, $override);
        assertTrue($this->cache->setMultiple($result));
        foreach ($this->fakeCache as $key => $value) {
            assertSame(
                $override,
                $this->cache->get($key)
            );
        }
        try {
            $this->cache->setMultiple(['a' => 'b', 5 => 'c', '' => 'd']);
            assertTrue(false);
        } catch (CacheInvalidArgumentException $e) {
            assertTrue(true);
        }
    }

    /**
     * @throws InvalidArgumentException
     * @see Cache::deleteMultiple()
     */
    public function testDeleteMultiple(): void
    {
        foreach ($this->fakeCache as $key => $value) {
            assertTrue($this->cache->has($key));
        }
        assertTrue($this->cache->deleteMultiple(array_keys($this->fakeCache)));
        foreach ($this->fakeCache as $key => $value) {
            assertFalse($this->cache->has($key));
        }
        try {
            $this->cache->deleteMultiple(['a', 5, '']);
            assertTrue(false);
        } catch (CacheInvalidArgumentException $e) {
            assertTrue(true);
        }
    }

    /**
     * @throws InvalidArgumentException
     * @see Cache::has()
     */
    public function testHas(): void
    {
        foreach (array_keys($this->fakeCache) as $key) {
            assertTrue($this->cache->has($key));
        }
        $this->expectException(CacheInvalidArgumentException::class);
        $this->cache->has(2);
    }
}
