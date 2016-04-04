<?php

namespace Aimeos\MW\MQueue;


class Stomp extends Base implements Iface
{
	private $queues = array();


	/**
	 * Returns the queue for the given name
	 *
	 * @param string $name Queue name
	 * @return \Aimeos\MW\MQueue\Queue\Iface Message queue
	 */
	public function getQueue( $name )
	{
		if( !isset( $this->queues[$name] ) )
		{
			$uri = $this->getConfig( 'uri', 'tcp://localhost:61613' );
			$user = $this->getConfig( 'username', null );
			$pass = $this->getConfig( 'password', null );

			if( is_array( $uri ) )
			{
				foreach( $uri as $idx => $entry )
				{
					$iuser = ( is_array( $user) ? $user[$idx] : $user );
					$ipass = ( is_array( $pass) ? $pass[$idx] : $pass );

					$result = $this->connect( $entry, $iuser, $ipass );

					if( $result instanceof \Stomp ) {
						break;
					}
				}
			}
			else
			{
				$result = $this->connect( $uri, $user, $pass );
			}

			if( $result instanceof \StompException ) {
				throw new \Aimeos\MW\MQueue\Exception( $result->getMessage() );
			}

			$this->queues[$name] = new \Aimeos\MW\MQueue\Queue\Stomp( $result, $name );
		}

		return $this->queues[$name];
	}


	/**
	 * Opens a connection to the message queue server
	 *
	 * @param string $uri Connection URI
	 * @param string $user User name for authentication
	 * @param string $pass Password for authentication
	 * @return \Stomp|\StompException
	 */
	protected function connect( $uri, $user, $pass )
	{
		try {
			return new \Stomp( $uri, $user, $pass );
		} catch( \StompException $e ) {
			return $e;
		}
	}
}