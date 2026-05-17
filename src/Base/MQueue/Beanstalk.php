<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 */


namespace Aimeos\Base\MQueue;


class Beanstalk extends Base implements Iface
{
	private \Pheanstalk\Pheanstalk $client;
	private array $queues = [];


	/**
	 * Initializes the message queue object
	 *
	 * @param array $config Associative list of configuration key/value pairs
	 */
	public function __construct( array $config )
	{
		parent::__construct( $config );

		$host = $this->config( 'host', 'localhost' );
		$port = $this->config( 'port', 11300 );

		if( is_array( $host ) )
		{
			foreach( $host as $idx => $entry )
			{
				$iport = ( is_array( $port) ? $port[$idx] : $port );
				// @phpstan-ignore argument.type, argument.type
				$this->client = $this->connect( $entry, $iport );
				break;
			}
		}
		else
		{
			// @phpstan-ignore argument.type, argument.type
			$this->client = $this->connect( $host, $port );
		}
	}


	/**
	 * Returns the queue for the given name
	 *
	 * @param string $name Queue name
	 * @return \Aimeos\Base\MQueue\Queue\Iface Message queue
	 */
	public function getQueue( string $name ) : \Aimeos\Base\MQueue\Queue\Iface
	{
		if( !isset( $this->queues[$name] ) )
		{
			$timeout = $this->config( 'readtimeout', 30 );
			// @phpstan-ignore argument.type
			$this->queues[$name] = new \Aimeos\Base\MQueue\Queue\Beanstalk( $this->client, $name, $timeout );
		}

		// @phpstan-ignore return.type
		return $this->queues[$name];
	}


	/**
	 * Opens a connection to the message queue server
	 *
	 * @param string $host Host name or IP address
	 * @param int $port Port the server is listening
	 * @return \Pheanstalk\Pheanstalk
	 */
	protected function connect( string $host, int $port ) : \Pheanstalk\Pheanstalk
	{
		$conntimeout = $this->config( 'conntimeout', 3 );

		// @phpstan-ignore argument.type
		return \Pheanstalk\Pheanstalk::create( $host, $port, new \Pheanstalk\Values\Timeout( $conntimeout ) );
	}
}
