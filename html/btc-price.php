<?php
function fetch_with_cache($url, $cacheFile, $ttl) {
    if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < $ttl) {
        return file_get_contents($cacheFile);
    }

    $ch = curl_init($url);
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
            return file_get_contents($cacheFile);
        }
        return null;
    }

    file_put_contents($cacheFile, $data, LOCK_EX);
    return $data;
}

$priceCache = __DIR__ . '/c/btc-cache.json';
$avgCache   = __DIR__ . '/c/btc-avg-cache.json';

$priceData = fetch_with_cache(
    'https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT',
    $priceCache,
    3
);

$avgData = fetch_with_cache(
    'https://api.binance.com/api/v3/ticker/24hr?symbol=BTCUSDT',
    $avgCache,
    900
);

if ($priceData === null && $avgData === null) {
    http_response_code(502);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Failed to fetch price']);
    exit;
}

$response = [];
if ($priceData !== null) {
    $price = json_decode($priceData, true);
    if ($price !== null) {
        $response['price'] = $price['price'] ?? null;
    }
}
if ($avgData !== null) {
    $avg = json_decode($avgData, true);
    if ($avg !== null) {
        $response['priceChange']      = $avg['priceChange']      ?? null;
        $response['priceChangePercent'] = $avg['priceChangePercent'] ?? null;
        $response['highPrice']        = $avg['highPrice']        ?? null;
        $response['lowPrice']         = $avg['lowPrice']         ?? null;
        $response['volume']           = $avg['volume']           ?? null;
        $response['quoteVolume']      = $avg['quoteVolume']      ?? null;
        $response['weightedAvgPrice'] = $avg['weightedAvgPrice'] ?? null;
    }
}

header('Content-Type: application/json');
header('Cache-Control: no-cache');
echo json_encode($response);
