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

use CoffeePhp\Cache\Enum\CacheError;
use Psr\Cache\CacheException as Psr6CacheException;
use Psr\SimpleCache\CacheException as Psr16CacheException;
use RuntimeException;
use Throwable;

/**
 * Class CacheException
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-02
 */
class CacheException extends RuntimeException implements Psr6CacheException, Psr16CacheException
{
    /**
     * CacheException constructor.
     * @param CacheError $error
     * @param Throwable|null $previous
     */
    public function __construct(CacheError $error, ?Throwable $previous = null)
    {
        parent::__construct(
            $error->getMessage() . ($previous !== null ? " ; {$previous->getMessage()}" : ''),
            $error->value,
            $previous
        );
    }
}
