# Cross-Platform Compatibility Guide

## Issue Fixed
The website was displaying visible code on some teammates' laptops due to:
1. **BOM (Byte Order Mark)** issues - invisible characters at the start of files
2. **Character encoding mismatches** - different systems handling UTF-8 differently
3. **Stray text in PHP files** - The `index.php` had a hostname appearing at the top

## Solution Implemented

### 1. Fixed index.php
- Removed stray hostname text that was appearing in output
- Ensured file starts cleanly with `<?php`

### 2. Added .htaccess Configuration
- Sets UTF-8 as default charset for all files
- Configures proper MIME types
- Sets correct HTTP headers for consistent rendering
- Configures cache headers for better performance

### 3. Created headers.php
- A configuration file with compatibility settings
- Should be included in all PHP pages for consistency
- Prevents BOM issues and ensures proper encoding

### 4. File Encoding Recommendations
All PHP and HTML files should:
- Use **UTF-8 (no BOM)** encoding
- Start with `<?php` with no preceding whitespace or BOM characters
- Have proper `Content-Type` headers

## For Teammates on Windows/Linux

If the website still shows visible code:

### 1. Configure Text Editor/IDE
**VS Code:**
- Set file encoding to `UTF-8` (bottom right corner)
- Ensure "Insert Final Newline" is enabled in settings
- Set end of line to `LF` (not `CRLF`)

**Sublime Text:**
- View > Line Endings > Unix (LF)
- File > Set File Encoding > UTF-8

**PhpStorm/IntelliJ:**
- File > File Properties > UTF-8
- Editor > Code Style > Line Separator set to Unix (LF)

### 2. Check Browser Cache
- Clear browser cache completely
- Hard refresh (Ctrl+F5 or Cmd+Shift+R)
- Try in Incognito/Private browsing mode

### 3. Verify XAMPP Setup
- Ensure XAMPP/Apache is properly configured for UTF-8
- Check that mod_headers and mod_rewrite are enabled in Apache

### 4. Restart Services
- Stop and restart Apache
- Clear any PHP opcode cache (if installed)

## Files Modified
- `/index.php` - Removed stray hostname text
- `/.htaccess` - Added compatibility headers and encoding configuration
- `/Draft/backend/config/headers.php` - New compatibility configuration file

## Best Practices Going Forward
1. Always save files as **UTF-8 (no BOM)**
2. Use LF line endings (Unix style) not CRLF (Windows style)
3. Never paste content that includes non-PHP text into .php files
4. Test on multiple platforms/browsers regularly
5. Use version control to catch encoding issues early

## Troubleshooting
If visible code still appears:
1. Check file encoding - must be UTF-8 without BOM
2. View source code in browser (right-click > View Page Source) to see actual HTML
3. Check browser console for JavaScript errors
4. Clear all browser cache and cookies
5. Restart Apache/XAMPP services
6. Verify no unwanted characters are at the start of files using a hex editor if needed
