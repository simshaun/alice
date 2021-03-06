<?php

/*
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\Exception\Generator\Resolver;

use Nelmio\Alice\Definition\Value\UniqueValue;
use Nelmio\Alice\Throwable\ResolutionThrowable;

/**
 * @covers Nelmio\Alice\Exception\Generator\Resolver\UniqueValueGenerationLimitReachedException
 */
class UniqueValueGenerationLimitReachedExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testIsARuntimeException()
    {
        $this->assertTrue(is_a(UniqueValueGenerationLimitReachedException::class, \RuntimeException::class, true));
    }

    public function testIsAResolutionThrowable()
    {
        $this->assertTrue(is_a(UniqueValueGenerationLimitReachedException::class, ResolutionThrowable::class, true));
    }

    public function testTestCreateNewExceptionWithFactory()
    {
        $exception = UniqueValueGenerationLimitReachedException::create(
            new UniqueValue('unique_id', new \stdClass()),
            10
        );

        $this->assertEquals(
            'Could not generate a unique value after 10 attempts for "unique_id".',
            $exception->getMessage()
        );
    }
}
