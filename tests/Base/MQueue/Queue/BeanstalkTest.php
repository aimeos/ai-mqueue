<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 */


namespace Aimeos\Base\MQueue\Queue;


#[\PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations]
class BeanstalkTest extends \PHPUnit\Framework\TestCase
{
	private $pubMock;
	private $subMock;
	private $stub;
	private $object;


	protected function setUp() : void
	{
		if( class_exists( '\Pheanstalk\Pheanstalk' ) === false ) {
			$this->markTestSkipped( 'Please install the "pheanstalk" library via composer first' );
		}

		$this->pubMock = $this->createMock( \Pheanstalk\Contract\PheanstalkPublisherInterface::class );
		$this->subMock = $this->createMock( \Pheanstalk\Contract\PheanstalkSubscriberInterface::class );

		$this->stub = $this->createClientStub( $this->pubMock, $this->subMock );
		$this->object = new \Aimeos\Base\MQueue\Queue\Beanstalk( $this->stub, 'test', 30 );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testConstructorException()
	{
		$subMock = $this->createMock( \Pheanstalk\Contract\PheanstalkSubscriberInterface::class );
		$subMock->expects( $this->once() )->method( 'watch' )
			->will( $this->throwException( new \Pheanstalk\Exception() ) );

		$stub = $this->createClientStub( $this->pubMock, $subMock );

		$this->expectException( \Aimeos\Base\MQueue\Exception::class );
		new \Aimeos\Base\MQueue\Queue\Beanstalk( $stub, 'test' );
	}


	public function testAdd()
	{
		$this->pubMock->expects( $this->once() )->method( 'put' );

		$this->object->add( 'test' );
	}


	public function testAddException()
	{
		$this->pubMock->expects( $this->once() )->method( 'put' )
			->will( $this->throwException( new \Pheanstalk\Exception() ) );

		$this->expectException( \Aimeos\Base\MQueue\Exception::class );
		$this->object->add( 'test' );
	}


	public function testDel()
	{
		$msg = new \Pheanstalk\Values\Job( new \Pheanstalk\Values\JobId( 1 ), 'test' );
		$message = new \Aimeos\Base\MQueue\Message\Beanstalk( $msg );

		$this->subMock->expects( $this->once() )->method( 'delete' );

		$this->object->del( $message );
	}


	public function testDelException()
	{
		$msg = new \Pheanstalk\Values\Job( new \Pheanstalk\Values\JobId( 1 ), 'test' );
		$message = new \Aimeos\Base\MQueue\Message\Beanstalk( $msg );

		$this->subMock->expects( $this->once() )->method( 'delete' )
			->will( $this->throwException( new \Pheanstalk\Exception() ) );

		$this->expectException( \Aimeos\Base\MQueue\Exception::class );
		$this->object->del( $message );
	}


	public function testGet()
	{
		$msg = new \Pheanstalk\Values\Job( new \Pheanstalk\Values\JobId( 1 ), 'test' );

		$this->subMock->expects( $this->once() )->method( 'reserveWithTimeout' )
			->willReturn( $msg );

		$this->assertInstanceOf( \Aimeos\Base\MQueue\Message\Iface::class, $this->object->get() );
	}


	public function testGetNone()
	{
		$this->subMock->expects( $this->once() )->method( 'reserveWithTimeout' )
			->willReturn( null );

		$this->assertNull( $this->object->get() );
	}


	private function createClientStub( $pubMock, $subMock ) : object
	{
		return new class( $pubMock, $subMock ) {
			public function __construct( private $pub, private $sub ) {}
			public function useTube( \Pheanstalk\Values\TubeName $tube ) : void { $this->pub->useTube( $tube ); }
			public function watch( \Pheanstalk\Values\TubeName $tube ) : int { return $this->sub->watch( $tube ); }
			public function put( string $data, int $priority = 1024, int $delay = 0, int $ttr = 60 ) : \Pheanstalk\Contract\JobIdInterface { return $this->pub->put( $data, $priority, $delay, $ttr ); }
			public function delete( \Pheanstalk\Contract\JobIdInterface $job ) : void { $this->sub->delete( $job ); }
			public function reserve() : \Pheanstalk\Values\Job { return $this->sub->reserve(); }
			public function reserveWithTimeout( int $timeout ) : ?\Pheanstalk\Values\Job { return $this->sub->reserveWithTimeout( $timeout ); }
		};
	}
}
