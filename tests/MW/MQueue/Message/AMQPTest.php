<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */


namespace Aimeos\MW\MQueue\Message;


class AMQPTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		if( class_exists( '\PhpAmqpLib\Message\AMQPMessage' ) === false ) {
			$this->markTestSkipped( 'Please install the "php-amqplib" library via composer first' );
		}

		$msg = new \PhpAmqpLib\Message\AMQPMessage( 'test' );
		$this->object = new \Aimeos\MW\MQueue\Message\AMQP( $msg );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetBody()
	{
		$this->assertEquals( 'test', $this->object->getBody() );
	}


	public function testGetObject()
	{
		$this->assertInstanceOf( \PhpAmqpLib\Message\AMQPMessage::class, $this->object->object() );
	}
}
