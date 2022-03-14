<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */


namespace Aimeos\Base\MQueue;


class Beanstalk extends Base implements Iface
{
	private $client;
	private $queues = [];


	/**
	 * Initializes the message queue object
	 *
	 * @param array $config Associative list of configuration key/value pairs
	 */
	public function __construct( array $config )
	{
		parent::__construct( $config );

		$host = $this->getConfig( 'host', 'localhost' );
		$port = $this->getConfig( 'port', \Pheanstalk\PheanstalkInterface::DEFAULT_PORT );

		if( is_array( $host ) )
		{
			foreach( $host as $idx => $entry )
			{
				$iport = ( is_array( $port) ? $port[$idx] : $port );
				$this->client = $this->connect( $entry, $iport );

				if( $this->client instanceof \Pheanstalk\PheanstalkInterface ) {
					break;
				}
			}
		}
		else
		{
			$this->client = $this->connect( $host, $port );
		}

		if( $this->client instanceof \Pheanstalk\Exception ) {
			throw new \Aimeos\Base\MQueue\Exception( $this->client->getMessage() );
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
			$timeout = $this->getConfig( 'readtimeout', 30 );
			$this->queues[$name] = new \Aimeos\Base\MQueue\Queue\Beanstalk( $this->client, $name, $timeout );
		}

		return $this->queues[$name];
	}


	/**
	 * Opens a connection to the message queue server
	 *
	 * @param string $host Host name or IP address
	 * @param int $port Port the server is listening
	 * @return \Pheanstalk\PheanstalkInterface
	 */
	protected function connect( string $host, int $port ) : \Pheanstalk\PheanstalkInterface
	{
		$conntimeout = $this->getConfig( 'conntimeout', 3 );
		$persist = $this->getConfig( 'persist', false );

		return new \Pheanstalk\Pheanstalk( $host, $port, $conntimeout, $persist );
	}
}
