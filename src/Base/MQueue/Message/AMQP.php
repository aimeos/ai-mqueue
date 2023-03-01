<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Base\MQueue\Message;


class AMQP implements Iface
{
	private \PhpAmqpLib\Message\AMQPMessage $msg;


	/**
	 * Initializes the message object
	 *
	 * @param \PhpAmqpLib\Message\AMQPMessage $msg AMQPMessage object
	 */
	public function __construct( \PhpAmqpLib\Message\AMQPMessage $msg )
	{
		$this->msg = $msg;
	}


	/**
	 * Returns the message body
	 *
	 * @return string Message body
	 */
	public function getBody() : string
	{
		return $this->msg->body;
	}


	/**
	 * Returns the original message object
	 *
	 * @return \PhpAmqpLib\Message\AMQPMessage AMQPMessage object
	 */
	public function object() : \PhpAmqpLib\Message\AMQPMessage
	{
		return $this->msg;
	}
}
