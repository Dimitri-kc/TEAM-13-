<?php
/**
 * Compatibility Check Script
 * Run this to verify the website will display properly on all platforms
 * 
 * Access at: http://localhost/TEAM-13-/Draft/backend/config/check-compatibility.php
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compatibility Check - TEAM-13</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        .check {
            margin: 15px 0;
            padding: 15px;
            border-left: 4px solid #ddd;
            border-radius: 4px;
        }
        .check.pass {
            border-left-color: #4caf50;
            background: #f1f8f6;
        }
        .check.fail {
            border-left-color: #f44336;
            background: #fef5f5;
        }
        .check.warn {
            border-left-color: #ff9800;
            background: #fff8f3;
        }
        .status {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .pass .status { color: #4caf50; }
        .fail .status { color: #f44336; }
        .warn .status { color: #ff9800; }
        .detail {
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✓ Compatibility Check</h1>
        <p>Testing your server configuration for cross-platform compatibility...</p>

        <?php
        // Check 1: PHP Version
        $phpVersion = phpversion();
        $phpCheck = version_compare($phpVersion, '7.2.0', '>=') ? 'pass' : 'fail';
        ?>
        <div class="check <?= $phpCheck ?>">
            <div class="status">PHP Version: <?= $phpVersion ?></div>
            <div class="detail">Required: PHP 7.2+ | Status: <?= $phpCheck === 'pass' ? 'PASS ✓' : 'FAIL ✗' ?></div>
        </div>

        <?php
        // Check 2: Character Encoding
        $charset = ini_get('default_charset');
        $encodingCheck = stripos($charset, 'utf-8') !== false ? 'pass' : 'warn';
        ?>
        <div class="check <?= $encodingCheck ?>">
            <div class="status">Default Charset: <?= $charset ?: 'Not set' ?></div>
            <div class="detail">Recommended: UTF-8 | Status: <?= $encodingCheck === 'pass' ? 'PASS ✓' : 'WARNING ⚠' ?></div>
        </div>

        <?php
        // Check 3: Output Buffering
        $bufferingCheck = ob_get_level() > 0 || ini_get('output_buffering') ? 'pass' : 'warn';
        ?>
        <div class="check <?= $bufferingCheck ?>">
            <div class="status">Output Buffering: <?= ob_get_level() > 0 ? 'Enabled' : (ini_get('output_buffering') ? 'Configured' : 'Disabled') ?></div>
            <div class="detail">Helps prevent header issues | Status: <?= $bufferingCheck === 'pass' ? 'PASS ✓' : 'WARNING ⚠' ?></div>
        </div>

        <?php
        // Check 4: mysqli Extension
        $mysqliCheck = extension_loaded('mysqli') ? 'pass' : 'fail';
        ?>
        <div class="check <?= $mysqliCheck ?>">
            <div class="status">MySQLi Extension: <?= extension_loaded('mysqli') ? 'Loaded' : 'Not Found' ?></div>
            <div class="detail">Required for database connections | Status: <?= $mysqliCheck === 'pass' ? 'PASS ✓' : 'FAIL ✗' ?></div>
        </div>

        <?php
        // Check 5: Session Support
        $sessionCheck = extension_loaded('session') ? 'pass' : 'fail';
        ?>
        <div class="check <?= $sessionCheck ?>">
            <div class="status">Session Extension: <?= extension_loaded('session') ? 'Loaded' : 'Not Found' ?></div>
            <div class="detail">Required for user authentication | Status: <?= $sessionCheck === 'pass' ? 'PASS ✓' : 'FAIL ✗' ?></div>
        </div>

        <?php
        // Check 6: File Permissions
        $testFile = dirname(__FILE__) . '/test-write.txt';
        $writeCheck = is_writable(dirname(__FILE__)) ? 'pass' : 'fail';
        ?>
        <div class="check <?= $writeCheck ?>">
            <div class="status">Directory Write Permission: <?= is_writable(dirname(__FILE__)) ? 'Writable' : 'Read-only' ?></div>
            <div class="detail">Required for uploads and logs | Status: <?= $writeCheck === 'pass' ? 'PASS ✓' : 'FAIL ✗' ?></div>
        </div>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">

        <h2>Summary</h2>
        <?php
        $checks = [$phpCheck, $encodingCheck, $bufferingCheck, $mysqliCheck, $sessionCheck, $writeCheck];
        $passCount = count(array_filter($checks, fn($c) => $c === 'pass'));
        $failCount = count(array_filter($checks, fn($c) => $c === 'fail'));
        $warnCount = count(array_filter($checks, fn($c) => $c === 'warn'));
        ?>
        <p>
            <strong>Results:</strong> 
            <span style="color: #4caf50;">✓ <?= $passCount ?> Passed</span> | 
            <span style="color: #ff9800;">⚠ <?= $warnCount ?> Warnings</span> | 
            <span style="color: #f44336;">✗ <?= $failCount ?> Failed</span>
        </p>

        <?php if ($failCount === 0): ?>
            <div style="background: #e8f5e9; border: 2px solid #4caf50; padding: 15px; border-radius: 4px; color: #2e7d32;">
                <strong>✓ All systems are compatible!</strong> Your website should display correctly on all platforms.
            </div>
        <?php else: ?>
            <div style="background: #ffebee; border: 2px solid #f44336; padding: 15px; border-radius: 4px; color: #c62828;">
                <strong>✗ Issues detected!</strong> Please fix the failed checks before deployment.
            </div>
        <?php endif; ?>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
        
        <h3>Platform Information</h3>
        <ul>
            <li><strong>PHP Version:</strong> <?= $phpVersion ?></li>
            <li><strong>Server:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></li>
            <li><strong>OS:</strong> <?= PHP_OS ?></li>
            <li><strong>Architecture:</strong> <?= PHP_INT_SIZE === 8 ? '64-bit' : '32-bit' ?></li>
            <li><strong>Memory Limit:</strong> <?= ini_get('memory_limit') ?></li>
            <li><strong>Max Upload Size:</strong> <?= ini_get('upload_max_filesize') ?></li>
        </ul>
    </div>
</body>
</html>
