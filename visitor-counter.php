<?php

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate');

$counterDirectory = __DIR__ . '/visitor-data';
$counterFile = $counterDirectory . '/count.txt';
$cookieName = 'dr_prasanna_visitor_counted';

/*
|--------------------------------------------------------------------------
| Create counter directory and file
|--------------------------------------------------------------------------
*/

if (!is_dir($counterDirectory)) {
    mkdir($counterDirectory, 0755, true);
}

if (!file_exists($counterFile)) {
    file_put_contents($counterFile, '0');
}

/*
|--------------------------------------------------------------------------
| Ignore common search-engine and social-media bots
|--------------------------------------------------------------------------
*/

$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

$isBot = preg_match(
    '/bot|crawl|spider|slurp|bingpreview|facebookexternalhit|whatsapp|telegrambot/i',
    $userAgent
);

/*
|--------------------------------------------------------------------------
| Count once per browser every 24 hours
|--------------------------------------------------------------------------
*/

$shouldIncrease = !$isBot && empty($_COOKIE[$cookieName]);

$fileHandle = fopen($counterFile, 'c+');

if (!$fileHandle) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Unable to access visitor counter'
    ]);

    exit;
}

flock($fileHandle, LOCK_EX);

rewind($fileHandle);

$currentValue = trim(stream_get_contents($fileHandle));
$visitorCount = max(0, (int) $currentValue);

if ($shouldIncrease) {
    $visitorCount++;

    rewind($fileHandle);
    ftruncate($fileHandle, 0);
    fwrite($fileHandle, (string) $visitorCount);
    fflush($fileHandle);

    $isHttps = (
        !empty($_SERVER['HTTPS']) &&
        $_SERVER['HTTPS'] !== 'off'
    );

    setcookie(
        $cookieName,
        '1',
        time() + 86400,
        '/',
        '',
        $isHttps,
        true
    );
}

flock($fileHandle, LOCK_UN);
fclose($fileHandle);

echo json_encode([
    'success' => true,
    'count' => $visitorCount
]);