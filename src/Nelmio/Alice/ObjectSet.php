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
 * Value object containing the parameters and objects built from the loaded and injected ones.
 */
final class ObjectSet
{
    /**
     * @var ParameterBag
     */
    private $parameters;

    /**
     * @var ObjectBag
     */
    private $objects;

    public function __construct(ParameterBag $parameters, ObjectBag $objects)
    {
        $this->parameters = $parameters->toArray();
        $this->objects = $objects->toFlatArray();
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getObjects(): array
    {
        return $this->objects;
    }
}
