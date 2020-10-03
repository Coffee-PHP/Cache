<?php

/**
 * CacheItemFactory.php
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

use CoffeePhp\Cache\Contract\Data\CacheItemInterface;
use CoffeePhp\Cache\Contract\Data\Factory\CacheItemFactoryInterface;
use CoffeePhp\Cache\Contract\Validation\CacheKeyValidatorInterface;
use CoffeePhp\Cache\Data\CacheItem;
use DateTimeInterface;

/**
 * Class CacheItemFactory
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-02
 */
final class CacheItemFactory implements CacheItemFactoryInterface
{
    private CacheKeyValidatorInterface $cacheKeyValidator;

    /**
     * CacheItemFactory constructor.
     * @param CacheKeyValidatorInterface $cacheKeyValidator
     */
    public function __construct(CacheKeyValidatorInterface $cacheKeyValidator)
    {
        $this->cacheKeyValidator = $cacheKeyValidator;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function create(
        $key,
        $value = null,
        bool $isHit = false,
        ?DateTimeInterface $expiration = null
    ): CacheItemInterface {
        return new CacheItem(
            $this->cacheKeyValidator,
            $key,
            $value,
            $isHit,
            $expiration
        );
    }
}
