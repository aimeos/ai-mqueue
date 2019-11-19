<?php

namespace Aimeos\MW\MQueue\Message;


class Stomp implements Iface
{
	private $msg;


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
	public function getObject() : \Stomp\Message
	{
		return $this->msg;
	}
}
