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

use Nelmio\Alice\Definition\MethodCallInterface;
use Nelmio\Alice\Definition\ServiceReferenceInterface;
use Nelmio\Alice\Definition\ValueInterface;

/**
 * Represents a method call for which the caller has been specified.
 */
final class MethodCallWithReference implements MethodCallInterface
{
    /**
     * @var ServiceReferenceInterface
     */
    private $caller;

    /**
     * @var string
     */
    private $method;

    /**
     * @var array
     */
    private $arguments;

    /**
     * @var string
     */
    private $stringValue;

    /**
     * @param ServiceReferenceInterface   $caller
     * @param string                      $method
     * @param ValueInterface[]|array|null $arguments
     */
    public function __construct(ServiceReferenceInterface $caller, string $method, array $arguments = null)
    {
        $this->caller = clone $caller;
        $this->method = $method;
        $this->arguments = deep_clone($arguments);
        $this->stringValue = $caller->getId().$method;
    }

    /**
     * @inheritdoc
     */
    public function withArguments(array $arguments = null): self
    {
        $clone = clone $this;
        $clone->arguments = deep_clone($arguments);

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function getCaller()
    {
        return clone $this->caller;
    }

    /**
     * @inheritdoc
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @inheritdoc
     */
    public function getArguments()
    {
        return deep_clone($this->arguments);
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return $this->stringValue;
    }
}
