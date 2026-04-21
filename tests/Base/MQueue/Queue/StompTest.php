<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 */


namespace Aimeos\Base\MQueue\Queue;


class StompTest extends \PHPUnit\Framework\TestCase
{
	private $mock;
	private $object;


	protected function setUp() : void
	{
		if( class_exists( '\Stomp\StatefulStomp' ) === false ) {
			$this->markTestSkipped( 'Please install the "stomp-php" composer package first' );
		}

		$this->mock = $this->getMockBuilder( \Stomp\StatefulStomp::class )
			->onlyMethods( array( 'subscribe', 'unsubscribe', 'send', 'ack', 'read' ) )
			->disableOriginalConstructor()
			->getMock();

		$this->mock->expects( $this->any() )->method( 'subscribe' )
			->willReturn( 1 );

		$this->object = new \Aimeos\Base\MQueue\Queue\Stomp( $this->mock, 'test' );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testAdd()
	{
		$this->mock->expects( $this->once() )->method( 'send' );

		$this->object->add( 'test' );
	}


	public function testAddException()
	{
		$this->mock->expects( $this->once() )->method( 'send' )
			->willReturn( false );

		$this->expectException( \Aimeos\Base\MQueue\Exception::class );
		$this->object->add( 'test' );
	}


	public function testDel()
	{
		$msg = new \Stomp\Transport\Message( 'test' );
		$message = new \Aimeos\Base\MQueue\Message\Stomp( $msg );

		$this->mock->expects( $this->once() )->method( 'ack' );

		$this->object->del( $message );
	}


	public function testGet()
	{
		$msg = new \Stomp\Transport\Message( 'test' );

		$this->mock->expects( $this->once() )->method( 'read' )
			->willReturn( $msg );

		$this->assertInstanceOf( \Aimeos\Base\MQueue\Message\Iface::class, $this->object->get() );
	}


	public function testGetNone()
	{
		$this->mock->expects( $this->once() )->method( 'read' )
			->willReturn( false );

		$this->assertNull( $this->object->get() );
	}


	public function testGetException()
	{
		$this->mock->expects( $this->once() )->method( 'read' )
			->will( $this->throwException( new \RuntimeException() ) );

		$this->expectException( \Aimeos\Base\MQueue\Exception::class );
		$this->object->get();
	}
}
