<?php

/**
 * CacheComponentRegistrar.php
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

namespace CoffeePhp\Cache\Integration;

use CoffeePhp\Cache\Contract\Data\Factory\CacheItemFactoryInterface;
use CoffeePhp\Cache\Contract\Validation\CacheKeyValidatorInterface;
use CoffeePhp\Cache\Data\Factory\CacheItemFactory;
use CoffeePhp\Cache\Validation\CacheKeyValidator;
use CoffeePhp\ComponentRegistry\Contract\ComponentRegistrarInterface;
use CoffeePhp\Di\Contract\ContainerInterface;

/**
 * Class CacheComponentRegistrar
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-02
 */
final class CacheComponentRegistrar implements ComponentRegistrarInterface
{
    /**
     * CacheComponentRegistrar constructor.
     * @param ContainerInterface $di
     */
    public function __construct(private ContainerInterface $di)
    {
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->di->bind(CacheKeyValidator::class, CacheKeyValidator::class);
        $this->di->bind(CacheKeyValidatorInterface::class, CacheKeyValidator::class);

        $this->di->bind(CacheItemFactory::class, CacheItemFactory::class);
        $this->di->bind(CacheItemFactoryInterface::class, CacheItemFactory::class);
    }
}
