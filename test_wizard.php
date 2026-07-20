<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

function req($kernel, $method, $uri, array $data = []) {
    $request = Illuminate\Http\Request::create($uri, $method, $data);
    $request->headers->set('Cookie', $_COOKIE['laravel_session'] ?? '');
    $response = $kernel->handle($request);
    foreach ($response->headers->getCookies() as $c) {
        if ($c->getName() === 'laravel_session') {
            $_COOKIE['laravel_session'] = $c->getValue();
        }
    }
    echo "$method $uri => " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() === 302) {
        echo "  Location: " . $response->headers->get('Location') . "\n";
        $flash = $response->getSession()?->get('error');
        if ($flash) echo "  Flash error: $flash\n";
    }
    $kernel->terminate($request, $response);
    return $response;
}

echo "-- Welcome --\n";
req($kernel, 'GET', '/install');

echo "-- GET License --\n";
req($kernel, 'GET', '/install/license');

echo "-- Accept License --\n";
req($kernel, 'POST', '/install/license');

echo "-- GET Requirements --\n";
req($kernel, 'GET', '/install/requirements');

echo "-- Verify Requirements --\n";
req($kernel, 'POST', '/install/requirements');

echo "-- GET Database --\n";
req($kernel, 'GET', '/install/database');

echo "-- POST Database (bad) --\n";
req($kernel, 'POST', '/install/database', []);

echo "-- POST Database (ok) --\n";
req($kernel, 'POST', '/install/database', [
    'db_host' => '127.0.0.1',
    'db_port' => '3306',
    'db_database' => 'cosmiclib',
    'db_username' => 'root',
    'db_password' => '',
]);

echo "-- GET Admin --\n";
req($kernel, 'GET', '/install/admin');

echo "-- POST Admin --\n";
req($kernel, 'POST', '/install/admin', [
    'name' => 'Admin',
    'email' => 'admin@t.id',
    'password' => 'secret123',
    'password_confirmation' => 'secret123',
]);

echo "-- GET School --\n";
req($kernel, 'GET', '/install/school');

echo "-- POST School --\n";
req($kernel, 'POST', '/install/school', [
    'school_name' => 'SMA Test',
    'school_address' => 'Jl. Test',
    'school_phone' => '0812',
    'school_email' => 's@t.id',
]);

echo "-- GET Smtp --\n";
req($kernel, 'GET', '/install/smtp');

echo "-- POST Smtp --\n";
req($kernel, 'POST', '/install/smtp', [
    'mail_driver' => 'log',
    'mail_from_address' => 'a@b.c',
    'mail_from_name' => 'X',
]);

echo "-- GET Confirm --\n";
req($kernel, 'GET', '/install/confirm');