<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

$session = app('session.store');
$session->put(['installer.db_host' => 'localhost']);
var_dump($session->get('installer'));
var_dump($session->all());