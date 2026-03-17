<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Exports\RpcPpeExport;
use App\Exports\PpesExport;

// Test RpcPpeExport
echo "Testing RpcPpeExport...\n";
$request = new Request([
    'entity_name' => 'Test Entity',
    'accountable_person' => 'Test Person',
    'position' => 'Test Position',
    'office' => 'Test Office',
    'fund_cluster' => '01',
    'as_of' => '2024-01-01'
]);

try {
    $rpcExport = new RpcPpeExport($request);
    $rpcData = $rpcExport->array();
    echo "RpcPpeExport: " . count($rpcData) . " rows generated successfully\n";
    echo "Header row 5: " . json_encode($rpcData[4]) . "\n";
} catch (Exception $e) {
    echo "RpcPpeExport Error: " . $e->getMessage() . "\n";
}

// Test PpesExport
echo "\nTesting PpesExport...\n";
try {
    $ppesExport = new PpesExport($request);
    $ppesData = $ppesExport->array();
    echo "PpesExport: " . count($ppesData) . " rows generated successfully\n";
    echo "Header row 5: " . json_encode($ppesData[4]) . "\n";
} catch (Exception $e) {
    echo "PpesExport Error: " . $e->getMessage() . "\n";
}

echo "\nTesting completed!\n";
