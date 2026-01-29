<?php

// Simple test script
require __DIR__ . '/vendor/autoload.php';

echo "Testing PhpSpreadsheet...\n";

// Test 1: Check if class exists
if (class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
    echo "✓ PhpSpreadsheet\IOFactory class found\n";
} else {
    die("✗ PhpSpreadsheet\IOFactory class NOT found! Run: composer require phpoffice/phpspreadsheet\n");
}

// Test 2: Try to load template
$templatePath = __DIR__ . '/public/templates/template_industri_primer.xlsx';
echo "\nChecking template file: $templatePath\n";

if (!file_exists($templatePath)) {
    die("✗ Template file not found!\n");
}

echo "✓ Template file exists (" . filesize($templatePath) . " bytes)\n";

// Test 3: Try to load Excel file
try {
    echo "\nTrying to load Excel file...\n";
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
    echo "✓ Excel file loaded successfully\n";
    
    $worksheet = $spreadsheet->getActiveSheet();
    echo "✓ Got active worksheet\n";
    
    $rows = $worksheet->toArray();
    echo "✓ Converted to array (" . count($rows) . " rows)\n";
    
    echo "\nFirst row (header):\n";
    print_r($rows[0]);
    
    if (isset($rows[1])) {
        echo "\nSecond row (data):\n";
        print_r($rows[1]);
    }
    
    echo "\n✓ All tests passed!\n";
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
