<?php

namespace Aimeos\MW\MQueue\Queue;


class AMQPTest extends \PHPUnit_Framework_TestCase
{
	private $mock;
	private $object;


	protected function setUp()
	{
		if( class_exists( '\PhpAmqpLib\Channel\AMQPChannel' ) === false ) {
			$this->markTestSkipped( 'Please install the "php-amqplib" library via composer first' );
		}

		$this->mock = $this->getMockBuilder( '\PhpAmqpLib\Channel\AMQPChannel' )
			->setMethods( array( 'queue_declare', 'basic_qos', 'basic_publish', 'basic_get', 'basic_ack' ) )
			->disableOriginalConstructor()
			->getMock();

		$this->object = new \Aimeos\MW\MQueue\Queue\AMQP( $this->mock, 'test' );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testConstructorException()
	{
		$this->mock->expects( $this->once() )->method( 'queue_declare' )
			->will( $this->throwException( new \RuntimeException() ) );

		$this->setExpectedException( '\Aimeos\MW\MQueue\Exception' );
		new \Aimeos\MW\MQueue\Queue\AMQP( $this->mock, 'test' );
	}


	public function testAdd()
	{
		$this->mock->expects( $this->once() )->method( 'basic_publish' );

		$this->object->add( 'test' );
	}


	public function testAddException()
	{
		$this->mock->expects( $this->once() )->method( 'basic_publish' )
			->will( $this->throwException( new \RuntimeException() ) );

		$this->setExpectedException( '\Aimeos\MW\MQueue\Exception' );
		$this->object->add( 'test' );
	}


	public function testDel()
	{
		$msg = new \PhpAmqpLib\Message\AMQPMessage( 'test' );
		$msg->delivery_info = array( 'channel' => $this->mock, 'delivery_tag' => 'test' );

		$message = new \Aimeos\MW\MQueue\Message\AMQP( $msg );

		$this->mock->expects( $this->once() )->method( 'basic_ack' );

		$this->object->del( $message );
	}


	public function testDelException()
	{
		$msg = new \PhpAmqpLib\Message\AMQPMessage( 'test' );
		$msg->delivery_info = array( 'channel' => $this->mock, 'delivery_tag' => 'test' );

		$message = new \Aimeos\MW\MQueue\Message\AMQP( $msg );

		$this->mock->expects( $this->once() )->method( 'basic_ack' )
			->will( $this->throwException( new \RuntimeException() ) );

		$this->setExpectedException( '\Aimeos\MW\MQueue\Exception' );
		$this->object->del( $message );
	}


	public function testGet()
	{
		$msg = new \PhpAmqpLib\Message\AMQPMessage( 'test' );

		$this->mock->expects( $this->once() )->method( 'basic_get' )
			->will( $this->returnValue( $msg ) );

		$this->assertInstanceOf( '\Aimeos\MW\MQueue\Message\Iface', $this->object->get() );
	}


	public function testGetNone()
	{
		$this->mock->expects( $this->once() )->method( 'basic_get' )
			->will( $this->returnValue( null ) );

		$this->assertNull( $this->object->get() );
	}


	public function testGetException()
	{
		$this->mock->expects( $this->once() )->method( 'basic_get' )
			->will( $this->throwException( new \RuntimeException() ) );

		$this->setExpectedException( '\Aimeos\MW\MQueue\Exception' );
		$this->object->get();
	}
}
