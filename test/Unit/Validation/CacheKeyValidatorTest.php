<?php

/**
 * CacheKeyValidatorTest.php
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

namespace CoffeePhp\Cache\Test\Unit\Validation;

use CoffeePhp\Cache\Exception\CacheKeyValidatorException;
use CoffeePhp\Cache\Test\Unit\AbstractCacheTest;
use CoffeePhp\Cache\Validation\CacheKeyValidator;

use function PHPUnit\Framework\assertSame;

/**
 * Class CacheKeyValidatorTest
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-03
 * @see CacheKeyValidator
 */
final class CacheKeyValidatorTest extends AbstractCacheTest
{
    private CacheKeyValidator $cacheKeyValidator;

    /**
     * @before
     */
    public function setupDependencies(): void
    {
        $this->cacheKeyValidator = $this->getClass(CacheKeyValidator::class);
    }

    /**
     * @see CacheKeyValidator::validate()
     */
    public function testValidate(): void
    {
        self::assertException(
            fn() => $this->cacheKeyValidator->validate(2),
            CacheKeyValidatorException::class,
            'The given key is not a string'
        );
        self::assertException(
            fn() => $this->cacheKeyValidator->validate(''),
            CacheKeyValidatorException::class,
            'The given key is empty'
        );
        assertSame('abc', $this->cacheKeyValidator->validate('abc'));
    }

    /**
     * @see CacheKeyValidator::validateMultiple()
     */
    public function testValidateMultiple(): void
    {
        self::assertException(
            fn() => $this->cacheKeyValidator->validateMultiple(2),
            CacheKeyValidatorException::class,
            'The given keys are not iterable'
        );
        self::assertException(
            fn() => $this->cacheKeyValidator->validateMultiple(['abc', 2, 'test']),
            CacheKeyValidatorException::class,
            'The given key is not a string'
        );
        self::assertException(
            fn() => $this->cacheKeyValidator->validateMultiple(['abc', '', 'test']),
            CacheKeyValidatorException::class,
            'The given key is empty'
        );
        assertSame(
            ['abc', 'test2', 'test'],
            $this->cacheKeyValidator->validateMultiple(['abc', 'test2', 'test'])
        );
    }
}
