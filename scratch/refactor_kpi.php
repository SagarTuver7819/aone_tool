<?php
$file = 'c:/xampp/htdocs/sagar/backup_aone/modules/dashboard/index.php';
$content = file_get_contents($file);

// Match the entire card structure to swap header and footer elements
$pattern = '/<div class="kpi-header"><span id="([^"]*)" class="cmp-tag"><\/span><div class="kpi-icon"><i class="([^"]+)"><\/i><\/div><\/div>\s*<div class="kpi-body"><h3><span id="([^"]*)">([^<]*)<\/span><\/h3><p>([^<]*)<\/p><\/div>\s*<div class="kpi-footer"><i class="([^"]+)"><\/i>(?:<span id="([^"]*)">([^<]*)<\/span>|<span>([^<]*)<\/span>)<\/div>/s';

$replacement = '<div class="kpi-header" style="align-items: center;">
            <div class="kpi-footer"><i class="$6"></i><span id="$7">$8$9</span></div>
            <div class="kpi-icon"><i class="$2"></i></div>
        </div>
        <div class="kpi-body"><h3><span id="$3">$4</span></h3><p>$5</p></div>
        <div style="display: flex; justify-content: flex-end; margin-top: auto;">
            <span id="$1" class="cmp-tag"></span>
        </div>';

$newContent = preg_replace($pattern, $replacement, $content);
echo "Replacements made: " . (strlen($content) !== strlen($newContent) ? "Yes" : "No") . "\n";
file_put_contents($file, $newContent);
?>
