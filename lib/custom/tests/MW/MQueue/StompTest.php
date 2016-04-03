<?php

namespace Aimeos\MW\MQueue;


class StompTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		if( class_exists( '\Stomp' ) === false ) {
			$this->markTestSkipped( 'Please install the "stomp" PHP extension first' );
		}
	}


	public function testGetQueueSingleConnection()
	{
		$object = new \Aimeos\MW\MQueue\Stomp( array( 'host' => 'localhost' ) );
	}


	public function testGetQueueMultiConnection()
	{
		$object = new \Aimeos\MW\MQueue\Stomp( array( 'host' => array( 'localhost', '127.0.0.1' ) ) );
	}


	public function testGetQueue()
	{
		$client = $this->getMockBuilder( '\Stomp' )
			->disableOriginalConstructor()
			->getMock();

		$object = $this->getMockBuilder( '\Aimeos\MW\MQueue\Stomp' )
			->setMethods( array( 'connect' ) )
			->disableOriginalConstructor()
			->getMock();

		$object->expects( $this->once() )->method( 'connect' )
			->will( $this->returnValue( $client ) );

		$this->assertInstanceOf( '\Aimeos\MW\MQueue\Queue\Iface', $object->getQueue( 'test' ) );
	}
}
