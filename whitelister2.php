<?php
// --------------------------
// PHP logic for Cloudflare
// --------------------------
$api_token = "";  // Replace with your API token
$account_id = ""; // Replace with your Cloudflare account ID
$correct_password = "hunter2";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_password = $_POST['password'] ?? '';
    $custom_description = trim($_POST['description'] ?? '');

    if ($entered_password !== $correct_password) {
        die("<h3 style='color: red;'>Error: Incorrect password.</h3>");
    }

    if (empty($custom_description)) {
        die("<h3 style='color: red;'>Error: Please enter a description.</h3>");
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
        "configuration" => [
            "target" => "ip",
            "value" => $ip
        ],
        "notes" => $custom_description,
        "scope" => [
            "type" => "account"
        ]
    ];

    // cURL request to Cloudflare API
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

    echo "<h3 style='color: green;'>Success! Your IP has been whitelisted.</h3>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Whitelister</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background-color: #ffffff;
            color: #000000;
            font-family: "Courier New", Courier, monospace;
            font-size: 14px;
            line-height: 1.4;
        }
        #container {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px dashed #000;
        }
        /* ASCII-inspired dashed hr lines */
        hr.ascii-hr {
            border: none;
            margin: 1em 0;
            text-align: center;
            height: 1em;
        }
        hr.ascii-hr::before {
            content: "------------------------------------------------------";
            display: inline-block;
            color: #000;
        }
        /* Header */
        #header {
            text-align: center;
            margin-bottom: 20px;
        }
        #header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        #header p {
            font-style: italic;
        }
        /* Post title */
        .post-title {
            font-size: 18px;
            margin: 20px 0 10px;
            text-align: center;
        }
        /* Form styling */
        form {
            border: 1px dashed #000;
            padding: 15px;
            margin: 20px 0;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 5px;
            margin-bottom: 10px;
            font-family: inherit;
            font-size: 14px;
            border: 1px solid #ccc;
        }
        button {
            padding: 5px 10px;
            font-family: inherit;
            font-size: 14px;
            border: 1px dashed #000;
            background-color: #f0f0f0;
            cursor: pointer;
        }
        button:hover {
            background-color: #e0e0e0;
        }
        /* Footer */
        footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px dashed #000;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
<div id="container">
    <div id="header">
        <hr class="ascii-hr">
        <h1>Whitelister</h1>
        <hr class="ascii-hr">
    </div>
        
    <form method="post">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <label for="description">Description</label>
        <input type="text" id="description" name="description" placeholder="?" required>
        <button type="submit">Whitelist!</button>
    </form>
</div>
</body>
</html>
