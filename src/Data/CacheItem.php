<?php

/**
 * CacheItem.php
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
 * @since 2020-10-01
 */

declare(strict_types=1);

namespace CoffeePhp\Cache\Data;

use CoffeePhp\Cache\Contract\Data\CacheItemInterface;
use CoffeePhp\Cache\Contract\Validation\CacheKeyValidatorInterface;
use CoffeePhp\Cache\Exception\CacheInvalidArgumentException;
use DateInterval;
use DateTime;
use DateTimeInterface;

use function is_int;

/**
 * Class CacheItem
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-01
 */
final class CacheItem implements CacheItemInterface
{
    private string $key;

    /** @var mixed */
    private $value;

    private ?DateTimeInterface $expiration;
    private bool $isHit;

    /**
     * CacheItem constructor.
     * @param CacheKeyValidatorInterface $keyValidator
     * @param mixed $key Must be a string.
     * @param mixed $value
     * @param bool $isHit
     * @param DateTimeInterface|null $expiration
     * @throws CacheInvalidArgumentException
     */
    public function __construct(
        CacheKeyValidatorInterface $keyValidator,
        $key,
        $value,
        bool $isHit,
        ?DateTimeInterface $expiration
    ) {
        $this->key = $keyValidator->validate($key);
        $this->value = $value;
        $this->expiration = $expiration;
        $this->isHit = $isHit;
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
    public function isHit(): bool
    {
        return $this->isHit;
    }

    /**
     * @inheritDoc
     * @return static
     */
    public function set($value): self
    {
        $this->value = $value;
        $this->isHit = true;
        return $this;
    }

    /**
     * @inheritDoc
     * @param DateTimeInterface|null $expiration
     * @return static
     * @noinspection PhpMissingParamTypeInspection
     */
    public function expiresAt($expiration): self
    {
        $this->expiration = $expiration;
        return $this;
    }

    /**
     * @inheritDoc
     * @param DateInterval|int|null $time
     * @return static
     */
    public function expiresAfter($time): self
    {
        if (is_int($time)) {
            $time = new DateInterval("PT{$time}S");
        }
        $this->expiration = $time !== null ? (new DateTime())->add($time) : null;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExpiration(): ?DateTimeInterface
    {
        return $this->expiration;
    }

    /**
     * @inheritDoc
     * @return mixed
     */
    public function get()
    {
        if (!$this->isHit) {
            return null;
        }
        return $this->value;
    }
}
