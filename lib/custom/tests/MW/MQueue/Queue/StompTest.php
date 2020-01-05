<?php

namespace Aimeos\MW\MQueue\Queue;


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
			->setMethods( array( 'subscribe', 'unsubscribe', 'send', 'ack', 'hasFrameToRead', 'readFrame', '__destruct' ) )
			->disableOriginalConstructor()
			->getMock();

		$this->object = new \Aimeos\MW\MQueue\Queue\Stomp( $this->mock, 'test' );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testConstructorException()
	{
		$this->mock->expects( $this->once() )->method( 'subscribe' )
			->will( $this->returnValue( false ) );

		$this->expectException( \Aimeos\MW\MQueue\Exception::class );
		new \Aimeos\MW\MQueue\Queue\Stomp( $this->mock, 'test' );
	}


	public function testAdd()
	{
		$this->mock->expects( $this->once() )->method( 'send' );

		$this->object->add( 'test' );
	}


	public function testAddException()
	{
		$this->mock->expects( $this->once() )->method( 'send' )
			->will( $this->returnValue( false ) );

		$this->expectException( \Aimeos\MW\MQueue\Exception::class );
		$this->object->add( 'test' );
	}


	public function testDel()
	{
		$msg = new \Stomp\Message( 'test' );
		$message = new \Aimeos\MW\MQueue\Message\Stomp( $msg );

		$this->mock->expects( $this->once() )->method( 'ack' );

		$this->object->del( $message );
	}


	public function testDelException()
	{
		$msg = new \Stomp\Message( 'test' );
		$message = new \Aimeos\MW\MQueue\Message\Stomp( $msg );

		$this->mock->expects( $this->once() )->method( 'ack' )
			->will( $this->returnValue( false ) );

		$this->expectException( \Aimeos\MW\MQueue\Exception::class );
		$this->object->del( $message );
	}


	public function testGet()
	{
		$msg = new \Stomp\Message( 'test' );

		$this->mock->expects( $this->once() )->method( 'hasFrameToRead' )
			->will( $this->returnValue( true ) );

		$this->mock->expects( $this->once() )->method( 'readFrame' )
			->will( $this->returnValue( $msg ) );

		$this->assertInstanceOf( \Aimeos\MW\MQueue\Message\Iface::class, $this->object->get() );
	}


	public function testGetNone()
	{
		$this->mock->expects( $this->once() )->method( 'hasFrameToRead' )
			->will( $this->returnValue( false ) );

		$this->assertNull( $this->object->get() );
	}


	public function testGetException()
	{
		$this->mock->expects( $this->once() )->method( 'hasFrameToRead' )
			->will( $this->returnValue( true ) );

		$this->mock->expects( $this->once() )->method( 'readFrame' )
			->will( $this->throwException( new \RuntimeException() ) );

		$this->expectException( \Aimeos\MW\MQueue\Exception::class );
		$this->object->get();
	}
}
