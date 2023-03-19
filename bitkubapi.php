<?php
$base_url = "https://api.bitkub.com";
$response = json_decode(file_get_contents($base_url . "/api/market/ticker"), true);

$eth_last_price = number_format($response["THB_ETH"]["last"], 2, ".", ",");
$btc_last_price = number_format($response["THB_BTC"]["last"], 2, ".", ",");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bitkub API</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="style1.0.css">
</head>
<body>
  <div class="container">
    <h1>Bitkub API</h1>
    <div class="lesson_style">ราคา ETH: <span><?php echo $eth_last_price; ?></span> THB</div>
    <div class="lesson_style">ราคา BTC: <span><?php echo $btc_last_price; ?></span> THB</div>
  </div>
  <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
