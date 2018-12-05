<?php

namespace Aimeos\MW\MQueue;


class AMQPTest extends \PHPUnit\Framework\TestCase
{
	protected function setUp()
	{
		if( class_exists( '\PhpAmqpLib\Connection\AMQPStreamConnection' ) === false ) {
			$this->markTestSkipped( 'Please install the "php-amqplib" library via composer first' );
		}
	}


	public function testProcess()
	{
		try
		{
			$mqueue = new \Aimeos\MW\MQueue\AMQP( array( 'host' => 'localhost' ) );
			$queue = $mqueue->getQueue( 'aimeos_unittest' );
		}
		catch( \Aimeos\MW\MQueue\Exception $e )
		{
			$this->markTestSkipped( 'No AMQP compliant server available at "localhost"' );
		}

		$queue->add( 'testmsg' );
		$msg = $queue->get();
		$queue->del( $msg );

		$this->assertNull( $queue->get() );
	}


	public function testSingleConnection()
	{
		$this->setExpectedException( \Aimeos\MW\MQueue\Exception::class );
		new \Aimeos\MW\MQueue\AMQP( array( 'host' => '192.168.255.255', 'connection_timeout' => 0.1 ) );
	}


	public function testMultiConnection()
	{
		$this->setExpectedException( \Aimeos\MW\MQueue\Exception::class );
		new \Aimeos\MW\MQueue\AMQP( array( 'host' => array( '192.168.254.255', '192.168.255.255' ), 'connection_timeout' => 0.1 ) );
	}


	public function testGetQueue()
	{
		$channel = $this->getMockBuilder( 'PhpAmqpLib\Channel\AMQPChannel' )
			->disableOriginalConstructor()
			->getMock();

		$object = $this->getMockBuilder( \Aimeos\MW\MQueue\AMQP::class )
			->setMethods( array( 'getChannel', '__destruct' ) )
			->disableOriginalConstructor()
			->getMock();

		$object->expects( $this->once() )->method( 'getChannel' )
			->will( $this->returnValue( $channel ) );

		$this->assertInstanceOf( \Aimeos\MW\MQueue\Queue\Iface::class, $object->getQueue( 'test' ) );
	}


	public function testGetQueueException()
	{
		$object = $this->getMockBuilder( \Aimeos\MW\MQueue\AMQP::class )
			->setMethods( array( 'getChannel', '__destruct' ) )
			->disableOriginalConstructor()
			->getMock();

		$object->expects( $this->once() )->method( 'getChannel' )
			->will( $this->throwException( new \RuntimeException() ) );

		$this->setExpectedException( \Aimeos\MW\MQueue\Exception::class );
		$object->getQueue( 'test' );
	}
}
