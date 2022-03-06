<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */


namespace Aimeos\MW\MQueue\Queue;


class Beanstalk implements Iface
{
	private $client;
	private $queue;
	private $timeout;


	/**
	 * Initializes the queue object
	 *
	 * @param \Pheanstalk\PheanstalkInterface $client Client object
	 * @param string $queue Message queue name
	 * @param int $timeout Number of seconds until the message is passed to another client
	 * @throws \Aimeos\MW\MQueue\Exception
	 */
	public function __construct( \Pheanstalk\PheanstalkInterface $client, string $queue, $timeout = null )
	{
		try {
			$client->useTube( $queue )->watch( $queue );
		} catch( \Exception $e ) {
			throw new \Aimeos\MW\MQueue\Exception( $e->getMessage() );
		}

		$this->client = $client;
		$this->queue = $queue;
		$this->timeout = $timeout;
	}


	/**
	 * Adds a new message to the message queue
	 *
	 * @param string $msg Message, e.g. JSON encoded data
	 * @return \Aimeos\MW\MQueue\Queue\Iface MQueue queue instance for method chaining
	 */
	public function add( string $msg ) : \Aimeos\MW\MQueue\Queue\Iface
	{
		try {
			$this->client->put( $msg );
		} catch( \Exception $e ) {
			throw new \Aimeos\MW\MQueue\Exception( $e->getMessage() );
		}

		return $this;
	}


	/**
	 * Removes the message from the queue
	 *
	 * @param \Aimeos\MW\MQueue\Message\Iface $msg Message object
	 * @return \Aimeos\MW\MQueue\Queue\Iface MQueue queue instance for method chaining
	 */
	public function del( \Aimeos\MW\MQueue\Message\Iface $msg ) : \Aimeos\MW\MQueue\Queue\Iface
	{
		try {
			$this->client->delete( $msg->object() );
		} catch( \Exception $e ) {
			throw new \Aimeos\MW\MQueue\Exception( $e->getMessage() );
		}

		return $this;
	}


	/**
	 * Returns the next message from the queue
	 *
	 * @return \Aimeos\MW\MQueue\Message\Iface|null Message object or null if none is available
	 */
	public function get() : ?\Aimeos\MW\MQueue\Message\Iface
	{
		if( ( $job = $this->client->reserve( $this->timeout ) ) !== false ) {
			return new \Aimeos\MW\MQueue\Message\Beanstalk( $job );
		}

		return null;
	}
}
