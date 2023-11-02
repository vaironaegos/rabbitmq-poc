<?php

declare(strict_types=1);

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('America/Sao_Paulo');

$connection = new AMQPStreamConnection('mail-service-rabbitmq', '5672', 'guest', 'guest');
$channel = $connection->channel();

$channel->exchange_declare('my-exchange', AMQP_EX_TYPE_DIRECT, false, true, false);
$channel->queue_declare('mailer', false, true, false, false);
$channel->queue_declare('xpto', false, true, false, false);
$channel->queue_declare('abc', false, true, false, false);

$channel->queue_bind('mailer', 'my-exchange', 'mailer');
$channel->queue_bind('xpto', 'my-exchange', 'xpto');
$channel->queue_bind('abc', 'my-exchange', 'abc');

$message1 = json_encode([
    'action' => 'send-mail',
    'id' => uniqid(),
    'name' => 'Pietro Coelho - mailer',
    'email' => 'pietro@astrotech.solutions',
]);

$message2 = json_encode([
    'action' => 'send-mail',
    'id' => uniqid(),
    'name' => 'Pietro Coelho - XPTO',
    'email' => 'pietro@astrotech.solutions',
]);

$message3 = json_encode([
    'action' => 'send-mail',
    'id' => uniqid(),
    'name' => 'Pietro Coelho - ABC',
    'email' => 'pietro@astrotech.solutions',
]);

$channel->basic_publish(msg: new AMQPMessage($message1), exchange: 'my-exchange', routing_key: 'mailer');
$channel->basic_publish(msg: new AMQPMessage($message2), exchange: 'my-exchange', routing_key: 'xpto');
$channel->basic_publish(msg: new AMQPMessage($message3), exchange: 'my-exchange', routing_key: 'abc');

echo 'Conectado no RabbitMQ!';
