<?php

namespace Aimeos\MW\MQueue\Message;


class StompTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		if( class_exists( '\StompFrame' ) === false ) {
			$this->markTestSkipped( 'Please install the Stomp PHP extension first' );
		}

		$msg = new \StompFrame( 'COMMAND', array(), 'test' );
		$this->object = new \Aimeos\MW\MQueue\Message\Stomp( $msg );
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
		$this->assertInstanceOf( '\StompFrame', $this->object->getObject() );
	}
}
