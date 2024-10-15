<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");

include "connect.php";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["message" => "Connection failed: " . $conn->connect_error]));
}

// Query to fetch all users
$sql = "SELECT id, firstname, lastname, email FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $users = [];
    
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode($users);
} else {
    echo json_encode(["message" => "No users found."]);
}

$conn->close();
?>
