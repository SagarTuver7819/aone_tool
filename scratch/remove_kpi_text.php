<?php
$file = 'c:/xampp/htdocs/sagar/backup_aone/modules/dashboard/index.php';
$content = file_get_contents($file);

// Match <div class="kpi-body"><h3>...</h3><p>...</p></div> and remove the <p>...</p> part
$pattern = '/(<div class="kpi-body"><h3>.*?<\/h3>)<p>[^<]*<\/p>(<\/div>)/s';
$replacement = '$1$2';

$newContent = preg_replace($pattern, $replacement, $content);
echo "Replacements made: " . (strlen($content) !== strlen($newContent) ? "Yes" : "No") . "\n";
file_put_contents($file, $newContent);
?>
