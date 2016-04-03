<?php

namespace Aimeos\MW\MQueue;


class BeanstalkTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		if( class_exists( '\Pheanstalk\Pheanstalk' ) === false ) {
			$this->markTestSkipped( 'Please install the "pheanstalk" library via composer first' );
		}
	}


	public function testSingleConnection()
	{
		$object = new \Aimeos\MW\MQueue\Beanstalk( array( 'host' => 'localhost' ) );
		$this->assertInstanceOf( '\Aimeos\MW\MQueue\Iface', $object );
	}


	public function testMultiConnection()
	{
		$object = new \Aimeos\MW\MQueue\Beanstalk( array( 'host' => array( 'localhost', '127.0.0.1' ) ) );
		$this->assertInstanceOf( '\Aimeos\MW\MQueue\Iface', $object );
	}


	public function testGetQueue()
	{
		$object = new \Aimeos\MW\MQueue\Beanstalk( array() );

		$this->setExpectedException( '\Aimeos\MW\MQueue\Exception' );
		$object->getQueue( 'test' );
	}
}
