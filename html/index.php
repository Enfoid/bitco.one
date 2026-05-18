<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BTC Price Wall</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">

<style>
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
        font-family: 'Inter', sans-serif;
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
        letter-spacing: -0.05em;
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
        margin-top: 2vh;
        font-size: 2vw;
        font-weight: 700;
        letter-spacing: 0.2em;
        opacity: 0.7;
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

        .updated {
            font-size: 3vw;
        }
    }
</style>
</head>
<body>

<div class="container">
    <div class="price" id="price">--</div>
    <div class="symbol">BTC / USD</div>
    <div class="updated" id="updated">connecting...</div>
</div>

<script>
    let previousPrice = null;

    async function fetchBTC() {
        try {
            const response = await fetch(
                'https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT'
            );

            const data = await response.json();

            const price = parseFloat(data.price);
            const formatted = Math.round(price).toLocaleString();

            const priceEl = document.getElementById('price');
            const updatedEl = document.getElementById('updated');

            priceEl.textContent = formatted;

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
                'Updated ' + new Date().toLocaleTimeString();

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
