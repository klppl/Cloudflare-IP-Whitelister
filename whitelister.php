<?php

// Cloudflare API Credentials
$api_token = "";  // Replace with your API token
$account_id = ""; // Replace with your Cloudflare account ID

$correct_password = "hunter2"; //Obvisouly hunter2 is a secure password

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_password = $_POST['password'] ?? '';
    $custom_description = trim($_POST['description'] ?? '');

    if ($entered_password !== $correct_password) {
        die('
        <!DOCTYPE html>
        <html lang="en">
        <head>
          <meta charset="UTF-8">
          <title>Access Denied</title>
          <style>
            body {
              background: black;
              overflow: hidden;
              color: #0f0;
              font-family: monospace;
              text-align: center;
              margin: 0;
              padding: 0;
            }
            #matrix {
              position: absolute;
              top: 0;
              left: 0;
              width: 100%;
              height: 100%;
              z-index: -1;
            }
            .message {
              position: relative;
              top: 40%;
              font-size: 2em;
            }
          </style>
        </head>
        <body>
          <canvas id="matrix"></canvas>
          <div class="message">
            <p>INCORRECT PASSWORD, NO HACKER ACCESS!</p>
          </div>
          <script>
            // Setup canvas for the Matrix rain effect
            var canvas = document.getElementById("matrix");
            var ctx = canvas.getContext("2d");
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
    
            var letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$%^&*()*&^%";
            letters = letters.split("");
            var fontSize = 16;
            var columns = canvas.width / fontSize;
            var drops = [];
            for (var x = 0; x < columns; x++) {
              drops[x] = 1;
            }
    
            function draw() {
              ctx.fillStyle = "rgba(0, 0, 0, 0.05)";
              ctx.fillRect(0, 0, canvas.width, canvas.height);
              ctx.fillStyle = "#0f0";
              ctx.font = fontSize + "px monospace";
              for (var i = 0; i < drops.length; i++) {
                var text = letters[Math.floor(Math.random() * letters.length)];
                ctx.fillText(text, i * fontSize, drops[i] * fontSize);
                if (drops[i] * fontSize > canvas.height && Math.random() > 0.975)
                  drops[i] = 0;
                drops[i]++;
              }
            }
    
            setInterval(draw, 33);
          </script>
        </body>
        </html>
        ');
    }    
    
    if (empty($custom_description)) {
        die("<div class='message error'>Enter a meme-worthy reason for your epic hack!</div>");
    }

    function getUserIP() {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return 'UNKNOWN';
    }

    $ip = getUserIP();
    $url = "https://api.cloudflare.com/client/v4/accounts/$account_id/firewall/access_rules/rules";

    $data = [
        "mode" => "whitelist",
        "configuration" => ["target" => "ip", "value" => $ip],
        "notes" => $custom_description,
        "scope" => ["type" => "account"]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $api_token",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    curl_close($ch);

    $response_data = json_decode($response, true);
    if ($response_data !== null) {
        $pretty_response = json_encode($response_data, JSON_PRETTY_PRINT);
    } else {
        $pretty_response = $response;
    }

    $custom_message  = "<p>WOW, SUCH HACKER, MUCH WHITELIST, VERY SECURE!!11</p>";
    $custom_message .= "<p><small>Behold the sacred runes from the Cloudflare gods:</small></p>";
    $custom_message .= "<pre style='background:#000; padding:10px; border:1px dashed #0f0; color:#0f0; overflow-x:auto;'>" 
                        . htmlspecialchars($pretty_response) . "</pre>";

    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <title>Hacker Success</title>
    <style>
        body {
        background: black;
        overflow: hidden;
        color: #0f0;
        font-family: monospace;
        text-align: center;
        margin: 0;
        padding: 0;
        }
        #matrix {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        }
        .message {
        position: relative;
        top: 20%;
        padding: 20px;
        font-size: 1.5em;
        background: rgba(0, 0, 0, 0.5);
        border: 1px dashed #0f0;
        display: inline-block;
        text-align: left;
        max-width: 90%;
        margin: auto;
        }
        pre {
        font-size: 0.8em;
        margin-top: 10px;
        }
    </style>
    </head>
    <body>
    <canvas id="matrix"></canvas>
    <div class="message">
        ' . $custom_message . '
    </div>
    <script>
        // Setup canvas for the Matrix rain effect
        var canvas = document.getElementById("matrix");
        var ctx = canvas.getContext("2d");
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        var letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$%^&*()*&^%";
        letters = letters.split("");
        var fontSize = 16;
        var columns = canvas.width / fontSize;
        var drops = [];
        for (var x = 0; x < columns; x++) {
        drops[x] = 1;
        }
        function draw() {
        ctx.fillStyle = "rgba(0, 0, 0, 0.05)";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = "#0f0";
        ctx.font = fontSize + "px monospace";
        for (var i = 0; i < drops.length; i++) {
            var text = letters[Math.floor(Math.random() * letters.length)];
            ctx.fillText(text, i * fontSize, drops[i] * fontSize);
            if (drops[i] * fontSize > canvas.height && Math.random() > 0.975)
            drops[i] = 0;
            drops[i]++;
        }
        }
        setInterval(draw, 33);
    </script>
    </body>
    </html>
    ';
    exit;

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hacker Whitelister</title>
  <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
  <style>
    body {
      background: #000;
      color: #33ff33;
      font-family: 'Press Start 2P', monospace;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    .container {
      background: #111;
      padding: 20px;
      border: 1px solid #33ff33;
      border-radius: 5px;
      box-shadow: 0 0 10px #33ff33;
      max-width: 350px;
      width: 90%;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 1.2em;
      position: relative;
      overflow: hidden;
    }
    h2::after {
      content: attr(data-text);
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      color: #0f0;
      animation: glitch 2s infinite linear alternate-reverse;
    }
    @keyframes glitch {
      0%   { clip: rect(2px, 9999px, 1px, 0); transform: translate(0); }
      10%  { clip: rect(1px, 9999px, 3px, 0); transform: translate(-2px, -2px); }
      20%  { clip: rect(3px, 9999px, 2px, 0); transform: translate(2px, 2px); }
      30%  { clip: rect(2px, 9999px, 4px, 0); transform: translate(-2px, 2px); }
      40%  { clip: rect(4px, 9999px, 3px, 0); transform: translate(2px, -2px); }
      50%  { clip: rect(1px, 9999px, 4px, 0); transform: translate(-2px, -2px); }
      60%  { clip: rect(3px, 9999px, 2px, 0); transform: translate(2px, 2px); }
      70%  { clip: rect(2px, 9999px, 3px, 0); transform: translate(-2px, 2px); }
      80%  { clip: rect(3px, 9999px, 1px, 0); transform: translate(2px, -2px); }
      90%  { clip: rect(1px, 9999px, 4px, 0); transform: translate(-2px, -2px); }
      100% { clip: rect(2px, 9999px, 3px, 0); transform: translate(0); }
    }
    input, button {
      width: 90%;
      padding: 10px;
      margin: 10px auto;
      border: 1px solid #33ff33;
      border-radius: 3px;
      background: #000;
      color: #33ff33;
      font-family: 'Press Start 2P', monospace;
      display: block;
    }
    button {
      cursor: pointer;
      transition: background 0.3s;
    }
    button:hover {
      background: #222;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2 data-text="HACKER WHITELISTER">HACKER WHITELISTER</h2>
    <form method="post">
      <input type="password" name="password" placeholder="Hacker password" required>
      <input type="text" name="description" placeholder="Reason for whitelisting" required>
      <button type="submit">Execute Whitelist Protocol</button>
    </form>
  </div>
</body>
</html>

