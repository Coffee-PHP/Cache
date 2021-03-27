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
 * @noinspection PhpUnnecessaryStaticReferenceInspection
 */

declare(strict_types=1);

namespace CoffeePhp\Cache\Data;

use CoffeePhp\Cache\Contract\Validation\CacheKeyValidatorInterface;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Psr\Cache\CacheItemInterface;

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

    /**
     * CacheItem constructor.
     * @param CacheKeyValidatorInterface $keyValidator
     * @param mixed $key
     * @param mixed $value
     * @param bool $isHit
     * @param DateTimeInterface|null $expiration
     */
    public function __construct(
        private CacheKeyValidatorInterface $keyValidator,
        mixed $key,
        private mixed $value,
        private bool $isHit,
        private ?DateTimeInterface $expiration
    ) {
        $this->key = $this->keyValidator->validate($key);
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
    public function get(): mixed
    {
        if (!$this->isHit) {
            return null;
        }
        return $this->value;
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
     */
    public function set(mixed $value): static
    {
        $this->value = $value;
        $this->isHit = true;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function expiresAt(?DateTimeInterface $expiration): static
    {
        $this->expiration = $expiration;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function expiresAfter(DateInterval|int|null $time): static
    {
        if ($time === null) {
            $this->expiration = null;
            return $this;
        }
        if (is_int($time)) {
            $this->expiration = (new DateTime())->add(new DateInterval("PT{$time}S"));
            return $this;
        }
        $this->expiration = (new DateTime())->add($time);
        return $this;
    }

    /**
     * Get the expiration date of this cache item.
     *
     * @return DateTimeInterface|null
     */
    public function getExpiration(): ?DateTimeInterface
    {
        return $this->expiration;
    }
}
