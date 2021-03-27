<?php

/**
 * CacheError.php
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

namespace CoffeePhp\Cache\Enum;

use CoffeePhp\Enum\AbstractIntEnum;

/**
 * Class CacheError
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-02
 * @method static static GET() Failed to fetch value from cache
 * @method static static GET_MULTIPLE() Failed to fetch multiple values from cache
 * @method static static SET() Failed to set value in cache
 * @method static static SET_MULTIPLE() Failed to set multiple values in cache
 * @method static static SET_DEFERRED() Failed to set a deferred value in cache
 * @method static static DELETE() Failed to delete value from cache
 * @method static static DELETE_MULTIPLE() Failed to delete multiple values from cache
 * @method static static CLEAR() Failed to clear cache
 * @method static static HAS() Failed to check for the availability of a key in cache
 * @method static static COMMIT() Failed to commit a cache transaction
 */
final class CacheError extends AbstractIntEnum
{
    /**
     * @inheritDoc
     */
    protected static function getConstants(): array
    {
        return [
            'GET' => 32,
            'GET_MULTIPLE' => 33,
            'SET' => 64,
            'SET_MULTIPLE' => 65,
            'SET_DEFERRED' => 66,
            'DELETE' => 128,
            'DELETE_MULTIPLE' => 129,
            'CLEAR' => 256,
            'HAS' => 512,
            'COMMIT' => 1024,
        ];
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return match ($this->name) {
            'GET' => 'CACHESTATE[32]: Failed to fetch a value from cache',
            'GET_MULTIPLE' => 'CACHESTATE[33]: Failed to fetch multiple values from cache',
            'SET' => 'CACHESTATE[64]: Failed to set a value in cache',
            'SET_MULTIPLE' => 'CACHESTATE[65]: Failed to set multiple values in cache',
            'SET_DEFERRED' => 'CACHESTATE[66]: Failed to set a deferred value in cache',
            'DELETE' => 'CACHESTATE[128]: Failed to delete a value from cache',
            'DELETE_MULTIPLE' => 'CACHESTATE[129]: Failed to delete multiple values from cache',
            'CLEAR' => 'CACHESTATE[256]: Failed to clear cache',
            'HAS' => 'CACHESTATE[512]: Failed to check for the availability of a key in cache',
            'COMMIT' => 'CACHESTATE[1024]: Failed to commit a cache transaction'
        };
    }
}
