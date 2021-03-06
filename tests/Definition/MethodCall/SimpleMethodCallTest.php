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

/**
 * @covers Nelmio\Alice\Definition\MethodCall\SimpleMethodCall
 */
class SimpleMethodCallTest extends \PHPUnit_Framework_TestCase
{
    public function testIsAMethodCall()
    {
        $this->assertTrue(is_a(SimpleMethodCall::class, MethodCallInterface::class, true));
    }
    
    public function testReadAccessorsReturnPropertiesValues()
    {
        $method = 'setUsername';
        $arguments = [new \stdClass()];

        $definition = new SimpleMethodCall($method, $arguments);

        $this->assertNull($definition->getCaller());
        $this->assertEquals($method, $definition->getMethod());
        $this->assertEquals($arguments, $definition->getArguments());
        $this->assertEquals($method, $definition->__toString());

        $definition = new SimpleMethodCall($method, null);

        $this->assertNull($definition->getCaller());
        $this->assertEquals($method, $definition->getMethod());
        $this->assertNull($definition->getArguments());
        $this->assertEquals($method, $definition->__toString());
    }

    public function testIsImmutable()
    {
        $arguments = [
            $arg0 = new \stdClass(),
        ];
        $definition = new SimpleMethodCall('foo', $arguments);

        // Mutate before reading values
        $arg0->foo = 'bar';

        // Mutate retrieved values
        $definition->getArguments()[0]->foo = 'baz';

        $this->assertEquals(
            [
                new \stdClass(),
            ],
            $definition->getArguments()
        );
    }

    public function testCanCreateANewInstanceWithNoArguments()
    {
        $method = 'setUsername';
        $arguments = null;
        $definition = new SimpleMethodCall($method, $arguments);

        $newArguments = [new \stdClass()];
        $newDefinition = $definition->withArguments($newArguments);

        $this->assertInstanceOf(SimpleMethodCall::class, $newDefinition);

        $this->assertNull($definition->getCaller());
        $this->assertEquals($method, $definition->getMethod());
        $this->assertEquals($arguments, $definition->getArguments());
        $this->assertEquals($method, $definition->__toString());

        $this->assertNull($newDefinition->getCaller());
        $this->assertEquals($method, $newDefinition->getMethod());
        $this->assertEquals($newArguments, $newDefinition->getArguments());
        $this->assertEquals($method, $newDefinition->__toString());
    }

    public function testCanCreateANewInstanceWithArguments()
    {
        $method = 'setUsername';
        $arguments = null;
        $definition = new SimpleMethodCall($method, $arguments);

        $newArguments = [
            $arg0 = new \stdClass(),
        ];
        $newDefinition = $definition->withArguments($newArguments);

        // Mutate before reading values
        $arg0->foo = 'bar';

        $this->assertInstanceOf(SimpleMethodCall::class, $newDefinition);

        $this->assertNull($definition->getCaller());
        $this->assertEquals($method, $definition->getMethod());
        $this->assertEquals($arguments, $definition->getArguments());
        $this->assertEquals($method, $definition->__toString());

        $this->assertNull($newDefinition->getCaller());
        $this->assertEquals($method, $newDefinition->getMethod());
        $this->assertEquals(
            [
                new \stdClass(),
            ],
            $newDefinition->getArguments()
        );
        $this->assertEquals($method, $newDefinition->__toString());
    }
}
