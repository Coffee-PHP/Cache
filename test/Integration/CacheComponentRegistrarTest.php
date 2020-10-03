<?php

/**
 * CacheComponentRegistrarTest.php
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

namespace CoffeePhp\Cache\Test\Integration;

use CoffeePhp\Cache\Contract\Data\Factory\CacheFactoryInterface;
use CoffeePhp\Cache\Contract\Data\Factory\CacheItemFactoryInterface;
use CoffeePhp\Cache\Contract\Validation\CacheKeyValidatorInterface;
use CoffeePhp\Cache\Data\Factory\CacheFactory;
use CoffeePhp\Cache\Data\Factory\CacheItemFactory;
use CoffeePhp\Cache\Integration\CacheComponentRegistrar;
use CoffeePhp\Cache\Validation\CacheKeyValidator;
use CoffeePhp\Di\Container;
use CoffeePhp\Event\Integration\EventComponentRegistrar;
use CoffeePhp\Json\Integration\JsonComponentRegistrar;
use CoffeePhp\Log\Integration\LogComponentRegistrar;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * Class CacheComponentRegistrarTest
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-03
 * @see CacheComponentRegistrar
 */
final class CacheComponentRegistrarTest extends TestCase
{
    /**
     * @see CacheComponentRegistrar::register()
     */
    public function testRegister(): void
    {
        $di = new Container();

        $jsonRegistrar = new JsonComponentRegistrar();
        $jsonRegistrar->register($di);

        $eventRegistrar = new EventComponentRegistrar();
        $eventRegistrar->register($di);

        $logRegistrar = new LogComponentRegistrar();
        $logRegistrar->register($di);

        $registrar = new CacheComponentRegistrar();
        $registrar->register($di);

        assertTrue($di->has(CacheKeyValidatorInterface::class));
        assertTrue($di->has(CacheFactoryInterface::class));
        assertTrue($di->has(CacheItemFactoryInterface::class));

        assertInstanceOf(
            CacheKeyValidatorInterface::class,
            $di->get(CacheKeyValidatorInterface::class)
        );
        assertSame(
            $di->get(CacheKeyValidatorInterface::class),
            $di->get(CacheKeyValidator::class)
        );

        assertInstanceOf(
            CacheFactoryInterface::class,
            $di->get(CacheFactoryInterface::class)
        );
        assertSame(
            $di->get(CacheFactoryInterface::class),
            $di->get(CacheFactory::class)
        );

        assertInstanceOf(
            CacheItemFactoryInterface::class,
            $di->get(CacheItemFactoryInterface::class)
        );
        assertSame(
            $di->get(CacheItemFactoryInterface::class),
            $di->get(CacheItemFactory::class)
        );
    }
}
