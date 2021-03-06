<?php

/*
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\Generator\Instantiator\Chainable;

use Nelmio\Alice\Definition\Fixture\SimpleFixture;
use Nelmio\Alice\Definition\MethodCall\MethodCallWithReference;
use Nelmio\Alice\Definition\MethodCall\NoMethodCall;
use Nelmio\Alice\Definition\MethodCall\SimpleMethodCall;
use Nelmio\Alice\Definition\ServiceReference\DummyReference;
use Nelmio\Alice\Definition\SpecificationBagFactory;
use Nelmio\Alice\Entity\Instantiator\AbstractDummyWithRequiredParameterInConstructor;
use Nelmio\Alice\Entity\Instantiator\DummyWithRequiredParameterInConstructor;
use Nelmio\Alice\Generator\Instantiator\ChainableInstantiatorInterface;
use Nelmio\Alice\Generator\ResolvedFixtureSetFactory;

/**
 * @covers Nelmio\Alice\Generator\Instantiator\Chainable\NoCallerMethodCallInstantiator
 */
class NoCallerMethodCallInstantiatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NoCallerMethodCallInstantiator
     */
    private $instantiator;

    public function setUp()
    {
        $this->instantiator = new NoCallerMethodCallInstantiator();
    }

    public function testIsAChainableInstantiator()
    {
        $this->assertTrue(is_a(NoCallerMethodCallInstantiator::class, ChainableInstantiatorInterface::class, true));
    }

    /**
     * @expectedException \DomainException
     */
    public function testIsNotClonable()
    {
        clone $this->instantiator;
    }

    public function testCannotInstantiateFixtureWithDefaultConstructor()
    {
        $fixture = new SimpleFixture('dummy', 'Dummy', SpecificationBagFactory::create());

        $this->assertFalse($this->instantiator->canInstantiate($fixture));
    }

    public function testCannotInstantiateFixtureWithNoMethodCallConstructor()
    {
        $fixture = new SimpleFixture('dummy', 'Dummy', SpecificationBagFactory::create(new NoMethodCall()));

        $this->assertFalse($this->instantiator->canInstantiate($fixture));
    }

    public function testCannotInstantiateFixtureWithIfConstructorIsAFactory()
    {
        $fixture = new SimpleFixture(
            'dummy',
            'Dummy',
            SpecificationBagFactory::create(new MethodCallWithReference(new DummyReference(), 'fake'))
        );

        $this->assertFalse($this->instantiator->canInstantiate($fixture));
    }

    public function testCanInstantiateFixtureWithIfConstructorIsAMalformedFactory()
    {
        $fixture = new SimpleFixture(
            'dummy',
            'Dummy',
            SpecificationBagFactory::create(new SimpleMethodCall('fake'))
        );

        $this->assertTrue($this->instantiator->canInstantiate($fixture));
    }

    public function testInstantiatesObjectWithArguments()
    {
        $fixture = new SimpleFixture(
            'dummy',
            DummyWithRequiredParameterInConstructor::class,
            SpecificationBagFactory::create(
                new SimpleMethodCall('__construct', [10])
            )
        );
        $set = $this->instantiator->instantiate($fixture, ResolvedFixtureSetFactory::create());

        $expected = new DummyWithRequiredParameterInConstructor(10);
        $actual = $set->getObjects()->get($fixture)->getInstance();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Edge case allowed because this scenario should not occur. Indeed if the method is other than the constructor,
     * the constructor is then a factory (static or not) i.e. has a caller. This situation is handled at the
     * denormalization level.
     */
    public function testIgnoresConstructorMethodSpecifiedByTheFixtureIfIsSomethingElseThanTheConstructor()
    {
        $fixture = new SimpleFixture(
            'dummy',
            DummyWithRequiredParameterInConstructor::class,
            SpecificationBagFactory::create(
                new SimpleMethodCall('fake', [10])
            )
        );
        $set = $this->instantiator->instantiate($fixture, ResolvedFixtureSetFactory::create());

        $expected = new DummyWithRequiredParameterInConstructor(10);
        $actual = $set->getObjects()->get($fixture)->getInstance();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException \Nelmio\Alice\Exception\Generator\Instantiator\InstantiationException
     * @expectedExceptionMessage Could not instantiate fixture "dummy".
     */
    public function testThrowsAnExceptionIfCouldNotInstantiateObject()
    {
        $fixture = new SimpleFixture(
            'dummy',
            AbstractDummyWithRequiredParameterInConstructor::class,
            SpecificationBagFactory::create(
                new SimpleMethodCall('fake', [10])
            )
        );

        $this->instantiator->instantiate($fixture, ResolvedFixtureSetFactory::create());
    }
}
