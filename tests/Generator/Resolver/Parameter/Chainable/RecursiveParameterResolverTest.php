<?php

/*
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\Generator\Resolver\Parameter\Chainable;

use Nelmio\Alice\Exception\Generator\Resolver\RecursionLimitReachedException;
use Nelmio\Alice\Generator\Resolver\ChainableParameterResolverInterface;
use Nelmio\Alice\Generator\Resolver\FakeParameterResolver;
use Nelmio\Alice\Generator\Resolver\Parameter\ImmutableDummyChainableResolverAwareResolver;
use Nelmio\Alice\Generator\Resolver\ParameterResolverAwareInterface;
use Nelmio\Alice\Generator\Resolver\ParameterResolverInterface;
use Nelmio\Alice\Generator\Resolver\ResolvingContext;
use Nelmio\Alice\Parameter;
use Nelmio\Alice\ParameterBag;
use Prophecy\Argument;

/**
 * @covers Nelmio\Alice\Generator\Resolver\Parameter\Chainable\RecursiveParameterResolver
 */
class RecursiveParameterResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testIsAChainableParameterResolver()
    {
        $this->assertTrue(is_a(RecursiveParameterResolver::class, ChainableParameterResolverInterface::class, true));
    }

    public function testIsAParameterResolverAwareResolver()
    {
        $this->assertTrue(is_a(RecursiveParameterResolver::class, ParameterResolverAwareInterface::class, true));
    }

    /**
     * @expectedException \DomainException
     */
    public function testIsNotClonable()
    {
        clone new RecursiveParameterResolver(new FakeChainableParameterResolver());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected limit for recursive calls to be of at least 2. Got 1 instead.
     */
    public function testThrowsExceptionIfInvalidRecursionLimitGiven()
    {
        new RecursiveParameterResolver(new FakeChainableParameterResolver(), 1);
    }

    public function testWithersReturnNewModifiedInstance()
    {
        $resolver = new RecursiveParameterResolver(new DummyChainableParameterResolverAwareResolver());
        $newResolver = $resolver->withResolver(new FakeParameterResolver());

        $this->assertEquals(
            new RecursiveParameterResolver(new DummyChainableParameterResolverAwareResolver()),
            $resolver
        );
        $this->assertEquals(
            new RecursiveParameterResolver(new DummyChainableParameterResolverAwareResolver(new FakeParameterResolver())),
            $newResolver
        );
    }

    public function testUseDecoratedResolverToKnowWhichParameterItCanResolve()
    {
        $parameter1 = new Parameter('foo', null);
        $parameter2 = new Parameter('bar', null);

        $decoratedResolverProphecy = $this->prophesize(ChainableParameterResolverInterface::class);
        $decoratedResolverProphecy->canResolve($parameter1)->willReturn(false);
        $decoratedResolverProphecy->canResolve($parameter2)->willReturn(true);
        /* @var ChainableParameterResolverInterface $decoratedResolver */
        $decoratedResolver = $decoratedResolverProphecy->reveal();

        $resolver = new RecursiveParameterResolver($decoratedResolver);

        $this->assertFalse($resolver->canResolve($parameter1));
        $this->assertTrue($resolver->canResolve($parameter2));

        $decoratedResolverProphecy->canResolve(Argument::any())->shouldHaveBeenCalledTimes(2);
    }

    /**
     * @testdox Resolves the given parameter two times with the decorated resolver. If the two results are identical, return this result
     */
    public function testResolveWithNoChange()
    {
        $parameter = new Parameter('foo', null);
        $unresolvedParameters = new ParameterBag(['name' => 'Alice']);
        $resolvedParameters = new ParameterBag(['place' => 'Wonderlands']);
        $context = new \Nelmio\Alice\Generator\Resolver\ResolvingContext('foo');
        $expected = new ParameterBag(['foo' => 'bar']);

        $decoratedResolverProphecy = $this->prophesize(ChainableParameterResolverInterface::class);
        $decoratedResolverProphecy
            ->resolve(
                $parameter,
                $unresolvedParameters,
                $resolvedParameters,
                $context
            )
            ->willReturn($expected)
        ;
        $decoratedResolverProphecy
            ->resolve(
                new Parameter('foo', 'bar'),
                $unresolvedParameters,
                $resolvedParameters,
                $context
            )
            ->willReturn($expected)
        ;
        /* @var ChainableParameterResolverInterface $decoratedResolver */
        $decoratedResolver = $decoratedResolverProphecy->reveal();

        $resolver = new RecursiveParameterResolver($decoratedResolver);
        $actual = $resolver->resolve($parameter, $unresolvedParameters, $resolvedParameters, $context);

        $this->assertEquals($expected, $actual);
        $decoratedResolverProphecy->resolve(Argument::cetera())->shouldHaveBeenCalledTimes(2);
    }

    public function testIfMultipleParametersAreResolvedInTheProcessThenTheyWillBeIncludedInTheReturnedResult()
    {
        $parameter = new Parameter('foo', null);
        $unresolvedParameters = new ParameterBag(['name' => 'Alice']);
        $resolvedParameters = new ParameterBag(['place' => 'Wonderlands']);
        $context = new \Nelmio\Alice\Generator\Resolver\ResolvingContext('foo');

        $decoratedResolverProphecy = $this->prophesize(ChainableParameterResolverInterface::class);
        $decoratedResolverProphecy
            ->resolve(
                $parameter,
                $unresolvedParameters,
                $resolvedParameters,
                $context
            )
            ->willReturn(
                new ParameterBag([
                    'foo' => 'first result',
                    'another_param1' => 'val1',
                ])
            )
        ;
        $decoratedResolverProphecy
            ->resolve(
                new Parameter('foo', 'first result'),
                $unresolvedParameters,
                $resolvedParameters,
                $context
            )
            ->willReturn(
                new ParameterBag([
                    'foo' => 'second result',
                    'another_param2' => 'val2',
                    // 'another_param1' has already been resolved so is not return in the result set!
                ])
            )
        ;
        $decoratedResolverProphecy
            ->resolve(
                new Parameter('foo', 'second result'),
                $unresolvedParameters,
                $resolvedParameters,
                $context
            )
            ->willReturn(
                new ParameterBag([
                    'foo' => 'second result',   // same as previous
                ])
            )
        ;
        /* @var ChainableParameterResolverInterface $decoratedResolver */
        $decoratedResolver = $decoratedResolverProphecy->reveal();

        $resolver = new RecursiveParameterResolver($decoratedResolver);
        $actual = $resolver->resolve($parameter, $unresolvedParameters, $resolvedParameters, $context);

        $this->assertEquals(
            new ParameterBag([
                'foo' => 'second result',
                'another_param1' => 'val1',
                'another_param2' => 'val2',
            ]),
            $actual
        );
        $decoratedResolverProphecy->resolve(Argument::cetera())->shouldHaveBeenCalledTimes(3);
    }

    /**
     * @dataProvider provideContexts
     */
    public function testTheSameContextIsPassedBetweenEachResolution(ResolvingContext $context = null)
    {
        $parameter = new Parameter('foo', null);

        $decoratedResolverProphecy = $this->prophesize(ChainableParameterResolverInterface::class);
        $decoratedResolverProphecy
            ->resolve(Argument::any(), Argument::any(), Argument::any(), $context)
            ->willReturn(new ParameterBag(['foo' => null]))
        ;
        /* @var ChainableParameterResolverInterface $decoratedResolver */
        $decoratedResolver = $decoratedResolverProphecy->reveal();

        $resolver = new RecursiveParameterResolver($decoratedResolver);
        $resolver->resolve($parameter, new ParameterBag(), new ParameterBag(), $context);
    }

    /**
     * @testdox Resolves the given parameter two times with the decorated resolver. As the results differ, re-iterate the operation until two successive resolutions leads to the same result.
     */
    public function testResolveWithChange()
    {
        $parameter = new Parameter('foo', null);
        $unresolvedParameters = new ParameterBag(['name' => 'Alice']);
        $resolvedParameters = new ParameterBag(['place' => 'Wonderlands']);
        $context = new ResolvingContext('foo');

        $decoratedResolverProphecy = $this->prophesize(ChainableParameterResolverInterface::class);
        $decoratedResolverProphecy
            ->resolve(
                $parameter,
                $unresolvedParameters,
                $resolvedParameters,
                $context
            )
            ->willReturn(
                new ParameterBag(['foo' => 'result1'])
            )
        ;
        $decoratedResolverProphecy
            ->resolve(
                new Parameter('foo', 'result1'),
                $unresolvedParameters,
                $resolvedParameters,
                $context
            )
            ->willReturn(
                new ParameterBag(['foo' => 'result2'])
            )
        ;
        $decoratedResolverProphecy
            ->resolve(
                new Parameter('foo', 'result2'),
                $unresolvedParameters,
                $resolvedParameters,
                $context
            )
            ->willReturn(
                new ParameterBag(['foo' => 'result3'])
            )
        ;
        $decoratedResolverProphecy
            ->resolve(
                new Parameter('foo', 'result3'),
                $unresolvedParameters,
                $resolvedParameters,
                $context
            )
            ->willReturn(
                $expected = new ParameterBag(['foo' => 'result3'])
            )
        ;
        /* @var ChainableParameterResolverInterface $decoratedResolver */
        $decoratedResolver = $decoratedResolverProphecy->reveal();

        $resolver = new RecursiveParameterResolver($decoratedResolver);
        $actual = $resolver->resolve($parameter, $unresolvedParameters, $resolvedParameters, $context);

        $this->assertEquals($expected, $actual);
        $decoratedResolverProphecy->resolve(Argument::cetera())->shouldHaveBeenCalledTimes(4);
    }

    public function testThrowsAnExceptionWhenRecursionLimitIsReached()
    {
        $parameter = new Parameter('foo', null);
        $unresolvedParameters = new ParameterBag();
        $resolvedParameters = new ParameterBag();
        $context = new ResolvingContext('foo');

        $decoratedResolverProphecy = $this->prophesize(ChainableParameterResolverInterface::class);
        $decoratedResolverProphecy
            ->resolve(Argument::cetera())
            ->will(
                function ($args) {
                    $hash = spl_object_hash($args[0]);

                    return new ParameterBag(['foo' => uniqid($hash)]);
                }
            )
        ;
        /* @var ChainableParameterResolverInterface $decoratedResolver */
        $decoratedResolver = $decoratedResolverProphecy->reveal();

        $resolver = new RecursiveParameterResolver($decoratedResolver);
        try {
            $resolver->resolve($parameter, $unresolvedParameters, $resolvedParameters, $context);
            $this->fail('Expected exception to be thrown.');
        } catch (RecursionLimitReachedException $exception) {
            $this->assertEquals(
                'Recursion limit (5 tries) reached while resolving the parameter "foo"',
                $exception->getMessage()
            );
            $decoratedResolverProphecy->resolve(Argument::cetera())->shouldHaveBeenCalledTimes(5);
        }
    }

    public function provideContexts()
    {
        return [
            'no context' => [
                null,
            ],
            'empty context' => [
                new \Nelmio\Alice\Generator\Resolver\ResolvingContext(),
            ],
            'context with random value' => [
                (new \Nelmio\Alice\Generator\Resolver\ResolvingContext())->with('name'),
            ],
        ];
    }
}
