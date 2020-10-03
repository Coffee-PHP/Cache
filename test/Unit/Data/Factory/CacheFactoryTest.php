<?php

/**
 * CacheFactoryTest.php
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

namespace CoffeePhp\Cache\Test\Unit\Data\Factory;

use CoffeePhp\Cache\Contract\Data\CacheInterface;
use CoffeePhp\Cache\Contract\Data\CacheItemPoolInterface;
use CoffeePhp\Cache\Data\Factory\CacheFactory;
use CoffeePhp\Cache\Data\Factory\CacheItemFactory;
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
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertInstanceOf;

/**
 * Class CacheFactoryTest
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-03
 * @see CacheFactory
 */
final class CacheFactoryTest extends TestCase
{
    private CacheFactory $cacheFactory;
    private CacheItemPoolInterface $cacheItemPool;

    /**
     * CacheFactoryTest constructor.
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
        $this->cacheFactory = new CacheFactory($cacheItemFactory, $logger);
        $this->cacheItemPool = new MockCacheItemPool($cacheItemFactory, $keyValidator, $logger);
    }

    /**
     * @see CacheFactory::create()
     */
    public function testCreate(): void
    {
        assertInstanceOf(
            CacheInterface::class,
            $this->cacheFactory->create($this->cacheItemPool)
        );
    }
}
