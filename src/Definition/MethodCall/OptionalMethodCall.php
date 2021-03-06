<?php

/*
 * This file is part of the Alice package.
 *  
 * (c) Nelmio <hello@nelm.io>
 *  
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\Definition\MethodCall;

use Nelmio\Alice\Definition\Flag\OptionalFlag;
use Nelmio\Alice\Definition\MethodCallInterface;

/**
 * Represents a method call that should be called or not based on the given probability.
 */
final class OptionalMethodCall implements MethodCallInterface
{
    /**
     * @var MethodCallInterface
     */
    private $methodCall;

    /**
     * @var OptionalFlag
     */
    private $flag;

    /**
     * @param MethodCallInterface $methodCall
     * @param OptionalFlag        $flag
     */
    public function __construct(MethodCallInterface $methodCall, OptionalFlag $flag)
    {
        $this->methodCall = (null !== $caller = $methodCall->getCaller())
            ? new MethodCallWithReference($caller, $methodCall->getMethod(), $methodCall->getArguments())
            : new SimpleMethodCall($methodCall->getMethod(), $methodCall->getArguments())
        ;
        $this->flag = $flag;
    }

    /**
     * @inheritdoc
     */
    public function withArguments(array $arguments = null): self
    {
        $clone = clone $this;
        $clone->methodCall = $clone->methodCall->withArguments(deep_clone($arguments));

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function getCaller()
    {
        return deep_clone($this->methodCall->getCaller());
    }

    /**
     * @inheritdoc
     */
    public function getMethod(): string
    {
        return $this->methodCall->getMethod();
    }

    /**
     * @inheritdoc
     */
    public function getArguments()
    {
        return deep_clone($this->methodCall->getArguments());
    }

    /**
     * @return int Element of ]0;100[.
     */
    public function getPercentage(): int
    {
        return $this->flag->getPercentage();
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return $this->methodCall->__toString();
    }
}
