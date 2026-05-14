<?php
$dbname = "university_portal";
$username = "root";
$password = "";

try {
    $conn = new PDO(
        "mysql:host=localhost;port=3312;dbname=$dbname;charset=utf8",
        $username,
        $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Database Connected Successfully!";

} catch(PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>