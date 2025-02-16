<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2025
 */


namespace Aimeos\Base\MQueue\Message;


class Stomp implements Iface
{
	private \Stomp\Message $msg;


	/**
	 * Initializes the message object
	 *
	 * @param \Stomp\Message $msg Stomp message object
	 */
	public function __construct( \Stomp\Message $msg )
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
	 * @return \Stomp\Message Stomp message object
	 */
	public function object() : \Stomp\Message
	{
		return $this->msg;
	}


	/**
	 * Returns the message body
	 *
	 * @return string Message body
	 */
	public function __toString() : string
	{
		return $this->msg->body;
	}
}
