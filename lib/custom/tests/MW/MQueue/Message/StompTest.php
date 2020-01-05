<?php

namespace Aimeos\MW\MQueue\Message;


class StompTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		if( class_exists( '\Stomp\Message' ) === false ) {
			$this->markTestSkipped( 'Please install the "stomp-php" composer package first' );
		}

		$msg = new \Stomp\Message( 'test' );
		$this->object = new \Aimeos\MW\MQueue\Message\Stomp( $msg );
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
		$this->assertInstanceOf( \Stomp\Message::class, $this->object->getObject() );
	}
}
