<?php

/**
 * AbstractCacheTest.php
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

namespace CoffeePhp\Cache\Test\Unit;

use CoffeePhp\Cache\Integration\CacheComponentRegistrar;
use CoffeePhp\Cache\Test\Fake\FakeCacheComponentRegistrar;
use CoffeePhp\ComponentRegistry\ComponentRegistry;
use CoffeePhp\Di\Container;
use CoffeePhp\QualityTools\TestCase;

/**
 * Class AbstractCacheTest
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2021-03-27
 */
abstract class AbstractCacheTest extends TestCase
{
    private Container $di;

    /**
     * @before
     */
    public function setupDi(): void
    {
        $this->di = new Container();
        $registry = new ComponentRegistry($this->di);
        $registry->register(CacheComponentRegistrar::class);
        $registry->register(FakeCacheComponentRegistrar::class);
    }

    /**
     * @template T
     * @param class-string<T> $id
     * @return T
     */
    final protected function getClass(string $id): object
    {
        return $this->di->get($id);
    }
}
