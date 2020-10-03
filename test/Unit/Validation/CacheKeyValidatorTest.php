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

use CoffeePhp\Cache\Exception\CacheInvalidArgumentException;
use CoffeePhp\Cache\Validation\CacheKeyValidator;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * Class CacheKeyValidatorTest
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-03
 * @see CacheKeyValidator
 */
final class CacheKeyValidatorTest extends TestCase
{
    private CacheKeyValidator $cacheKeyValidator;

    /**
     * CacheKeyValidatorTest constructor.
     * @param string|null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->cacheKeyValidator = new CacheKeyValidator();
    }

    /**
     * @see CacheKeyValidator::validate()
     */
    public function testValidate(): void
    {
        try {
            $this->cacheKeyValidator->validate(2);
            assertTrue(false);
        } catch (CacheInvalidArgumentException $e) {
            assertSame('The given key is not a string.', $e->getMessage());
        }
        try {
            $this->cacheKeyValidator->validate('');
            assertTrue(false);
        } catch (CacheInvalidArgumentException $e) {
            assertSame('The given key is empty.', $e->getMessage());
        }
        assertSame(
            'abc',
            $this->cacheKeyValidator->validate('abc')
        );
    }

    /**
     * @see CacheKeyValidator::validateMultiple()
     */
    public function testValidateMultiple(): void
    {
        try {
            $this->cacheKeyValidator->validateMultiple(['abc', 2, 'test']);
            assertTrue(false);
        } catch (CacheInvalidArgumentException $e) {
            assertSame('The given key is not a string.', $e->getMessage());
        }
        try {
            $this->cacheKeyValidator->validateMultiple(['abc', '', 'test']);
            assertTrue(false);
        } catch (CacheInvalidArgumentException $e) {
            assertSame('The given key is empty.', $e->getMessage());
        }
        assertSame(
            ['abc', 'test2', 'test'],
            $this->cacheKeyValidator->validateMultiple(['abc', 'test2', 'test'])
        );
    }
}
