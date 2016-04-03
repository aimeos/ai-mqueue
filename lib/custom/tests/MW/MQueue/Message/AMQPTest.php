<?php

namespace Aimeos\MW\MQueue\Message;


class AMQPTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		if( class_exists( '\PhpAmqpLib\Message\AMQPMessage' ) === false ) {
			$this->markTestSkipped( 'Please install the "php-amqplib" library via composer first' );
		}

		$msg = new \PhpAmqpLib\Message\AMQPMessage( 'test' );
		$this->object = new \Aimeos\MW\MQueue\Message\AMQP( $msg );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetBody()
	{
		$this->assertEquals( 'test', $this->object->getBody() );
	}


	public function testGetObject()
	{
		$this->assertInstanceOf( '\PhpAmqpLib\Message\AMQPMessage', $this->object->getObject() );
	}
}
