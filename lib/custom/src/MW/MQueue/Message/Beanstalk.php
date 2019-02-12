<?php

namespace Aimeos\MW\MQueue\Message;


class Beanstalk implements Iface
{
	private $job;


	/**
	 * Initializes the message object
	 *
	 * @param \Pheanstalk\Job $job Job object
	 */
	public function __construct( \Pheanstalk\Job $job )
	{
		$this->job = $job;
	}


	/**
	 * Returns the message body
	 *
	 * @return string Message body
	 */
	public function getBody()
	{
		return $this->job->getData();
	}


	/**
	 * Returns the original message object
	 *
	 * @return \Pheanstalk\Job Job object
	 */
	public function getObject()
	{
		return $this->job;
	}
}
