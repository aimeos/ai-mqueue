<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2025
 */


namespace Aimeos\Base\MQueue\Message;


class StompTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		if( class_exists( '\Stomp\Message' ) === false ) {
			$this->markTestSkipped( 'Please install the "stomp-php" composer package first' );
		}

		$msg = new \Stomp\Message( 'test' );
		$this->object = new \Aimeos\Base\MQueue\Message\Stomp( $msg );
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
		$this->assertInstanceOf( \Stomp\Message::class, $this->object->object() );
	}


	public function testToString()
	{
		$this->assertEquals( 'test', (string) $this->object );
	}
}
