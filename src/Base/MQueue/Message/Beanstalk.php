<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Base\MQueue\Message;


class Beanstalk implements Iface
{
	private \Pheanstalk\Job $job;


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
	public function getBody() : string
	{
		return $this->job->getData();
	}


	/**
	 * Returns the original message object
	 *
	 * @return \Pheanstalk\Job Job object
	 */
	public function object() : \Pheanstalk\Job
	{
		return $this->job;
	}
}
