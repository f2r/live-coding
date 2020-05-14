#!/usr/bin/php
<?php

spl_autoload_register(function ($class) {
    preg_match('`(.+)\\\(\w+)$`', $class, $match);
    eval('namespace '.$match[1].'{class '.$match[2].' extends \RemoteClass {}}');
});

class RemoteClass {
    /**
     * @var mixed
     */
    private $instance;

    public function __construct()
    {
        $this->instance = $this->callRemote([
            'class' => get_class($this),
        ]);
    }

    public function __call($name, $arguments)
    {
        return $this->callRemote([
            'instance' => $this->instance,
            'call' => $name,
            'arguments' => $arguments,
        ]);
    }

    public function __set($name, $value)
    {
        $this->callRemote([
                'instance' => $this->instance,
                'set' => $name,
                'value' => $value,
        ]);
    }

    public function __get($name)
    {
        return $this->callRemote([
                'instance' => $this->instance,
                'get' => $name,
        ]);
    }

    public function __destruct()
    {
        $this->callRemote([
            'destruct' => $this->instance,
        ]);
    }


    private function callRemote(array $data)
    {
        $f = fsockopen('127.0.0.1', 13374);
        fputs($f, serialize($data));
        $result = unserialize(fread($f, 32678));
        fclose($f);
        if (isset($result['exception'])) {
            throw new \RuntimeException($result['exception']);
        }
        return $result['return'] ?? null;
    }

}

$object = new App\Belette();
$object->setText('Bonjour à tous');
$object->revert();
echo $object->getText(), "\n";

$object->x = 42;
var_dump($object->x++);
var_dump($object->x++);

