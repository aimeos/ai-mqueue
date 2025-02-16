<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2025
 */


namespace Aimeos\Base\MQueue\Queue;


class AMQPTest extends \PHPUnit\Framework\TestCase
{
	private $mock;
	private $object;


	protected function setUp() : void
	{
		if( class_exists( '\PhpAmqpLib\Channel\AMQPChannel' ) === false ) {
			$this->markTestSkipped( 'Please install the "php-amqplib" library via composer first' );
		}

		$this->mock = $this->getMockBuilder( \PhpAmqpLib\Channel\AMQPChannel::class )
			->onlyMethods( array( 'queue_declare', 'basic_qos', 'basic_publish', 'basic_get', 'basic_ack', 'close' ) )
			->disableOriginalConstructor()
			->getMock();

		$this->object = new \Aimeos\Base\MQueue\Queue\AMQP( $this->mock, 'test' );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testConstructorException()
	{
		$this->mock->expects( $this->once() )->method( 'queue_declare' )
			->will( $this->throwException( new \RuntimeException() ) );

		$this->expectException( \Aimeos\Base\MQueue\Exception::class );
		new \Aimeos\Base\MQueue\Queue\AMQP( $this->mock, 'test' );
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

		$this->expectException( \Aimeos\Base\MQueue\Exception::class );
		$this->object->add( 'test' );
	}


	public function testDel()
	{
		$msg = new \PhpAmqpLib\Message\AMQPMessage( 'test' );
		$msg->delivery_info = array( 'channel' => $this->mock, 'delivery_tag' => 'test' );

		$message = new \Aimeos\Base\MQueue\Message\AMQP( $msg );

		$this->mock->expects( $this->once() )->method( 'basic_ack' );

		$this->object->del( $message );
	}


	public function testDelException()
	{
		$msg = new \PhpAmqpLib\Message\AMQPMessage( 'test' );
		$msg->delivery_info = array( 'channel' => $this->mock, 'delivery_tag' => 'test' );

		$message = new \Aimeos\Base\MQueue\Message\AMQP( $msg );

		$this->mock->expects( $this->once() )->method( 'basic_ack' )
			->will( $this->throwException( new \RuntimeException() ) );

		$this->expectException( \Aimeos\Base\MQueue\Exception::class );
		$this->object->del( $message );
	}


	public function testGet()
	{
		$msg = new \PhpAmqpLib\Message\AMQPMessage( 'test' );

		$this->mock->expects( $this->once() )->method( 'basic_get' )
			->willReturn( $msg );

		$this->assertInstanceOf( \Aimeos\Base\MQueue\Message\Iface::class, $this->object->get() );
	}


	public function testGetNone()
	{
		$this->mock->expects( $this->once() )->method( 'basic_get' )
			->willReturn( null );

		$this->assertNull( $this->object->get() );
	}


	public function testGetException()
	{
		$this->mock->expects( $this->once() )->method( 'basic_get' )
			->will( $this->throwException( new \RuntimeException() ) );

		$this->expectException( \Aimeos\Base\MQueue\Exception::class );
		$this->object->get();
	}
}
