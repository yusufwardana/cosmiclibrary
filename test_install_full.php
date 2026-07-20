<?php

declare(strict_types=1);

$base = 'http://127.0.0.1:8765';
$cookieFile = __DIR__ . '/test_cookie.txt';

@unlink($cookieFile);

$client = new class($base, $cookieFile) {
    public function __construct(
        private string $base,
        private string $cookieFile,
    ) {}

    public function get(string $path): string
    {
        $ch = curl_init($this->base . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEJAR => $this->cookieFile,
            CURLOPT_COOKIEFILE => $this->cookieFile,
            CURLOPT_HEADER => true,
            CURLOPT_TIMEOUT => 30,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        echo "GET {$path} -> HTTP {$httpCode}\n";
        return (string) $response;
    }

    public function post(string $path, array $data): string
    {
        $ch = curl_init($this->base . $path);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_COOKIEJAR => $this->cookieFile,
            CURLOPT_COOKIEFILE => $this->cookieFile,
            CURLOPT_HEADER => true,
            CURLOPT_TIMEOUT => 120,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        echo "POST {$path} -> HTTP {$httpCode}, final URL: {$effectiveUrl}\n";
        return (string) $response;
    }
};

function extractToken(string $html): ?string
{
    if (preg_match('/name="_token" value="([^"]+)"/', $html, $m)) {
        return $m[1];
    }
    if (preg_match('/<meta name="csrf-token" content="([^"]+)"/', $html, $m)) {
        return $m[1];
    }
    return null;
}

$welcome = $client->get('/install');
$token = extractToken($welcome);
echo "Welcome token: " . ($token ? 'found' : 'NOT FOUND') . "\n";
echo "Welcome first 400 chars:\n" . substr(preg_replace('/\r?\n/', ' ', $welcome), 0, 400) . "\n\n";

$page = $client->post('/install/license', [
    '_token' => $token,
    'license_accepted' => '1',
]);
$token = extractToken($page) ?: $token;

$page = $client->post('/install/requirements', ['_token' => $token]);
$token = extractToken($page) ?: $token;

$page = $client->post('/install/database', [
    '_token' => $token,
    'db_host' => '127.0.0.1',
    'db_port' => '3306',
    'db_database' => 'cosmiclib',
    'db_username' => 'root',
    'db_password' => '',
]);
$token = extractToken($page) ?: $token;

$page = $client->post('/install/admin', [
    '_token' => $token,
    'admin_name' => 'Administrator',
    'admin_email' => 'admin@example.com',
    'admin_password' => 'password',
    'admin_password_confirmation' => 'password',
]);
$token = extractToken($page) ?: $token;

$page = $client->post('/install/school', [
    '_token' => $token,
    'school_name' => 'SMA Negeri 1',
    'school_address' => 'Jl. Pendidikan No. 1',
]);
$token = extractToken($page) ?: $token;

$page = $client->post('/install/smtp', [
    '_token' => $token,
    'mail_driver' => 'log',
    'mail_host' => 'localhost',
    'mail_port' => '1025',
    'mail_username' => '',
    'mail_password' => '',
    'mail_encryption' => '',
    'mail_from_address' => 'noreply@example.com',
    'mail_from_name' => 'CosmicLib',
]);
$token = extractToken($page) ?: $token;

$confirm = $client->get('/install/confirm');
$token = extractToken($confirm) ?: $token;
echo "Confirm token: " . ($token ? 'found' : 'NOT FOUND') . "\n";

echo "\n--- RUN INSTALLATION ---\n";
$result = $client->post('/install/run', ['_token' => $token]);
echo substr($result, 0, 2000);


echo "\n\n--- COOKIE FILE ---\n";
echo @file_get_contents($cookieFile) ?: 'empty';