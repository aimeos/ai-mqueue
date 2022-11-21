<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */


namespace Aimeos\Base\MQueue;


class Stomp extends Base implements Iface
{
	private $queues = [];


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
			try {
				$client = $this->connect();
			} catch( \Exception $e ) {
				throw new \Aimeos\Base\MQueue\Exception( $e->getMessage() );
			}

			$this->queues[$name] = new \Aimeos\Base\MQueue\Queue\Stomp( $client, $name );
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
		$uri = $this->config( 'uri', 'tcp://localhost:61613' );
		$user = $this->config( 'username', null );
		$pass = $this->config( 'password', null );

		$stomp = new \Stomp\Stomp( $uri );
		$stomp->connect( $user, $pass );

		return $stomp;
	}
}
