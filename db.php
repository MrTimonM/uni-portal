<?php
$dbname = getenv('UP_DB_NAME') ?: 'university_portal';
$username = getenv('UP_DB_USER') ?: 'root';
$password = getenv('UP_DB_PASS') ?: '';
$host = getenv('UP_DB_HOST') ?: 'localhost';
$configuredPort = getenv('UP_DB_PORT') ?: '3312';
$ports = array_values(array_unique([$configuredPort, '3306']));
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
            ]
        );
        break;
    } catch (PDOException $e) {
        $lastError = $e;

        if ((int) $e->getCode() === 1049) {
            try {
                $serverConn = new PDO(
                    "mysql:host=$host;port=$port;charset=utf8mb4",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
                $serverConn->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $conn = new PDO(
                    "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
                break;
            } catch (PDOException $createError) {
                $lastError = $createError;
            }
        }
    }
}

if (!isset($conn)) {
    die('Database Connection Failed: ' . htmlspecialchars($lastError->getMessage()));
}
?>
