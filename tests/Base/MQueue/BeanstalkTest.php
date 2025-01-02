<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2025
 */


namespace Aimeos\Base\MQueue;


class BeanstalkTest extends \PHPUnit\Framework\TestCase
{
	protected function setUp() : void
	{
		if( class_exists( '\Pheanstalk\Pheanstalk' ) === false ) {
			$this->markTestSkipped( 'Please install the "pheanstalk" library via composer first' );
		}
	}


	public function testProcess()
	{
		try
		{
			$mqueue = new \Aimeos\Base\MQueue\Beanstalk( array( 'conntimeout' => 1, 'readtimeout' => 1 ) );
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


	public function testSingleConnection()
	{
		$object = new \Aimeos\Base\MQueue\Beanstalk( array( 'host' => '192.168.255.255',  'conntimeout' => 1 ) );
		$this->assertInstanceOf( \Aimeos\Base\MQueue\Iface::class, $object );
	}


	public function testMultiConnection()
	{
		$config = array( 'host' => array( '192.168.254.255', '192.168.255.255' ),  'conntimeout' => 1 );
		$object = new \Aimeos\Base\MQueue\Beanstalk( $config );
		$this->assertInstanceOf( \Aimeos\Base\MQueue\Iface::class, $object );
	}


	public function testGetQueue()
	{
		$object = new \Aimeos\Base\MQueue\Beanstalk( array( 'host' => '192.168.255.255',  'conntimeout' => 1 ) );

		$this->expectException( \Aimeos\Base\MQueue\Exception::class );
		$object->getQueue( 'test' );
	}
}
