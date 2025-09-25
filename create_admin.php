<?php
require 'db_connect.php';

$name = 'New Admin';
$email = 'newadmin@bsu.edu';
$password = 'newpassword123';

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare("INSERT INTO administrators (name, email, password) VALUES (?, ?, ?)");
$stmt->execute([$name, $email, $hashedPassword]);

echo "Admin created!<br>Email: $email<br>Password: $password";
?>