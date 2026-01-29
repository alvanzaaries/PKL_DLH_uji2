<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing IndustriPrimerImport...\n\n";

$templatePath = __DIR__ . '/public/templates/template_industri_primer.xlsx';

if (!file_exists($templatePath)) {
    die("Template file not found: $templatePath\n");
}

echo "Template file found: $templatePath\n";
echo "File size: " . filesize($templatePath) . " bytes\n\n";

try {
    $importer = new \App\Imports\IndustriPrimerImport();
    $result = $importer->import($templatePath);
    
    echo "Import Result:\n";
    echo "Success: " . $result['success'] . "\n";
    echo "Errors: " . $result['errors_count'] . "\n";
    echo "Total: " . $result['total'] . "\n\n";
    
    if (!empty($result['errors'])) {
        echo "Error Details:\n";
        foreach ($result['errors'] as $error) {
            echo "  Row {$error['row']}: {$error['message']}\n";
        }
    }
    
    echo "\n✓ Test completed successfully!\n";
    
} catch (\Exception $e) {
    echo "✗ Error occurred:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
