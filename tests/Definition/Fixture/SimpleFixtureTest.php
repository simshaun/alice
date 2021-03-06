<?php

/*
 * This file is part of the Alice package.
 *  
 * (c) Nelmio <hello@nelm.io>
 *  
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\Definition\Fixture;

use Nelmio\Alice\Definition\MethodCall\DummyMethodCall;
use Nelmio\Alice\Definition\SpecificationBagFactory;
use Nelmio\Alice\FixtureInterface;

/**
 * @covers Nelmio\Alice\Definition\Fixture\SimpleFixture
 */
class SimpleFixtureTest extends \PHPUnit_Framework_TestCase
{
    public function testIsAFixture()
    {
        $this->assertTrue(is_a(SimpleFixture::class, FixtureInterface::class, true));
    }
    
    public function testReadAccessorsReturnPropertiesValues()
    {
        $reference = 'user0';
        $className = 'Nelmio\Alice\Entity\User';
        $specs = SpecificationBagFactory::create();

        $fixture = new SimpleFixture($reference, $className, $specs);

        $this->assertEquals($reference, $fixture->getId());
        $this->assertEquals($className, $fixture->getClassName());
        $this->assertEquals($specs, $fixture->getSpecs());
    }

    public function testWithersReturnNewModifiedInstance()
    {
        $reference = 'user0';
        $className = 'Nelmio\Alice\Entity\User';
        $specs = SpecificationBagFactory::create();
        $newSpecs = SpecificationBagFactory::create(new DummyMethodCall('dummy'));

        $fixture = new SimpleFixture($reference, $className, $specs);
        $newFixture = $fixture->withSpecs($newSpecs);

        $this->assertInstanceOf(SimpleFixture::class, $newFixture);
        $this->assertNotSame($fixture, $newFixture);

        $this->assertEquals($specs, $fixture->getSpecs());
        $this->assertEquals($newSpecs, $newFixture->getSpecs());
    }
}
