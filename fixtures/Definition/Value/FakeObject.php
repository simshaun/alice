<?php

/*
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\Definition\Value;

use Nelmio\Alice\NotCallableTrait;
use Nelmio\Alice\ObjectInterface;

class FakeObject implements ObjectInterface
{
    use NotCallableTrait;

    /**
     * @inheritdoc
     */
    public function getReference(): string
    {
        $this->__call(__METHOD__, func_get_args());
    }

    /**
     * @inheritdoc
     */
    public function getInstance()
    {
        $this->__call(__METHOD__, func_get_args());
    }
}
