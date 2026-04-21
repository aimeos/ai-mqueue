<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 */


namespace Aimeos\Base\MQueue\Message;


class BeanstalkTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		if( class_exists( '\Pheanstalk\Values\Job' ) === false ) {
			$this->markTestSkipped( 'Please install the "pheanstalk" library via composer first' );
		}

		$msg = new \Pheanstalk\Values\Job( new \Pheanstalk\Values\JobId( 1 ), 'test' );
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
		$this->assertInstanceOf( \Pheanstalk\Values\Job::class, $this->object->object() );
	}


	public function testToString()
	{
		$this->assertEquals( 'test', (string) $this->object );
	}
}
