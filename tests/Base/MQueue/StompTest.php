<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Base\MQueue;


class StompTest extends \PHPUnit\Framework\TestCase
{
	protected function setUp() : void
	{
		if( class_exists( '\Stomp\Stomp' ) === false ) {
			$this->markTestSkipped( 'Please install the "stomp-php" composer package first' );
		}
	}


	public function testProcess()
	{
		try
		{
			$config = array( 'username' => 'guest', 'password' => 'guest' );
			$mqueue = new \Aimeos\Base\MQueue\Stomp( $config );
			$queue = $mqueue->getQueue( 'aimeos_unittest' );
		}
		catch( \Aimeos\Base\MQueue\Exception $e )
		{
			$this->markTestSkipped( 'No Stomp compliant server available at "localhost"' );
		}

		$queue->add( 'testmsg' );
		$msg = $queue->get();
		$queue->del( $msg );

		$this->assertNull( $queue->get() );
	}


	public function testGetQueueException()
	{
		$object = new \Aimeos\Base\MQueue\Stomp( array( 'host' => 'tcp://127.0.0.1:61616' ) );

		$this->expectException( \Aimeos\Base\MQueue\Exception::class );
		$object->getQueue( 'test' );
	}


	public function testGetQueue()
	{
		$client = $this->getMockBuilder( \Stomp\Stomp::class )
			->disableOriginalConstructor()
			->getMock();

		$object = $this->getMockBuilder( \Aimeos\Base\MQueue\Stomp::class )
			->onlyMethods( array( 'connect' ) )
			->disableOriginalConstructor()
			->getMock();

		$object->expects( $this->once() )->method( 'connect' )
			->will( $this->returnValue( $client ) );

		$this->assertInstanceOf( \Aimeos\Base\MQueue\Queue\Iface::class, $object->getQueue( 'test' ) );
	}
}
