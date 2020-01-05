<?php

namespace Aimeos\MW\MQueue\Queue;


class BeanstalkTest extends \PHPUnit\Framework\TestCase
{
	private $mock;
	private $object;


	protected function setUp() : void
	{
		if( class_exists( '\Pheanstalk\Pheanstalk' ) === false ) {
			$this->markTestSkipped( 'Please install the "pheanstalk" library via composer first' );
		}

		$this->mock = $this->getMockBuilder( \Pheanstalk\Pheanstalk::class )
			->setMethods( array( 'useTube', 'watch', 'put', 'delete', 'reserve' ) )
			->disableOriginalConstructor()
			->getMock();

		$this->mock->expects( $this->any() )->method( 'useTube' )
			->will( $this->returnValue( $this->mock ) );

		$this->object = new \Aimeos\MW\MQueue\Queue\Beanstalk( $this->mock, 'test' );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testConstructorException()
	{
		$this->mock->expects( $this->once() )->method( 'watch' )
			->will( $this->throwException( new \Pheanstalk\Exception() ) );

		$this->expectException( \Aimeos\MW\MQueue\Exception::class );
		new \Aimeos\MW\MQueue\Queue\Beanstalk( $this->mock, 'test' );
	}


	public function testAdd()
	{
		$this->mock->expects( $this->once() )->method( 'put' );

		$this->object->add( 'test' );
	}


	public function testAddException()
	{
		$this->mock->expects( $this->once() )->method( 'put' )
			->will( $this->throwException( new \Pheanstalk\Exception() ) );

		$this->expectException( \Aimeos\MW\MQueue\Exception::class );
		$this->object->add( 'test' );
	}


	public function testDel()
	{
		$msg = new \Pheanstalk\Job( 1, 'test' );
		$message = new \Aimeos\MW\MQueue\Message\Beanstalk( $msg );

		$this->mock->expects( $this->once() )->method( 'delete' );

		$this->object->del( $message );
	}


	public function testDelException()
	{
		$msg = new \Pheanstalk\Job( 1, 'test' );
		$message = new \Aimeos\MW\MQueue\Message\Beanstalk( $msg );

		$this->mock->expects( $this->once() )->method( 'delete' )
			->will( $this->throwException( new \Pheanstalk\Exception() ) );

		$this->expectException( \Aimeos\MW\MQueue\Exception::class );
		$this->object->del( $message );
	}


	public function testGet()
	{
		$msg = new \Pheanstalk\Job( 1, 'test' );

		$this->mock->expects( $this->once() )->method( 'reserve' )
			->will( $this->returnValue( $msg ) );

		$this->assertInstanceOf( \Aimeos\MW\MQueue\Message\Iface::class, $this->object->get() );
	}


	public function testGetNone()
	{
		$this->mock->expects( $this->once() )->method( 'reserve' )
			->will( $this->returnValue( false ) );

		$this->assertNull( $this->object->get() );
	}
}
