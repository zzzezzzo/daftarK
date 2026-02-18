<?php

// Test file to verify CashBoxTransaction class can be loaded
require_once __DIR__ . '/vendor/autoload.php';

try {
    $transaction = new \App\Models\CashBoxTransaction();
    echo "CashBoxTransaction class loaded successfully!";
} catch (Exception $e) {
    echo "Error loading CashBoxTransaction: " . $e->getMessage();
}
