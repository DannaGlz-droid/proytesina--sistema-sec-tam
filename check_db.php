<?php
// Direct database check
$env = parse_ini_file('.env');
$host = $env['DB_HOST'];
$user = $env['DB_USERNAME'];
$pass = $env['DB_PASSWORD'];
$db = $env['DB_DATABASE'];

try {
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $sql = "SELECT original_row_data FROM failed_import_records WHERE import_id = 90 LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result && $row = $result->fetch_assoc()) {
        echo "=== Original Row Data ===\n";
        $data = json_decode($row['original_row_data'], true);
        echo "Keys found:\n";
        foreach (array_keys($data) as $key) {
            echo "  - " . $key . "\n";
        }
        echo "\nFull data:\n";
        print_r($data);
    } else {
        echo "No records found";
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
