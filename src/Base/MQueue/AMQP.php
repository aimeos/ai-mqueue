<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2025
 */


namespace Aimeos\Base\MQueue;


class AMQP extends Base implements Iface
{
	private $conn;
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
		$port = $this->config( 'port', 5672 );
		$user = $this->config( 'username', 'guest' );
		$pass = $this->config( 'password', 'guest' );

		if( is_array( $host ) )
		{
			foreach( $host as $idx => $entry )
			{
				$iport = ( is_array( $port) ? $port[$idx] : $port );
				$iuser = ( is_array( $user) ? $user[$idx] : $user );
				$ipass = ( is_array( $pass) ? $pass[$idx] : $pass );

				$this->conn = $this->connect( $entry, $iport, $iuser, $ipass );

				if( $this->conn instanceof \PhpAmqpLib\Connection\AMQPStreamConnection ) {
					break;
				}
			}
		}
		else
		{
			$this->conn = $this->connect( $host, $port, $user, $pass );
		}

		if( $this->conn instanceof \Exception ) {
			throw new \Aimeos\Base\MQueue\Exception( $this->conn->getMessage() );
		}
	}


	/**
	 * Closes the open connection
	 */
	public function __destruct()
	{
		if( $this->conn ) {
			$this->conn->close();
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
		try
		{
			if( !isset( $this->queues[$name] ) ) {
				$this->queues[$name] = new \Aimeos\Base\MQueue\Queue\AMQP( $this->getChannel(), $name );
			}

			return $this->queues[$name];
		}
		catch( \Exception $e )
		{
			throw new \Aimeos\Base\MQueue\Exception( $e->getMessage() );
		}
	}


	/**
	 * Opens a connection to the message queue server
	 *
	 * @param string $host Host name or IP address
	 * @param int $port Port the server is listening
	 * @param string $user User name for authentication
	 * @param string $pass Password for authentication
	 * @return \PhpAmqpLib\Connection\AMQPStreamConnection|\PhpAmqpLib\Exception\AMQPException
	 */
	protected function connect( string $host, int $port, string $user, string $pass )
	{
		$vhost = $this->config( 'vhost', '/' );
		$insist = $this->config( 'insist', false );
		$loginMethod = $this->config( 'login_method', 'AMQPLAIN' );
		$loginResponse = $this->config( 'login_response', null );
		$locale = $this->config( 'locale', 'en_US' );
		$conntimeout = $this->config( 'connection_timeout', 3.0 );
		$timeout = $this->config( 'read_write_timeout', 3.0 );
		$keepalive = $this->config( 'keepalive', false );
		$heartbeat = $this->config( 'heartbeat', 0 );

		try
		{
			return new \PhpAmqpLib\Connection\AMQPStreamConnection(
				$host, $port, $user, $pass,
				$vhost, $insist, $loginMethod, $loginResponse,
				$locale, $conntimeout, $timeout, null,
				$keepalive, $heartbeat
			);
		}
		catch( \Exception $e )
		{
			return $e;
		}
	}


	/**
	 * Returns a new AMQP channel
	 *
	 * @return \PhpAmqpLib\Connection\AMQPChannel AMQP channel
	 */
	protected function getChannel()
	{
		return $this->conn->channel();
	}
}
