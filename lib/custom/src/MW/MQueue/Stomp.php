<?php

namespace Aimeos\MW\MQueue;


class Stomp extends Base implements Iface
{
	private $queues = [];


	/**
	 * Returns the queue for the given name
	 *
	 * @param string $name Queue name
	 * @return \Aimeos\MW\MQueue\Queue\Iface Message queue
	 */
	public function getQueue( string $name ) : \Aimeos\MW\MQueue\Queue\Iface
	{
		if( !isset( $this->queues[$name] ) )
		{
			try {
				$client = $this->connect();
			} catch( \Exception $e ) {
				throw new \Aimeos\MW\MQueue\Exception( $e->getMessage() );
			}

			$this->queues[$name] = new \Aimeos\MW\MQueue\Queue\Stomp( $client, $name );
		}

		return $this->queues[$name];
	}


	/**
	 * Creates a connection to the Stomp server
	 *
	 * @return \Stomp\Stomp Stomp client
	 */
	protected function connect()
	{
		$uri = $this->getConfig( 'uri', 'tcp://localhost:61613' );
		$user = $this->getConfig( 'username', null );
		$pass = $this->getConfig( 'password', null );

		$stomp = new \Stomp\Stomp( $uri );
		$stomp->connect( $user, $pass );

		return $stomp;
	}
}
