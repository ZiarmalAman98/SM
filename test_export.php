<?php

require_once 'vendor/autoload.php';

use App\Exports\CustomResultsTemplateExport;

// Test the export directly
$export = new CustomResultsTemplateExport(1, 12); // branch_id=1, class_id=12
$export->store('test-export.xlsx');

echo "Test export completed\n";