<?php
http_response_code(500);
if(isset($_GET["code"], $_GET["stack"], $_GET["error"])){
    $code = filter_var($_GET["code"], FILTER_SANITIZE_NUMBER_INT);
    $stack = base64_decode($_GET["stack"]);
    $error = $_GET["error"];
}

?>
<!DOCTYPE html>

<html>
    <head>
        <title>500 | Internal Server Error</title>
        <link rel="stylesheet" type="text/css" href="/assets/css/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
    </head>

    <body>
        <h1>500 | Internal Server Error</h1>

        <div class="box">
            <h2><b>A critical error occurred - Code: <?php echo htmlspecialchars($code); ?></b></h2>
            <p><b>Message: </b> <span><?php echo htmlspecialchars($error) ?></span></p>
            <p>Trace (if available):</p><pre>
                <?php echo htmlspecialchars($stack) ?? "No trace."; ?>
            </pre>

            <button class="button" onclick="history.back()">Go back</button>
        </div>
    </body>
</html>