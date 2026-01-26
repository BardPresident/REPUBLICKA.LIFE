<?php
// download.php — secure file download for The Republic
header('Content-Type: text/plain; charset=utf-8');

$allowed_files = [
    'shell.php',
    'library.php',
    // Add other PHP files that should be downloadable
];

if (isset($_GET['file'])) {
    $requested_file = basename($_GET['file']);
    
    // Security check: only allow specific files
    if (in_array($requested_file, $allowed_files) || 
        strpos($requested_file, '.php') !== false) {
        
        // Further security: check if file exists and is in current directory
        $file_path = __DIR__ . '/' . $requested_file;
        
        if (file_exists($file_path) && is_file($file_path)) {
            // Set headers for download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $requested_file . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            
            // Clear output buffer
            flush();
            
            // Read file and output
            readfile($file_path);
            exit;
        }
    }
}

// If we get here, something went wrong
echo "Error: File not found or access denied.\n";
echo "Available files:\n";
foreach ($allowed_files as $file) {
    echo "- $file\n";
}
?>
