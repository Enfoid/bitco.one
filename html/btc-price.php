<?php
$cacheFile = __DIR__ . '/btc-cache.json';
$ttl = 3;

if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < $ttl) {
    $data = file_get_contents($cacheFile);
} else {
    $ch = curl_init('https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_USERAGENT => 'BTC-Price-Wall/1.0',
    ]);
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || $data === false) {
        if (is_file($cacheFile)) {
            $data = file_get_contents($cacheFile);
        } else {
            http_response_code(502);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Failed to fetch price']);
            exit;
        }
    } else {
        file_put_contents($cacheFile, $data, LOCK_EX);
    }
}

header('Content-Type: application/json');
header('Cache-Control: no-cache');
echo $data;
