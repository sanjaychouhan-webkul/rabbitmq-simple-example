<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPMailer\PHPMailer\PHPMailer;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n\n";

$callback = function ($msg) {
    sendEmail($msg->body);
    echo ' [x] Message Consumed '."\n\n";
    echo " [*] Waiting for messages. To exit press CTRL+C\n\n";
};

$channel->basic_consume('hello', '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();


function sendEmail($data)
{
    $data = json_decode($data,true);
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'YOUR_HOST';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = 'USERNAME';
    $mail->Password = 'PASSWORD';
    $mail->setFrom('example@example.com', 'Admin');
    $mail->addAddress($data['to'], $data['name']);
    $mail->Subject = $data['subject'];
    $mail->Body = $data['body'];
    $mail->send();
}
?>