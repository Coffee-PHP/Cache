<?php

/**
 * CacheItemInterface.php
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

namespace CoffeePhp\Cache\Contract\Data;

use DateTimeInterface;
use JsonSerializable;
use Psr\Cache\CacheItemInterface as Psr_Cache_CacheItemInterfaceAlias;
use Serializable;

/**
 * Interface CacheItemInterface
 * @package coffeephp\cache
 * @author Danny Damsky <dannydamsky99@gmail.com>
 * @since 2020-10-01
 */
interface CacheItemInterface extends Psr_Cache_CacheItemInterfaceAlias, Serializable, JsonSerializable
{
    /**
     * @inheritDoc
     */
    public function getKey(): string;

    /**
     * @inheritDoc
     */
    public function isHit(): bool;

    /**
     * @inheritDoc
     * @return static
     */
    public function set($value): self;

    /**
     * @inheritDoc
     * @return static
     */
    public function expiresAt($expiration): self;

    /**
     * @inheritDoc
     * @return static
     */
    public function expiresAfter($time): self;

    /**
     * Get the expiration date of the cache item.
     *
     * @return DateTimeInterface
     */
    public function getExpiration(): ?DateTimeInterface;

    /**
     * Convert the cache item object to a string.
     *
     * @return string
     */
    public function __toString(): string;
}
