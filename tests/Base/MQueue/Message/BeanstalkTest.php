<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


namespace Aimeos\Base\MQueue\Message;


class BeanstalkTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		if( class_exists( '\Pheanstalk\Job' ) === false ) {
			$this->markTestSkipped( 'Please install the "pheanstalk" library via composer first' );
		}

		$msg = new \Pheanstalk\Job( 1, 'test' );
		$this->object = new \Aimeos\Base\MQueue\Message\Beanstalk( $msg );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetBody()
	{
		$this->assertEquals( 'test', $this->object->getBody() );
	}


	public function testObject()
	{
		$this->assertInstanceOf( \Pheanstalk\Job::class, $this->object->object() );
	}


	public function testToString()
	{
		$this->assertEquals( 'test', (string) $this->object );
	}
}
