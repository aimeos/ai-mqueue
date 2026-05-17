<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 */


namespace Aimeos\Base\MQueue\Message;


class Stomp implements Iface
{
	private \Stomp\Transport\Frame $msg;


	/**
	 * Initializes the message object
	 *
	 * @param \Stomp\Transport\Frame $msg Stomp frame object
	 */
	public function __construct( \Stomp\Transport\Frame $msg )
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
		return (string) $this->msg->body;
	}


	/**
	 * Returns the original message object
	 *
	 * @return \Stomp\Transport\Frame Stomp frame object
	 */
	public function object() : \Stomp\Transport\Frame
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
		return (string) $this->msg->body;
	}
}
