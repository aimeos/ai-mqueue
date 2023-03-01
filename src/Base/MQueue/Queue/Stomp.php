<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Base\MQueue\Queue;


class Stomp implements Iface
{
	private \Stomp\Stomp $client;
	private string $queue;


	/**
	 * Initializes the message queue class
	 *
	 * @param \Stomp\Stomp $client Stomp object
	 * @param string $queue Message queue name
	 * @throws \Aimeos\Base\MQueue\Exception
	 */
	public function __construct( \Stomp\Stomp $client, string $queue )
	{
		if( $client->subscribe( $queue ) === false ) {
			throw new \Aimeos\Base\MQueue\Exception( sprintf( 'Unable to subscribe to queue "%1$s"', $queue ) );
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
	 * @return \Aimeos\Base\MQueue\Queue\Iface MQueue queue instance for method chaining
	 * @throws \Aimeos\Base\MQueue\Exception
	 */
	public function add( string $msg ) : \Aimeos\Base\MQueue\Queue\Iface
	{
		if( $this->client->send( $this->queue, $msg ) === false )
		{
			$msg = sprintf( 'Sending message to queue "%1$s" failed: ' . $msg, $this->queue );
			throw new \Aimeos\Base\MQueue\Exception( $msg );
		}

		return $this;
	}


	/**
	 * Removes the message from the queue
	 *
	 * @param \Aimeos\Base\MQueue\Message\Iface $msg Message object
	 * @return \Aimeos\Base\MQueue\Iface MQueue instance for method chaining
	 * @throws \Aimeos\Base\MQueue\Exception
	 */
	public function del( \Aimeos\Base\MQueue\Message\Iface $msg ) : \Aimeos\Base\MQueue\Queue\Iface
	{
		if( $this->client->ack( $msg->object() ) === false ) {
			throw new \Aimeos\Base\MQueue\Exception( 'Couldn\'t acknowledge frame: ' . $msg->getBody() );
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
		try
		{
			if( $this->client->hasFrameToRead() && ( $msg = $this->client->readFrame() ) !== false ) {
				return new \Aimeos\Base\MQueue\Message\Stomp( $msg );
			}
		}
		catch( \Exception $e )
		{
			throw new \Aimeos\Base\MQueue\Exception( $e->getMessage() );
		}

		return null;
	}
}
