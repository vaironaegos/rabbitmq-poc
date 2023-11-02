<?php

declare(strict_types=1);

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require __DIR__ . '/vendor/autoload.php';

date_default_timezone_set('America/Sao_Paulo');

[, $queueName] = explode('=', $argv[2]);

$connection = new AMQPStreamConnection('mail-service-rabbitmq', '5672', 'guest', 'guest');
$channel = $connection->channel();

$channel->exchange_declare('my-exchange', AMQP_EX_TYPE_DIRECT, false, true, false);
$channel->queue_declare($queueName, false, true, false, false);
$channel->queue_bind($queueName, 'my-exchange', $queueName);

$channel->basic_consume($queueName, '', false, false, false, false, function(AMQPMessage $message) {
    echo $message->body . PHP_EOL;
    $message->getChannel()->basic_ack($message->getDeliveryTag());
});

while ($channel->is_open()) {
    $channel->wait();
}