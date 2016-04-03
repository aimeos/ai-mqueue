<?php

namespace Aimeos\MW\MQueue\Queue;


class Stomp implements Iface
{
	private $client;
	private $queue;


	/**
	 * Initializes the message queue class
	 *
	 * @param \Stomp $client Stomp object
	 * @param string $queue Message queue name
	 * @throws \Aimeos\MW\MQueue\Exception
	 */
	public function __construct( \Stomp $client, $queue )
	{
		if( $client->subscribe( $queue ) === false ) {
			throw new \Aimeos\MW\MQueue\Exception( $client->error() );
		}

		$this->client = $client;
		$this->queue = $queue;
	}


	/**
	 * Unsubscribes from the queue on cleanup
	 */
	public function __destruct()
	{
		$this->client->unsubscribe( $this->queue );
	}


	/**
	 * Adds a new message to the message queue
	 *
	 * @param string $msg Message, e.g. JSON encoded data
	 * @throws \Aimeos\MW\MQueue\Exception
	 */
	public function add( $msg )
	{
		if( $this->client->send( $this->queue, $msg ) === false ) {
			throw new \Aimeos\MW\MQueue\Exception( $this->client->error() );
		}
	}


	/**
	 * Removes the message from the queue
	 *
	 * @param \Aimeos\MW\MQueue\Message\Iface $msg Message object
	 * @throws \Aimeos\MW\MQueue\Exception
	 */
	public function del( \Aimeos\MW\MQueue\Message\Iface $msg )
	{
		if( $this->client->ack( $msg->getObject() ) === false ) {
			throw new \Aimeos\MW\MQueue\Exception( $this->client->error() );
		}
	}


	/**
	 * Returns the next message from the queue
	 *
	 * @return \Aimeos\MW\MQueue\Message\Iface|null Message object or null if none is available
	 * @throws \Aimeos\MW\MQueue\Exception
	 */
	public function get()
	{
		try
		{
			if( $this->client->hasFrame() ) {
				return new \Aimeos\MW\MQueue\Message\Stomp( $this->client->readFrame() );
			}
		}
		catch( \StompException $e )
		{
			throw new \Aimeos\MW\MQueue\Exception( $e->getMessage() );
		}
	}
}