<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

$json = json_decode(file_get_contents(__DIR__ . '/data.json'), true);

$spreadsheet = new Spreadsheet();
$spreadsheet->removeSheetByIndex(0);

$skipSheets = ['models'];

foreach ($json as $sheetName => $items) {
    if (in_array($sheetName, $skipSheets)) continue;
    if (empty($items)) continue;

    $worksheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $sheetName);
    $spreadsheet->addSheet($worksheet);

    $headers = ['key'];
    foreach ($items as $key => $data) {
        if (!is_array($data)) continue;
        foreach ($data as $field => $value) {
            if (!in_array($field, $headers)) {
                $headers[] = $field;
            }
        }
    }

    $col = 1;
    foreach ($headers as $header) {
        $cell = $worksheet->getCell([$col, 1]);
        $cell->setValue($header);
        $worksheet->getStyle([$col, 1])->getFont()->setBold(true);
        $col++;
    }

    $row = 2;
    foreach ($items as $key => $data) {
        if (!is_array($data)) continue;
        $worksheet->getCell([1, $row])->setValue($key);
        $col = 2;
        foreach (array_slice($headers, 1) as $field) {
            $value = $data[$field] ?? '';
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $worksheet->getCell([$col, $row])->setValue($value);
            $col++;
        }
        $row++;
    }

    foreach (range(1, count($headers)) as $col) {
        $worksheet->getColumnDimensionByColumn($col)->setAutoSize(true);
    }
}

$writer = new Xlsx($spreadsheet);
$writer->save(__DIR__ . '/data.xlsx');

echo "Excel generated: tests/fixtures/data.xlsx\n";
