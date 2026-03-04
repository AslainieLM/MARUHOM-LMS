<?php
$db = new mysqli('localhost', 'root', 'admin', 'lms_maruhom');
if ($db->connect_error) { die('Connection failed: ' . $db->connect_error); }
$pw = password_hash('fatima', PASSWORD_DEFAULT);
$sql = "INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES ('fatima librarian', 'fatima@gmail.com', '$pw', 'librarian', NOW(), NOW())";
$db->query($sql);
echo $db->affected_rows > 0 ? "Librarian user added!\n" : "Error: " . $db->error . "\n";
$db->close();
