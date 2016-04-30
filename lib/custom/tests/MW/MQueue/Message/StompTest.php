<?php

namespace Aimeos\MW\MQueue\Message;


class StompTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		if( class_exists( '\Stomp\Message' ) === false ) {
			$this->markTestSkipped( 'Please install the "stomp-php" composer package first' );
		}

		$msg = new \Stomp\Message( 'test' );
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
		$this->assertInstanceOf( '\Stomp\Message', $this->object->getObject() );
	}
}
