<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (!file_exists(__DIR__ . '/data.xlsx')) {
    die("Error: data.xlsx not found. Run generate-excel.php first.\n");
}

$existing = file_exists(__DIR__ . '/data.json')
    ? json_decode(file_get_contents(__DIR__ . '/data.json'), true)
    : [];

$spreadsheet = IOFactory::load(__DIR__ . '/data.xlsx');
$result = [];

foreach ($spreadsheet->getSheetNames() as $sheetName) {
    $worksheet = $spreadsheet->getSheetByName($sheetName);
    if (!$worksheet) continue;

    $rows = $worksheet->toArray();
    if (count($rows) < 2) continue;

    $headers = $rows[0];
    $items = [];

    for ($i = 1; $i < count($rows); $i++) {
        $row = $rows[$i];
        if (empty($row[0])) continue;

        $key = $row[0];
        $item = [];

        for ($j = 1; $j < count($headers); $j++) {
            $field = $headers[$j];
            $value = $row[$j] ?? '';

            if ($value === '' || $value === null) continue;

            $originalType = isset($existing[$sheetName][$key][$field])
                ? gettype($existing[$sheetName][$key][$field])
                : null;

            if ($field === 'photos') {
                $value = array_map('trim', explode(',', $value));
            } elseif ($originalType === 'string') {
                $value = (string) $value;
            } elseif (is_numeric($value)) {
                $value = str_contains((string) $value, '.')
                    ? (float) $value
                    : (int) $value;
            }

            $item[$field] = $value;
        }

        $items[$key] = empty($item) ? new stdClass() : $item;
    }

    $result[$sheetName] = $items;
}

if (isset($existing['models'])) {
    $result['models'] = $existing['models'];
}

file_put_contents(
    __DIR__ . '/data.json',
    json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n"
);

echo "Converted: data.xlsx -> data.json (tests/fixtures/data.json)\n";
