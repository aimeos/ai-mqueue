<?php

namespace Aimeos\MW\MQueue;


class BeanstalkTest extends \PHPUnit\Framework\TestCase
{
	protected function setUp()
	{
		if( class_exists( '\Pheanstalk\Pheanstalk' ) === false ) {
			$this->markTestSkipped( 'Please install the "pheanstalk" library via composer first' );
		}
	}


	public function testProcess()
	{
		try
		{
			$mqueue = new \Aimeos\MW\MQueue\Beanstalk( array( 'conntimeout' => 1, 'readtimeout' => 1 ) );
			$queue = $mqueue->getQueue( 'aimeos_unittest' );
		}
		catch( \Aimeos\MW\MQueue\Exception $e )
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
		$object = new \Aimeos\MW\MQueue\Beanstalk( array( 'host' => '192.168.255.255',  'conntimeout' => 1 ) );
		$this->assertInstanceOf( '\Aimeos\MW\MQueue\Iface', $object );
	}


	public function testMultiConnection()
	{
		$config = array( 'host' => array( '192.168.254.255', '192.168.255.255' ),  'conntimeout' => 1 );
		$object = new \Aimeos\MW\MQueue\Beanstalk( $config );
		$this->assertInstanceOf( '\Aimeos\MW\MQueue\Iface', $object );
	}


	public function testGetQueue()
	{
		$object = new \Aimeos\MW\MQueue\Beanstalk( array( 'host' => '192.168.255.255',  'conntimeout' => 1 ) );

		$this->setExpectedException( '\Aimeos\MW\MQueue\Exception' );
		$object->getQueue( 'test' );
	}
}
