<?php
// Script para probar diferentes contraseñas de MySQL
$host = '127.0.0.1';
$port = 3306;
$username = 'root';
$database = 'sistematam';

// Lista de contraseñas comunes en XAMPP
$passwords = [
    '',           // Sin contraseña
    'root',       // root
    'password',   // password
    'admin',      // admin
    '123456',     // 123456
    'mysql',      // mysql
    'xampp',      // xampp
    'localhost',  // localhost
    '12345',      // 12345
    'qwerty',     // qwerty
];

echo "Probando conexiones a MySQL...\n\n";

foreach ($passwords as $password) {
    $display_password = $password === '' ? '(vacía)' : $password;
    echo "Probando contraseña: $display_password ... ";
    
    try {
        $dsn = "mysql:host=$host;port=$port";
        $pdo = new PDO($dsn, $username, $password);
        echo "✅ ÉXITO! La contraseña '$display_password' funciona.\n";
        
        // Intentar conectar a la base de datos específica
        try {
            $dsn_db = "mysql:host=$host;port=$port;dbname=$database";
            $pdo_db = new PDO($dsn_db, $username, $password);
            echo "✅ También se puede conectar a la base de datos '$database'.\n";
        } catch (Exception $e) {
            echo "⚠️  Se conecta al servidor pero la base de datos '$database' no existe.\n";
        }
        
        echo "\n=== CONFIGURACIÓN CORRECTA ===\n";
        echo "DB_HOST=127.0.0.1\n";
        echo "DB_PORT=3306\n";
        echo "DB_USERNAME=root\n";
        echo "DB_PASSWORD=$password\n";
        echo "DB_DATABASE=$database\n";
        echo "===============================\n";
        break;
        
    } catch (Exception $e) {
        echo "❌ Falló\n";
    }
}

echo "\nPrueba completada.\n";
?>