<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2025
 */


namespace Aimeos\Base\MQueue\Message;


class AMQPTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		if( class_exists( '\PhpAmqpLib\Message\AMQPMessage' ) === false ) {
			$this->markTestSkipped( 'Please install the "php-amqplib" library via composer first' );
		}

		$msg = new \PhpAmqpLib\Message\AMQPMessage( 'test' );
		$this->object = new \Aimeos\Base\MQueue\Message\AMQP( $msg );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetBody()
	{
		$this->assertEquals( 'test', $this->object->getBody() );
	}


	public function testObject()
	{
		$this->assertInstanceOf( \PhpAmqpLib\Message\AMQPMessage::class, $this->object->object() );
	}


	public function testToString()
	{
		$this->assertEquals( 'test', (string) $this->object );
	}
}
