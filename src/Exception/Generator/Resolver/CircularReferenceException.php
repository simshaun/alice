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

use Nelmio\Alice\Throwable\ResolutionThrowable;

class CircularReferenceException extends \RuntimeException implements ResolutionThrowable
{
    public static function createForParameter(string $key, array $resolving)
    {
        return new static(
            sprintf(
                'Circular reference detected for the parameter "%s" while resolving ["%s"].',
                $key,
                implode('", "', array_keys($resolving))
            )
        );
    }
}
