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
	public function getBody()
	{
		return $this->msg->body;
	}


	/**
	 * Returns the original message object
	 *
	 * @return \StompFrame Stomp frame object
	 */
	public function getObject()
	{
		return $this->msg;
	}
}