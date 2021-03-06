<?php

/*
 * This file is part of the Alice package.
 *  
 * (c) Nelmio <hello@nelm.io>
 *  
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice;

/**
 * An object is an instance (the real object) with a reference (fixture describing the instance).
 */
interface ObjectInterface
{
    /**
     * @return string
     *
     * @example
     *  'user0'
     * @TODO: rename to getId()
     */
    public function getReference(): string;

    /**
     * @return object
     */
    public function getInstance();
}
