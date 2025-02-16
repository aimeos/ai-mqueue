<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2025
 */


namespace Aimeos\Base\MQueue\Queue;


class StompTest extends \PHPUnit\Framework\TestCase
{
	private $mock;
	private $object;


	protected function setUp() : void
	{
		if( class_exists( '\Stomp\Stomp' ) === false ) {
			$this->markTestSkipped( 'Please install the "stomp-php" composer package first' );
		}

		$this->mock = $this->getMockBuilder( \Stomp\Stomp::class )
			->onlyMethods( array( 'subscribe', 'unsubscribe', 'send', 'ack', 'hasFrameToRead', 'readFrame', '__destruct' ) )
			->disableOriginalConstructor()
			->getMock();

		$this->object = new \Aimeos\Base\MQueue\Queue\Stomp( $this->mock, 'test' );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testConstructorException()
	{
		$this->mock->expects( $this->once() )->method( 'subscribe' )
			->willReturn( false );

		$this->expectException( \Aimeos\Base\MQueue\Exception::class );
		new \Aimeos\Base\MQueue\Queue\Stomp( $this->mock, 'test' );
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
		$msg = new \Stomp\Message( 'test' );
		$message = new \Aimeos\Base\MQueue\Message\Stomp( $msg );

		$this->mock->expects( $this->once() )->method( 'ack' );

		$this->object->del( $message );
	}


	public function testDelException()
	{
		$msg = new \Stomp\Message( 'test' );
		$message = new \Aimeos\Base\MQueue\Message\Stomp( $msg );

		$this->mock->expects( $this->once() )->method( 'ack' )
			->willReturn( false );

		$this->expectException( \Aimeos\Base\MQueue\Exception::class );
		$this->object->del( $message );
	}


	public function testGet()
	{
		$msg = new \Stomp\Message( 'test' );

		$this->mock->expects( $this->once() )->method( 'hasFrameToRead' )
			->willReturn( true );

		$this->mock->expects( $this->once() )->method( 'readFrame' )
			->willReturn( $msg );

		$this->assertInstanceOf( \Aimeos\Base\MQueue\Message\Iface::class, $this->object->get() );
	}


	public function testGetNone()
	{
		$this->mock->expects( $this->once() )->method( 'hasFrameToRead' )
			->willReturn( false );

		$this->assertNull( $this->object->get() );
	}


	public function testGetException()
	{
		$this->mock->expects( $this->once() )->method( 'hasFrameToRead' )
			->willReturn( true );

		$this->mock->expects( $this->once() )->method( 'readFrame' )
			->will( $this->throwException( new \RuntimeException() ) );

		$this->expectException( \Aimeos\Base\MQueue\Exception::class );
		$this->object->get();
	}
}
