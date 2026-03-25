<?php
$host = '127.0.0.1';
$user = 'root';
$password = 'Patito123';
$database = 'sistematam';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check pending failed records for import 88
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM failed_import_records WHERE import_id = ? AND status = 'pending'");
    $stmt->execute([88]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Pending failed records for import 88: " . $result['count'] . PHP_EOL;
    
    // Get all statuses
    $stmt = $pdo->prepare("SELECT status, COUNT(*) as count FROM failed_import_records WHERE import_id = ? GROUP BY status");
    $stmt->execute([88]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nAll failed records by status for import 88:\n";
    foreach ($results as $row) {
        echo "- {$row['status']}: {$row['count']}\n";
    }
    
    // Get one record to check structure
    $stmt = $pdo->prepare("SELECT * FROM failed_import_records WHERE import_id = 88 AND status = 'pending' LIMIT 1");
    $stmt->execute();
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "\nSample record structure:\n";
    echo "ID: " . $record['id'] . "\n";
    echo "Error Message: " . $record['error_message'] . "\n";
    echo "Original Row Data length: " . strlen($record['original_row_data']) . " bytes\n";
    echo "Original Row Data (first 200 chars): " . substr($record['original_row_data'], 0, 200) . "\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . PHP_EOL;
}
?>
