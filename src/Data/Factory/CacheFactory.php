<?php

/**
 * CacheFactory.php
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
 * @since 2020-10-02
 */

declare(strict_types=1);

namespace CoffeePhp\Cache\Data\Factory;

use CoffeePhp\Cache\Contract\Data\Factory\CacheFactoryInterface;
use CoffeePhp\Cache\Contract\Data\Factory\CacheItemFactoryInterface;
use CoffeePhp\Cache\Contract\Validation\CacheKeyValidatorInterface;
use CoffeePhp\Cache\Data\Cache;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class CacheFactory
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-02
 */
final class CacheFactory implements CacheFactoryInterface
{
    /**
     * CacheFactory constructor.
     * @param CacheItemFactoryInterface $itemFactory
     * @param CacheKeyValidatorInterface $keyValidator
     */
    public function __construct(
        private CacheItemFactoryInterface $itemFactory,
        private CacheKeyValidatorInterface $keyValidator
    ) {
    }

    /**
     * @inheritDoc
     */
    public function create(CacheItemPoolInterface $cacheItemPool): Cache
    {
        return new Cache($this->itemFactory, $cacheItemPool, $this->keyValidator);
    }
}
