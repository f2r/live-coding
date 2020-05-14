#!/usr/bin/php
<?php

$f = fsockopen('127.0.0.1', 13374);
fputs($f, serialize(['command' => 'stop']));
fclose($f);


