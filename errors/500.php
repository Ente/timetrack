<?php
define("NO_DB", true);
http_response_code(500);
if(isset($_GET["code"], $_GET["stack"], $_GET["error"])){
    $code = filter_var($_GET["code"], FILTER_SANITIZE_NUMBER_INT);
    $stack = base64_decode($_GET["stack"]);
    $error = $_GET["error"];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>500 | Internal Server Error</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=JetBrains+Mono&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/v8.css?v=1">
</head>
<body>
  <div class="animated-bg"></div>

  <main style="display: flex; justify-content: center; align-items: center; min-height: 100vh; flex-direction: column;">
    <div class="card" style="max-width: 720px; width: 100%;">
      <h1 class="text-center" style="color: #f44336;">500 | Internal Server Error</h1>
      <h2 class="text-center">A critical error occurred</h2>
      <p><b>Error Code:</b> <?= htmlspecialchars($code); ?></p>
      <p><b>Message:</b> <?= htmlspecialchars($error); ?></p>
      <p><b>Stack Trace:</b></p>
      <pre style="font-family: 'JetBrains Mono', monospace; background: #111; color: #eee; padding: 1rem; border-radius: 0.5rem; max-height: 300px; overflow: auto;">
<?= htmlspecialchars($stack ?? "No trace available."); ?>
      </pre>
      <div style="margin-top: 1rem; text-align:center;">
        <button class="button" onclick="history.back()">Go back</button>
      </div>
    </div>
  </main>

  <?php include $_SERVER["DOCUMENT_ROOT"] . "/assets/gui/standard_footer.php"; ?>
</body>
</html>
