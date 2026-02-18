<?php

// Delete old problematic migration files
$filesToDelete = [
    'database/migrations/2024_02_14_000000_add_transaction_fields_to_customer_transactions_table.php',
    'database/migrations/2024_02_14_120000_add_default_to_supplier_invoices_remaining_amount.php'
];

foreach ($filesToDelete as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "Deleted: $file\n";
    } else {
        echo "File not found: $file\n";
    }
}

echo "Cleanup completed!\n";
