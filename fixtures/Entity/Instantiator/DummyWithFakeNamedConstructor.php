<?php

/*
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\Entity\Instantiator;

class DummyWithFakeNamedConstructor
{
    public static function namedConstruct()
    {
        return null;
    }
}
