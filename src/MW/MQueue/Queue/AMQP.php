<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */


namespace Aimeos\MW\MQueue\Queue;


class AMQP implements Iface
{
	private $channel;
	private $queue;


	/**
	 * Initializes the message queue class
	 *
	 * @param \PhpAmqpLib\Channel\AMQPChannel $channel AMQP channel
	 * @param string $queue Message queue name
	 * @throws \Aimeos\MW\MQueue\Exception
	 */
	public function __construct( \PhpAmqpLib\Channel\AMQPChannel $channel, string $queue )
	{
		try
		{
			$channel->queue_declare( $queue, false, true, false, false );
			$channel->basic_qos( null, 1, null );
		}
		catch( \Exception $e )
		{
			throw new \Aimeos\MW\MQueue\Exception( $e->getMessage() );
		}

		$this->channel = $channel;
		$this->queue = $queue;
	}


	/**
	 * Closes the channel on cleanup
	 */
	public function __destruct()
	{
		try {
			$this->channel->close();
		} catch( \Exception $e ) { ; }
	}


	/**
	 * Adds a new message to the message queue
	 *
	 * @param string $msg Message, e.g. JSON encoded data
	 * @return \Aimeos\MW\MQueue\Queue\Iface MQueue queue instance for method chaining
	 * @throws \Aimeos\MW\MQueue\Exception
	 */
	public function add( string $msg ) : \Aimeos\MW\MQueue\Queue\Iface
	{
		try
		{
			$message = new \PhpAmqpLib\Message\AMQPMessage( $msg, array( 'delivery_mode' => 2 ) );
			$this->channel->basic_publish( $message, '', $this->queue );
		}
		catch( \Exception $e )
		{
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
			$this->channel->basic_ack( $msg->object()->delivery_info['delivery_tag'] );
		} catch( \Exception $e ) {
			throw new \Aimeos\MW\MQueue\Exception( $e->getMessage() );
		}

		return $this;
	}


	/**
	 * Returns the next message from the queue
	 *
	 * @return \Aimeos\MW\MQueue\Message\Iface|null Message object or null if none is available
	 * @throws \Aimeos\MW\MQueue\Exception
	 */
	public function get() : ?\Aimeos\MW\MQueue\Message\Iface
	{
		try
		{
			if( ( $msg = $this->channel->basic_get( $this->queue ) ) !== null ) {
				return new \Aimeos\MW\MQueue\Message\AMQP( $msg );
			}
		}
		catch( \Exception $e )
		{
			throw new \Aimeos\MW\MQueue\Exception( $e->getMessage() );
		}

		return null;
	}
}