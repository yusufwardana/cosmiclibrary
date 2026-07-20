<?php
// Simulate browser flow: hit each step in turn with a cookie jar, dump session after each.
$jar = __DIR__.'/cookies_install.txt';
@unlink($jar);

$base = 'http://localhost/install';
$steps = [
    'GET'  => ['/database'],
    'POST' => ['/database'],
    'GET'  => ['/admin'],
    'POST' => ['/admin'],
    'GET'  => ['/school'],
    'POST' => ['/school'],
    'GET'  => ['/smtp'],
    'POST' => ['/smtp'],
    'GET'  => ['/confirm'],
];

$postDb = [
    'db_host' => '127.0.0.1',
    'db_port' => '3306',
    'db_database' => 'cosmiclib',
    'db_username' => 'root',
    'db_password' => '',
    '_token' => '',
];
$postAdmin = [
    'name' => 'Admin',
    'email' => 'admin@test.id',
    'password' => 'secret123',
    'password_confirmation' => 'secret123',
    '_token' => '',
];
$postSchool = [
    'school_name' => 'SMA Test',
    'school_address' => 'Jl. Test',
    'school_phone' => '0812',
    'school_email' => 's@test.id',
    '_token' => '',
];
$postSmtp = [
    'mail_driver' => 'log',
    'mail_from_address' => 'a@b.c',
    'mail_from_name' => 'X',
    '_token' => '',
];

$nextPost = null;
foreach ($steps as $i => [$method, $path]) {
    $url = $base.$path[0];
    echo "=== $method $url ===\n";
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => true,
        CURLOPT_COOKIEJAR      => $jar,
        CURLOPT_COOKIEFILE     => $jar,
        CURLOPT_FOLLOWLOCATION => false,
    ]);
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        $body = match ($path[0]) {
            '/database' => $postDb,
            '/admin'    => $postAdmin,
            '/school'   => $postSchool,
            '/smtp'     => $postSmtp,
        };
        $body['_token'] = $nextPost ?? '';
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
    }
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($resp, 0, $headerSize);
    $body = substr($resp, $headerSize);
    echo "Status: $code\n";
    if (preg_match('/location:\s*(.+)/i', $headers, $m)) {
        echo "Redirect: " . trim($m[1]) . "\n";
    }
    if ($code === 200 && $method === 'GET' && $path[0] === '/database') {
        if (preg_match('/<input[^>]+name="_token"[^>]+value="([^"]+)"/', $body, $m)) {
            $nextPost = $m[1];
            echo "Got token: " . substr($nextPost, 0, 10) . "...\n";
        }
    }
    if ($code === 419) {
        echo "CSRF MISMATCH at $path[0]\n";
        break;
    }
    curl_close($ch);
}