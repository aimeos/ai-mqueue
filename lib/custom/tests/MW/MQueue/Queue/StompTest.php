<?php

namespace Aimeos\MW\MQueue\Queue;


class StompTest extends \PHPUnit_Framework_TestCase
{
	private $mock;
	private $object;


	protected function setUp()
	{
		if( class_exists( '\Stomp' ) === false ) {
			$this->markTestSkipped( 'Please install the "Stomp" PHP extension first' );
		}

		$this->mock = $this->getMockBuilder( '\Stomp' )
			->setMethods( array( 'subscribe', 'unsubscribe', 'send', 'ack', 'hasFrame', 'readFrame', 'error', '__destruct' ) )
			->disableOriginalConstructor()
			->getMock();

		$this->object = new \Aimeos\MW\MQueue\Queue\Stomp( $this->mock, 'test' );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testConstructorException()
	{
		$this->mock->expects( $this->once() )->method( 'subscribe' )
			->will( $this->returnValue( false ) );

		$this->mock->expects( $this->once() )->method( 'error' )
			->will( $this->returnValue( 'exception' ) );

		$this->setExpectedException( '\Aimeos\MW\MQueue\Exception' );
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

		$this->mock->expects( $this->once() )->method( 'error' )
			->will( $this->returnValue( 'exception' ) );

		$this->setExpectedException( '\Aimeos\MW\MQueue\Exception' );
		$this->object->add( 'test' );
	}


	public function testDel()
	{
		$msg = new \StompFrame( 'COMMAND', array(), 'test' );
		$message = new \Aimeos\MW\MQueue\Message\Stomp( $msg );

		$this->mock->expects( $this->once() )->method( 'ack' );

		$this->object->del( $message );
	}


	public function testDelException()
	{
		$msg = new \StompFrame( 'COMMAND', array(), 'test' );
		$message = new \Aimeos\MW\MQueue\Message\Stomp( $msg );

		$this->mock->expects( $this->once() )->method( 'ack' )
			->will( $this->returnValue( false ) );

		$this->setExpectedException( '\Aimeos\MW\MQueue\Exception' );
		$this->object->del( $message );
	}


	public function testGet()
	{
		$msg = new \StompFrame( 'test' );

		$this->mock->expects( $this->once() )->method( 'hasFrame' )
			->will( $this->returnValue( true ) );

		$this->mock->expects( $this->once() )->method( 'readFrame' )
			->will( $this->returnValue( $msg ) );

		$this->assertInstanceOf( '\Aimeos\MW\MQueue\Message\Iface', $this->object->get() );
	}


	public function testGetNone()
	{
		$this->mock->expects( $this->once() )->method( 'hasFrame' )
			->will( $this->returnValue( false ) );

		$this->assertNull( $this->object->get() );
	}


	public function testGetException()
	{
		$this->mock->expects( $this->once() )->method( 'hasFrame' )
			->will( $this->returnValue( true ) );

		$this->mock->expects( $this->once() )->method( 'readFrame' )
			->will( $this->throwException( new \StompException() ) );

		$this->setExpectedException( '\Aimeos\MW\MQueue\Exception' );
		$this->object->get();
	}
}
