<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 */


namespace Aimeos\Base\MQueue\Queue;


class Beanstalk implements Iface
{
	private object $client;
	private string $queue;
	private ?int $timeout;


	/**
	 * Initializes the queue object
	 *
	 * @param \Pheanstalk\Pheanstalk $client Client object
	 * @param string $queue Message queue name
	 * @param int $timeout Number of seconds until the message is passed to another client
	 * @throws \Aimeos\Base\MQueue\Exception
	 */
	public function __construct( object $client, string $queue, ?int $timeout = null )
	{
		try {
			$tube = new \Pheanstalk\Values\TubeName( $queue );
			$client->useTube( $tube );
			$client->watch( $tube );
		} catch( \Exception $e ) {
			throw new \Aimeos\Base\MQueue\Exception( $e->getMessage() );
		}

		$this->client = $client;
		$this->queue = $queue;
		$this->timeout = $timeout;
	}


	/**
	 * Adds a new message to the message queue
	 *
	 * @param string $msg Message, e.g. JSON encoded data
	 * @return \Aimeos\Base\MQueue\Queue\Iface MQueue queue instance for method chaining
	 */
	public function add( string $msg ) : \Aimeos\Base\MQueue\Queue\Iface
	{
		try {
			$this->client->put( $msg );
		} catch( \Exception $e ) {
			throw new \Aimeos\Base\MQueue\Exception( $e->getMessage() );
		}

		return $this;
	}


	/**
	 * Removes the message from the queue
	 *
	 * @param \Aimeos\Base\MQueue\Message\Iface $msg Message object
	 * @return \Aimeos\Base\MQueue\Queue\Iface MQueue queue instance for method chaining
	 */
	public function del( \Aimeos\Base\MQueue\Message\Iface $msg ) : \Aimeos\Base\MQueue\Queue\Iface
	{
		try {
			$this->client->delete( $msg->object() );
		} catch( \Exception $e ) {
			throw new \Aimeos\Base\MQueue\Exception( $e->getMessage() );
		}

		return $this;
	}


	/**
	 * Returns the next message from the queue
	 *
	 * @return \Aimeos\Base\MQueue\Message\Iface|null Message object or null if none is available
	 */
	public function get() : ?\Aimeos\Base\MQueue\Message\Iface
	{
		if( $this->timeout !== null ) {
			$job = $this->client->reserveWithTimeout( $this->timeout );
		} else {
			$job = $this->client->reserve();
		}

		if( $job !== null ) {
			return new \Aimeos\Base\MQueue\Message\Beanstalk( $job );
		}

		return null;
	}
}
