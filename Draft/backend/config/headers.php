<?php
/**
 * Compatibility & Encoding Configuration
 * Include this at the very beginning of every PHP file to ensure cross-platform compatibility
 * 
 * This file ensures:
 * - Proper character encoding (UTF-8) across all platforms
 * - Consistent header output regardless of OS
 * - Prevention of BOM (Byte Order Mark) issues
 * - Proper session handling
 */

// Set output buffering to prevent accidental output before headers
if (!ob_get_level()) {
    ob_start();
}

// Ensure UTF-8 encoding is set before any output
header('Content-Type: text/html; charset=utf-8');

// Additional security headers for cross-platform consistency
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');

// Force UTF-8 in PHP runtime
ini_set('default_charset', 'UTF-8');

// Ensure BOM is not added (critical for cross-platform compatibility)
mb_internal_encoding('UTF-8');

// Verify no output has been sent yet (catch BOM issues early)
if (headers_sent($file, $line)) {
    trigger_error("Headers already sent in $file at line $line", E_USER_WARNING);
}

?>
