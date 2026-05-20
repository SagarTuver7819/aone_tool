<?php
$files = [
    'c:/xampp/htdocs/sagar/backup_aone/modules/dashboard/index.php',
    'c:/xampp/htdocs/sagar/backup_aone/includes/sidebar.php',
    'c:/xampp/htdocs/sagar/backup_aone/style.css'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        // Case-insensitive replace for uppercase -> capitalize
        $newContent = preg_replace('/text-transform:\s*uppercase/i', 'text-transform: capitalize', $content);
        if ($content !== $newContent) {
            file_put_contents($file, $newContent);
            echo "Updated: $file\n";
        }
    }
}
?>
