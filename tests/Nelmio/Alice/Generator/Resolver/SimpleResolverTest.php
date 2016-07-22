<?php

/*
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\Generator\Resolver;

use Nelmio\Alice\FixtureBag;
use Nelmio\Alice\FixtureInterface;
use Nelmio\Alice\FixtureSet;
use Nelmio\Alice\Generator\ResolvedFixtureSet;
use Nelmio\Alice\Generator\FixtureSetResolverInterface;
use Nelmio\Alice\ObjectBag;
use Nelmio\Alice\ParameterBag;
use Prophecy\Argument;

/**
 * @covers Nelmio\Alice\Generator\Resolver\SimpleFixtureSetResolver
 */
class SimpleResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testIsAResolver()
    {
        $this->assertTrue(is_a(SimpleFixtureSetResolver::class, FixtureSetResolverInterface::class, true));
    }

    public function testCanResolveAFixtureSet()
    {
        $unresolvedFixtureProphecy = $this->prophesize(FixtureInterface::class);
        $unresolvedFixtureProphecy->getId()->willReturn('Nelmio\Entity\User#user1');
        /** @var FixtureInterface $unresolvedFixture */
        $unresolvedFixture = $unresolvedFixtureProphecy->reveal();

        $resolvedFixtureProphecy = $this->prophesize(FixtureInterface::class);
        $resolvedFixtureProphecy->getId()->willReturn('Nelmio\Entity\User#user1');
        /** @var FixtureInterface $resolvedFixture */
        $resolvedFixture = $resolvedFixtureProphecy->reveal();

        $loadedParameters = new ParameterBag(['foo' => 'bar']);
        $injectedParameters = new ParameterBag(['fou' => 'baz']);
        $unresolvedFixtures = (new FixtureBag())->with($unresolvedFixture);
        $injectedObjects = new ObjectBag([
            'stdClass' => [
                'std' => new \stdClass(),
            ],
        ]);

        $set = new FixtureSet($loadedParameters, $injectedParameters, $unresolvedFixtures, $injectedObjects);

        $resolvedParameters = new ParameterBag([
            'foo' => 'bar',
            'fou' => 'baz',
        ]);
        $parametersResolverProphecy = $this->prophesize(ParameterBagResolverInterface::class);
        $parametersResolverProphecy->resolve($loadedParameters, $injectedParameters)->willReturn($resolvedParameters);
        /** @var ParameterBagResolverInterface $parametersResolver */
        $parametersResolver = $parametersResolverProphecy->reveal();

        $resolvedFixtures = (new FixtureBag())->with($resolvedFixture);
        $fixtureResolverProphecy = $this->prophesize(FixtureBagResolverInterface::class);
        $fixtureResolverProphecy->resolve($unresolvedFixtures)->willReturn($resolvedFixtures);
        /** @var FixtureBagResolverInterface $fixtureResolver */
        $fixtureResolver = $fixtureResolverProphecy->reveal();

        $expected = new ResolvedFixtureSet($resolvedParameters, $resolvedFixtures, $injectedObjects);

        $resolver = new SimpleFixtureSetResolver($parametersResolver, $fixtureResolver);
        $actual = $resolver->resolve($set);

        $this->assertEquals($expected, $actual);

        $parametersResolverProphecy->resolve(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $fixtureResolverProphecy->resolve(Argument::any())->shouldHaveBeenCalledTimes(1);
    }
}
