<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


namespace Aimeos\Base\MQueue;


class AMQPTest extends \PHPUnit\Framework\TestCase
{
	protected function setUp() : void
	{
		if( class_exists( '\PhpAmqpLib\Connection\AMQPStreamConnection' ) === false ) {
			$this->markTestSkipped( 'Please install the "php-amqplib" library via composer first' );
		}
	}


	public function testProcess()
	{
		try
		{
			$mqueue = new \Aimeos\Base\MQueue\AMQP( array( 'host' => 'localhost' ) );
			$queue = $mqueue->getQueue( 'aimeos_unittest' );
		}
		catch( \Aimeos\Base\MQueue\Exception $e )
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
		$this->expectException( \Aimeos\Base\MQueue\Exception::class );
		new \Aimeos\Base\MQueue\AMQP( array( 'host' => '192.168.255.255', 'connection_timeout' => 0.1 ) );
	}


	public function testMultiConnection()
	{
		$this->expectException( \Aimeos\Base\MQueue\Exception::class );
		new \Aimeos\Base\MQueue\AMQP( array( 'host' => array( '192.168.254.255', '192.168.255.255' ), 'connection_timeout' => 0.1 ) );
	}


	public function testGetQueue()
	{
		$channel = $this->getMockBuilder( 'PhpAmqpLib\Channel\AMQPChannel' )
			->disableOriginalConstructor()
			->getMock();

		$object = $this->getMockBuilder( \Aimeos\Base\MQueue\AMQP::class )
			->onlyMethods( array( 'getChannel', '__destruct' ) )
			->disableOriginalConstructor()
			->getMock();

		$object->expects( $this->once() )->method( 'getChannel' )
			->will( $this->returnValue( $channel ) );

		$this->assertInstanceOf( \Aimeos\Base\MQueue\Queue\Iface::class, $object->getQueue( 'test' ) );
	}


	public function testGetQueueException()
	{
		$object = $this->getMockBuilder( \Aimeos\Base\MQueue\AMQP::class )
			->onlyMethods( array( 'getChannel', '__destruct' ) )
			->disableOriginalConstructor()
			->getMock();

		$object->expects( $this->once() )->method( 'getChannel' )
			->will( $this->throwException( new \RuntimeException() ) );

		$this->expectException( \Aimeos\Base\MQueue\Exception::class );
		$object->getQueue( 'test' );
	}
}
