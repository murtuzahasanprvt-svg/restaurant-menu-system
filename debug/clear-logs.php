<?php
/**
 * Clear debug logs
 */

$logFile = __DIR__ . '/debug.log';
$reportFile = __DIR__ . '/debug_report.json';

$cleared = [];

if (file_exists($logFile)) {
    unlink($logFile);
    $cleared[] = 'Debug log cleared';
}

if (file_exists($reportFile)) {
    unlink($reportFile);
    $cleared[] = 'Debug report cleared';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs Cleared - Restaurant Menu System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #28a745; text-align: center; }
        .success { background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .actions { text-align: center; margin-top: 20px; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✅ Logs Cleared Successfully</h1>
        <div class="success">
            <?php foreach ($cleared as $item): ?>
                <p>✓ <?php echo htmlspecialchars($item); ?></p>
            <?php endforeach; ?>
        </div>
        <div class="actions">
            <a href="../debug-index.php?debug=1" class="btn">🔍 Go to Debug Dashboard</a>
            <a href="../index.php" class="btn">🚀 Launch Application</a>
        </div>
    </div>
</body>
</html>
<?php
?>