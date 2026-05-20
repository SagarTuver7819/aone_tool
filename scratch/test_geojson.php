<?php
$json = file_get_contents('https://raw.githubusercontent.com/PublicaMundi/MappingAPI/master/data/geojson/us-states.json');
$data = json_decode($json, true);
$names = [];
foreach ($data['features'] as $f) {
    $names[] = $f['properties']['name'];
}
echo implode(", ", $names) . "\n";
