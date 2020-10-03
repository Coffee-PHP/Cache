<?php

/**
 * CacheException.php
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

namespace CoffeePhp\Cache\Exception;

use Psr\Cache\CacheException as Psr_Cache_CacheException;
use Psr\SimpleCache\CacheException as Psr_SimpleCache_CacheException;
use RuntimeException;

/**
 * Class CacheException
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-02
 */
class CacheException extends RuntimeException implements
    Psr_Cache_CacheException,
    Psr_SimpleCache_CacheException
{
}
