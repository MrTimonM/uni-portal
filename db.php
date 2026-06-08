<?php
$dbname = getenv('UP_DB_NAME') ?: 'university_portal';
$username = getenv('UP_DB_USER') ?: 'root';
$password = getenv('UP_DB_PASS') ?: '';
$host = getenv('UP_DB_HOST') ?: '127.0.0.1';
$configuredPort = getenv('UP_DB_PORT') ?: '3306';
$ports = array_values(array_unique([$configuredPort, '3312']));
$lastError = null;

foreach ($ports as $port) {
    try {
        $conn = new PDO(
            "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 1,
            ]
        );
        break;
    } catch (PDOException $e) {
        $lastError = $e;
    }
}

if (!isset($conn)) {
    die('Database Connection Failed. Import university_portal.sql into MySQL first, then reload the project. Details: ' . htmlspecialchars($lastError->getMessage()));
}
?>
