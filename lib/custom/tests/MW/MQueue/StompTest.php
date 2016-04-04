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


	public function testProcess()
	{
		try
		{
			$config = array( 'username' => 'guest', 'password' => 'guest' );
			$mqueue = new \Aimeos\MW\MQueue\Stomp( $config );
			$queue = $mqueue->getQueue( 'aimeos_unittest' );
		}
		catch( \Aimeos\MW\MQueue\Exception $e )
		{
			$this->markTestSkipped( 'No Stomp compliant server available at "localhost"' );
		}

		$queue->add( 'testmsg' );
		$msg = $queue->get();
		$queue->del( $msg );

		$this->assertNull( $queue->get() );
	}


	public function testGetQueueSingleConnection()
	{
		$object = new \Aimeos\MW\MQueue\Stomp( array( 'host' => 'tcp://127.0.0.1:61616' ) );
	}


	public function testGetQueueMultiConnection()
	{
		$object = new \Aimeos\MW\MQueue\Stomp( array( 'host' => array( 'tcp://127.0.0.1:61616', 'tcp://127.0.0.1:61617' ) ) );
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
