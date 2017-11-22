<a href="https://aimeos.org/">
    <img src="https://aimeos.org/fileadmin/template/icons/logo.png" alt="Aimeos logo" title="Aimeos" align="right" height="60" />
</a>

Aimeos file system extension
===============================
[![Build Status](https://travis-ci.org/aimeos/ai-mqueue.svg?branch=master)](https://travis-ci.org/aimeos/ai-mqueue)
[![Coverage Status](https://coveralls.io/repos/aimeos/ai-mqueue/badge.svg?branch=master)](https://coveralls.io/r/aimeos/ai-mqueue?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aimeos/ai-mqueue/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aimeos/ai-mqueue/?branch=master)
[![License](https://poser.pugx.org/aimeos/ai-mqueue/license.svg)](https://packagist.org/packages/aimeos/ai-mqueue)

The Aimeos message queue extension contains adapter for pushing and retrieving
arbitrary messages to remote message queue servers to. Thus, processing of
resource intensive tasks can be postponed and offloaded to another server. This
is necessary for scaling really big setups.

## Table of contents

- [Installation](#installation)
- [Configuration](#configuration)
- [License](#license)
- [Links](#links)

## Installation

As every Aimeos extension, the easiest way is to install it via
[composer](https://getcomposer.org/). If you don't have composer installed yet,
you can execute this string on the command line to download it:
```
php -r "readfile('https://getcomposer.org/installer');" | php -- --filename=composer
```

Add the ai-mqueue extension name to the "require" section of your ```composer.json```
(or your ```composer.aimeos.json```, depending on what is available) file:
```
"require": [
    "aimeos/ai-mqueue": "dev-master",
    ...
],
```

Afterwards you only need to execute the composer update command on the command line:
```
composer update
```

These commands will install the Aimeos extension into the extension directory
and it will be available immediately.

## Configuration

All message queue adapters are configured below the ```resource/mq``` configuration
key, e.g. in the resource section of your config file:
```
'resource' => array(
	'mq' => array(
		// message queue adapter specific configuration
	),
),
```

### AMQP (RabbitMQ, Azure, Apache ActiveMQ + Qpid, MQlight and others)

To use the AMQP adapter, add this line to the `require` section of your
`composer.json` or (`composer.aimeos.json`) file:
```
"require": [
    "php-amqplib/php-amqplib": "~2.0",
    ...
],
```

The available configuration options are the one offered by the  `php-amqplib`
library:
```
'mq' => array(
	'adapter' => 'AMQP',
	'host' => 'localhost', // optional
	'port' => 5672, // optional
	'username' => 'guest', // optional
	'password' => 'guest', // optional
	'vhost' => '/', // optional
	'insist' => false, // optional
	'login_method' => 'AMQPLAIN', // optional
	'login_response' => null, // optional
	'locale' => 'en_US', // optional
	'connection_timeout' => 3.0, // optional
	'read_write_timeout' => 3.0, // optional
	'keepalive' => false, // optional
	'heartbeat' => 0, // optional
),
```

### Beanstalk

To use the Beanstalk adapter, add this line to the `require` section of your
`composer.json` or (`composer.aimeos.json`) file:
```
"require": [
    "pda/pheanstalk": "~3.0",
    ...
],
```

The available configuration options are the one offered by the  `pheanstalk`
library:
```
'mq' => array(
	'adapter' => 'Beanstalk',
	'host' => 'localhost', // optional
	'port' => 11300, // optional
	'conntimeout' => 3, // optional
	'readtimeout' => 30, // optional
	'persist' => false, // optional
),
```

### Stomp

To use the Stomp adapter, make sure you've installed the "stomp" PHP extension.
Most of the time there's already a package for the most widely used Linux
distributions available.

The available configuration options are:
```
'mq' => array(
	'adapter' => 'Stomp',
	'uri' => 'tcp://localhost:61613', // optional
	'username' => null, // optional
	'password' => null, // optional
),
```

## License

The Aimeos message queue extension is licensed under the terms of the LGPLv3
Open Source license and is available for free.

## Links

* [Web site](https://aimeos.org/)
* [Documentation](https://aimeos.org/docs)
* [Help](https://aimeos.org/help)
* [Issue tracker](https://github.com/aimeos/ai-mqueue/issues)
* [Source code](https://github.com/aimeos/ai-mqueue)
