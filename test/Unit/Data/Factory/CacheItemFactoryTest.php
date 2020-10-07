<?php

/**
 * CacheItemFactoryTest.php
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

use CoffeePhp\Cache\Contract\Data\CacheItemInterface;
use CoffeePhp\Cache\Data\Factory\CacheItemFactory;
use CoffeePhp\Cache\Exception\CacheInvalidArgumentException;
use CoffeePhp\Cache\Validation\CacheKeyValidator;
use DateTime;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertInstanceOf;

/**
 * Class CacheItemFactoryTest
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-03
 * @see CacheItemFactory
 */
final class CacheItemFactoryTest extends TestCase
{
    private CacheItemFactory $cacheItemFactory;

    /**
     * CacheItemFactoryTest constructor.
     * @param string|null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->cacheItemFactory = new CacheItemFactory(new CacheKeyValidator());
    }

    /**
     * @see CacheItemFactory::create()
     */
    public function testCreate(): void
    {
        assertInstanceOf(
            CacheItemInterface::class,
            $this->cacheItemFactory->create('a', 'b', true, new DateTime())
        );
        $this->expectException(CacheInvalidArgumentException::class);
        $this->cacheItemFactory->create('', 'b', true, new DateTime());
    }
}
