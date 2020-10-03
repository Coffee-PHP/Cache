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
 * @noinspection PhpSuperClassIncompatibleWithInterfaceInspection
 */

declare(strict_types=1);

namespace CoffeePhp\Cache\Enum;

use CoffeePhp\Enum\Contract\EnumInterface;
use CoffeePhp\Enum\Util\EnumGetConstantNameByValueTrait;
use CoffeePhp\Enum\Util\EnumGetConstantValueByNameTrait;
use CoffeePhp\Enum\Util\EnumGetInstanceByConstantNameTrait;
use CoffeePhp\Enum\Util\EnumGetInstanceByConstantValueTrait;
use CoffeePhp\Enum\Util\EnumHasConstantNameTrait;
use CoffeePhp\Enum\Util\EnumHasConstantValueTrait;
use CoffeePhp\Enum\Util\EnumMagicTrait;

use function serialize;
use function unserialize;

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
final class CacheError implements EnumInterface
{
    use EnumGetConstantNameByValueTrait;
    use EnumGetConstantValueByNameTrait;
    use EnumGetInstanceByConstantValueTrait;
    use EnumGetInstanceByConstantNameTrait;
    use EnumHasConstantNameTrait;
    use EnumHasConstantValueTrait;
    use EnumMagicTrait;

    /**
     * @var string[]
     * @psalm-var array<string, string>
     * @phpstan-var array<string, string>
     */
    private const MESSAGES = [
        'GET' => 'Failed to fetch value from cache',
        'GET_MULTIPLE' => 'Failed to fetch multiple values from cache',
        'SET' => 'Failed to set value in cache',
        'SET_MULTIPLE' => 'Failed to set multiple values in cache',
        'SET_DEFERRED' => 'Failed to set a deferred value in cache',
        'DELETE' => 'Failed to delete value from cache',
        'DELETE_MULTIPLE' => 'Failed to delete multiple values from cache',
        'CLEAR' => 'Failed to clear cache',
        'HAS' => 'Failed to check for the availability of a key in cache',
        'COMMIT' => 'Failed to commit a cache transaction'
    ];

    /**
     * @var int[]|null
     * @psalm-var array<string, int>|null
     * @phpstan-var array<string, int>|null
     */
    private static ?array $constants = null;

    /**
     * @var self[]|null
     */
    private static ?array $instances = null;

    /**
     * @inheritDoc
     * @return int[]
     * @psalm-return array<string, int>
     * @phpstan-return array<string, int>
     */
    public static function getConstants(): array
    {
        if (self::$constants === null) {
            self::$constants = [
                'GET' => 32,
                'GET_MULTIPLE' => 33,
                'SET' => 64,
                'SET_MULTIPLE' => 65,
                'SET_DEFERRED' => 66,
                'DELETE' => 128,
                'DELETE_MULTIPLE' => 129,
                'CLEAR' => 256,
                'HAS' => 512,
                'COMMIT' => 1024
            ];
        }
        return self::$constants;
    }

    /**
     * @inheritDoc
     * @return self[]
     */
    public static function getInstances(): array
    {
        if (self::$instances === null) {
            $constants = self::getConstants();
            self::$instances = [
                'GET' => new self('GET', $constants['GET']),
                'GET_MULTIPLE' => new self('GET_MULTIPLE', $constants['GET_MULTIPLE']),
                'SET' => new self('SET', $constants['SET']),
                'SET_MULTIPLE' => new self('SET_MULTIPLE', $constants['SET_MULTIPLE']),
                'SET_DEFERRED' => new self('SET_DEFERRED', $constants['SET_DEFERRED']),
                'DELETE' => new self('DELETE', $constants['DELETE']),
                'DELETE_MULTIPLE' => new self('DELETE_MULTIPLE', $constants['DELETE_MULTIPLE']),
                'CLEAR' => new self('CLEAR', $constants['CLEAR']),
                'HAS' => new self('HAS', $constants['HAS']),
                'COMMIT' => new self('COMMIT', $constants['COMMIT'])
            ];
        }
        return self::$instances;
    }

    private string $key;
    private int $value;
    private string $message;

    /**
     * CacheError constructor.
     * @param string $key
     * @param int $code
     */
    public function __construct(string $key, int $code)
    {
        $this->key = $key;
        $this->value = $code;
        $this->message = self::MESSAGES[$key];
    }

    /**
     * @inheritDoc
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function serialize(): string
    {
        return serialize(
            [
                'key' => $this->key,
                'value' => $this->value,
                'message' => $this->message
            ]
        );
    }

    /**
     * @inheritDoc
     * @noinspection UnserializeExploitsInspection
     */
    public function unserialize($serialized): void
    {
        [
            'key' => $this->key,
            'value' => $this->value,
            'message' => $this->message
        ] = (array)unserialize($serialized);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'key' => $this->key,
            'value' => $this->value,
            'message' => $this->message
        ];
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return "CACHESTATE[{$this->value}]: {$this->message}";
    }
}
