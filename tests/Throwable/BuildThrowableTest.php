<?php

/*
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\Throwable;

use Nelmio\Alice\Exception\RootBuildException;

/**
 * @covers Nelmio\Alice\Throwable\BuildThrowable
 */
class BuildThrowableTest extends \PHPUnit_Framework_TestCase
{
    public function testIsABuildThrowable()
    {
        $this->assertTrue(is_a(RootBuildException::class, LoadingThrowable::class, true));
    }
}
