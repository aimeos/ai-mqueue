<?php

namespace Aimeos\MW\MQueue;


class AMQP extends Base implements Iface
{
	private $conn;
	private $queues = array();


	/**
	 * Initializes the message queue object
	 *
	 * @param array $config Associative list of configuration key/value pairs
	 */
	public function __construct( array $config )
	{
		parent::__construct( $config );

		$host = $this->getConfig( 'host', 'localhost' );
		$port = $this->getConfig( 'port', 5672 );
		$user = $this->getConfig( 'username', 'guest' );
		$pass = $this->getConfig( 'password', 'guest' );

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
			throw new \Aimeos\MW\MQueue\Exception( $this->conn->getMessage() );
		}
	}


	/**
	 * Closes the open connection
	 */
	public function __destruct()
	{
		$this->conn->close();
	}


	/**
	 * Returns the queue for the given name
	 *
	 * @param string $name Queue name
	 * @return \Aimeos\MW\MQueue\Queue\Iface Message queue
	 */
	public function getQueue( $name )
	{
		try
		{
			if( !isset( $this->queues[$name] ) ) {
				$this->queues[$name] = new \Aimeos\MW\MQueue\Queue\AMQP( $this->getChannel(), $name );
			}

			return $this->queues[$name];
		}
		catch( \Exception $e )
		{
			throw new \Aimeos\MW\MQueue\Exception( $e->getMessage() );
		}
	}


	/**
	 * Opens a connection to the message queue server
	 *
	 * @param string $host Host name or IP address
	 * @param integer $port Port the server is listening
	 * @param string $user User name for authentication
	 * @param string $pass Password for authentication
	 * @return \PhpAmqpLib\Connection\AMQPStreamConnection|\PhpAmqpLib\Exception\AMQPException
	 */
	protected function connect( $host, $port, $user, $pass )
	{
		$vhost = $this->getConfig( 'vhost', '/' );
		$insist = $this->getConfig( 'insist', false );
		$loginMethod = $this->getConfig( 'login_method', 'AMQPLAIN' );
		$loginResponse = $this->getConfig( 'login_response', null );
		$locale = $this->getConfig( 'locale', 'en_US' );
		$conntimeout = $this->getConfig( 'connection_timeout', 3.0 );
		$timeout = $this->getConfig( 'read_write_timeout', 3.0 );
		$keepalive = $this->getConfig( 'keepalive', false );
		$heartbeat = $this->getConfig( 'heartbeat', 0 );

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
