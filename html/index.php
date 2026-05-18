<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BTC Price Wall — Live Bitcoin Price in USD</title>
<meta name="description" content="Live Bitcoin (BTC) price in USD, updated every 3 seconds. Real-time BTC/USD ticker wall display.">
<meta name="keywords" content="Bitcoin price, BTC, live Bitcoin price, BTC USD, Bitcoin ticker, crypto price">
<meta name="robots" content="index, follow">
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="canonical" href="https://<?= $_SERVER['HTTP_HOST'] ?>">

<meta property="og:title" content="BTC Price Wall — Live Bitcoin Price in USD">
<meta property="og:description" content="Live Bitcoin (BTC) price in USD, updated every 3 seconds.">
<meta property="og:type" content="website">
<meta property="og:url" content="https://<?= $_SERVER['HTTP_HOST'] ?>">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="BTC Price Wall — Live Bitcoin Price in USD">
<meta name="twitter:description" content="Live Bitcoin (BTC) price in USD, updated every 3 seconds.">

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "BTC Price Wall",
    "description": "Live Bitcoin price in USD, updated every 3 seconds.",
    "url": "https://<?= $_SERVER['HTTP_HOST'] ?>",
    "potentialAction": {
        "@type": "WatchAction",
        "target": "https://<?= $_SERVER['HTTP_HOST'] ?>"
    }
}
</script>

<style>
    @font-face {
        font-family: 'Audiowide';
        src: url('Audiowide-Regular.ttf') format('truetype');
        font-weight: normal;
        font-style: normal;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html, body {
        width: 100%;
        height: 100%;
        overflow: hidden;
        background: #000;
        color: #fff;
        font-family: 'Audiowide', sans-serif;
    }

    body {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .container {
        text-align: center;
        width: 100%;
        padding: 2vw;
    }

    .price {
        font-size: 18vw;
        font-weight: 900;
        line-height: 0.9;
        letter-spacing: 0.03em;
        transition: color 0.3s ease, transform 0.2s ease;
        user-select: none;
    }

    .price.up {
        color: #00ff88;
        transform: scale(1.02);
    }

    .price.down {
        color: #ff4d4d;
        transform: scale(0.98);
    }

    .symbol {
        margin-top: 5vh;
        font-size: 2vw;
        font-weight: 700;
        letter-spacing: 0.2em;
        opacity: 0.7;
    }

    .change {
        display: inline-flex;
        align-items: center;
        gap: 0.4em;
        margin-left: 0.6em;
        font-size: 1.4vw;
        font-weight: 700;
        opacity: 1;
        transition: color 0.3s ease;
    }

    .change .arrow {
        display: inline-block;
        width: 0;
        height: 0;
        border-left: 0.5em solid transparent;
        border-right: 0.5em solid transparent;
    }

    .change .arrow.up {
        border-bottom: 0.7em solid #00ff88;
    }

    .change .arrow.down {
        border-top: 0.7em solid #ff4d4d;
    }

    .change.green {
        color: #00ff88;
    }

    .change.red {
        color: #ff4d4d;
    }

    .updated {
        margin-top: 1vh;
        font-size: 1vw;
        opacity: 0.35;
    }

    @media (max-width: 900px) {
        .price {
            font-size: 24vw;
        }

        .symbol {
            font-size: 5vw;
        }

        .change {
            font-size: 3.5vw;
        }

        .updated {
            font-size: 3vw;
        }
    }
</style>
</head>
<body>

<div class="container">
    <div class="price" id="price">--</div>
    <div class="symbol">BTC / USD <span class="change" id="change"><span class="arrow" id="arrow"></span><span id="changePercent"></span></span></div>
    <div class="updated" id="updated">connecting...</div>
</div>

<script>
    let previousPrice = null;

    async function fetchBTC() {
        try {
            const response = await fetch('btc-price.php');

            const data = await response.json();

            const price = parseFloat(data.price);
            const formatted = Math.round(price).toLocaleString();

            const priceEl = document.getElementById('price');
            const updatedEl = document.getElementById('updated');
            const changeEl = document.getElementById('change');
            const arrowEl = document.getElementById('arrow');
            const changePercentEl = document.getElementById('changePercent');

            priceEl.textContent = formatted;

            if (data.priceChangePercent !== null && data.priceChangePercent !== undefined) {
                const pct = parseFloat(data.priceChangePercent);
                const prefix = pct > 0 ? '+' : '';
                changePercentEl.textContent = prefix + pct.toFixed(2) + '%';

                if (pct >= 0) {
                    changeEl.className = 'change green';
                    arrowEl.className = 'arrow up';
                } else {
                    changeEl.className = 'change red';
                    arrowEl.className = 'arrow down';
                }
            }

            if (previousPrice !== null) {
                if (price > previousPrice) {
                    priceEl.classList.remove('down');
                    priceEl.classList.add('up');
                } else if (price < previousPrice) {
                    priceEl.classList.remove('up');
                    priceEl.classList.add('down');
                }

                setTimeout(() => {
                    priceEl.classList.remove('up');
                    priceEl.classList.remove('down');
                }, 500);
            }

            previousPrice = price;

            updatedEl.textContent =
                'Updated ' + new Date().toLocaleTimeString('en-US', { hour12: false, timeZoneName: 'short' });

        } catch (err) {
            console.error(err);
            document.getElementById('updated').textContent =
                'Connection error';
        }
    }

    fetchBTC();

    setInterval(fetchBTC, 3000);
</script>

</body>
</html>
