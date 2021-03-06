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
 * @covers Nelmio\Alice\Generator\Instantiator\Chainable\NoMethodCallInstantiator
 */
class NoMethodCallInstantiatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NoMethodCallInstantiator
     */
    private $instantiator;

    public function setUp()
    {
        $this->instantiator = new NoMethodCallInstantiator();
    }

    public function testIsAChainableInstantiator()
    {
        $this->assertTrue(is_a(NoMethodCallInstantiator::class, ChainableInstantiatorInterface::class, true));
    }

    /**
     * @expectedException \DomainException
     */
    public function testIsNotClonable()
    {
        clone $this->instantiator;
    }

    public function testCanInstantiateFixtureWithNoMethodCallConstructor()
    {
        $fixture = new SimpleFixture('dummy', 'Dummy', SpecificationBagFactory::create(new NoMethodCall()));

        $this->assertTrue($this->instantiator->canInstantiate($fixture));
    }

    public function testCannotInstantiateFixtureWithDefaultConstructor()
    {
        $fixture = new SimpleFixture('dummy', 'Dummy', SpecificationBagFactory::create());

        $this->assertFalse($this->instantiator->canInstantiate($fixture));
    }

    public function testInstantiatesWithReflectionAndNoArguments()
    {
        $fixture = new SimpleFixture(
            'dummy',
            DummyWithRequiredParameterInConstructor::class,
            SpecificationBagFactory::create()
        );
        $set = $this->instantiator->instantiate($fixture, ResolvedFixtureSetFactory::create());

        $instance = $set->getObjects()->get($fixture)->getInstance();
        $this->assertInstanceOf(DummyWithRequiredParameterInConstructor::class, $instance);

        try {
            (new \ReflectionObject($instance))->getProperty('requiredParam');
            $this->fail('Expected exception to be thrown.');
        } catch (\ReflectionException $exception) {
            $this->assertEquals(
                'Property requiredParam does not exist',
                $exception->getMessage()
            );
        }
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
