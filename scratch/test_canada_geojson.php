<?php
$json = file_get_contents('https://raw.githubusercontent.com/codeforgermany/click_that_hood/main/public/data/canada.geojson');
$data = json_decode($json, true);
$names = [];
foreach ($data['features'] as $f) {
    $names[] = $f['properties']['name'];
}
echo implode(", ", $names) . "\n";
