<?php

namespace Aimeos\MW\MQueue\Message;


class BeanstalkTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp()
	{
		if( class_exists( '\Pheanstalk\Job' ) === false ) {
			$this->markTestSkipped( 'Please install the "pheanstalk" library via composer first' );
		}

		$msg = new \Pheanstalk\Job( 1, 'test' );
		$this->object = new \Aimeos\MW\MQueue\Message\Beanstalk( $msg );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetBody()
	{
		$this->assertEquals( 'test', $this->object->getBody() );
	}


	public function testGetObject()
	{
		$this->assertInstanceOf( '\Pheanstalk\Job', $this->object->getObject() );
	}
}
