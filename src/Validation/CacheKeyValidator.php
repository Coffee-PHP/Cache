<?php

/**
 * CacheKeyValidator.php
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

namespace CoffeePhp\Cache\Validation;

use CoffeePhp\Cache\Contract\Validation\CacheKeyValidatorInterface;
use CoffeePhp\Cache\Exception\CacheKeyValidatorException;

use function is_iterable;
use function is_string;

/**
 * Class CacheKeyValidator
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-02
 */
final class CacheKeyValidator implements CacheKeyValidatorInterface
{
    /**
     * @inheritDoc
     */
    public function validate(mixed $key): string
    {
        if (!is_string($key)) {
            throw new CacheKeyValidatorException('The given key is not a string');
        }
        if (empty($key)) {
            throw new CacheKeyValidatorException('The given key is empty');
        }
        return $key;
    }

    /**
     * @inheritDoc
     */
    public function validateMultiple(mixed $keys): array
    {
        if (!is_iterable($keys)) {
            throw new CacheKeyValidatorException('The given keys are not iterable');
        }
        $validatedKeys = [];
        foreach ($keys as $key) {
            $validatedKeys[] = $this->validate($key);
        }
        return $validatedKeys;
    }
}
