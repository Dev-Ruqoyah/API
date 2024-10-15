<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");

include "connect.php";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["message" => "Connection failed: " . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $rawData = file_get_contents("php://input");
    
    // Check if JSON data is received
    if (!$rawData) {
        echo json_encode(["message" => "No input data"]);
        exit;
    }

    $data = json_decode($rawData);

    // Validate the JSON decoding
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["message" => "Invalid JSON format"]);
        exit;
    }

    // Validate input data
    if (isset($data->email) && isset($data->password)) {
        $email = $data->email;
        $password = $data->password;

        $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashedPassword);
            $stmt->fetch();
            
            if (password_verify($password, $hashedPassword)) {
                $_SESSION['email'] = $email; // Set session variable
                echo json_encode(["message" => "Login successful"]);
            } else {
                echo json_encode(["message" => "Invalid password"]);
            }
        } else {
            echo json_encode(["message" => "User not found"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["message" => "Email and password are required"]);
    }
}

$conn->close();
?>
