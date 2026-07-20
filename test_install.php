<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$data = [
    'db_host' => '127.0.0.1',
    'db_database' => 'cosmiclib',
    'db_username' => 'root',
    'db_password' => '',
    'admin_name' => 'Admin Test',
    'admin_email' => 'admin@test.com',
    'admin_password' => 'password123',
    'school_name' => 'Test School',
];

echo "Testing InstallerEngine...\n";

try {
    $eng = new \App\Services\InstallerEngine();
    echo "isInstalled: " . ($eng->isInstalled() ? 'YES' : 'NO') . "\n";
    
    echo "Running install...\n";
    $eng->install($data);
    
    echo "isInstalled after: " . ($eng->isInstalled() ? 'YES' : 'NO') . "\n";
    echo "SUCCESS!\n";
} catch (\Throwable $e) {
    echo "ERROR: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}