<?php
declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

$socket = socket_create_listen(13374);

$objects = [];
$running = true;
while($running && $client = socket_accept($socket)) {
    $data = unserialize(socket_read($client, 32678));
    if (isset($data['command']) and $data['command'] === 'stop') {
        $running = false;
        break;
    }

    echo "Received:\n";
    var_dump($data);
    echo "==========\n";

    $instance = null;
    if (isset($data['instance'])) {
        $instance = $objects[$data['instance']];
    }
    $response = [];
    try {
        if (isset($data['destruct'])) {
            unset($objects[$data['destruct']]);
            echo 'Object destroyed', "\n";
            continue;
        }

        if ($instance === null and isset($data['class'])) {
            $instance = new $data['class'];
            $response['return'] = spl_object_hash($instance);
            $objects[$response['return']] = $instance;
        }

        if ($instance === null) {
            throw new \RuntimeException('Instance not found');
        }

        if (isset($data['call'])) {
            $instance = [$instance, $data['call']];
            $response['return'] = call_user_func($instance, ...$data['arguments']);
        } elseif (isset($data['set'], $data['value'])) {
            $instance->{$data['set']} = $data['value'];
        } elseif (isset($data['get'])) {
            $response['return'] = $instance->{$data['get']};
        }
    } catch(\Throwable $exception) {
        $response['exception'] = $exception->getMessage();
    } finally {
        socket_write($client, serialize($response));
        socket_close($client);

        echo "Sent:\n";
        var_dump($response);
        echo "==========\n";
    }
}

socket_close($socket);