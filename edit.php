<?php
session_start(); // Start the session
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
include "connect.php";


$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["message" => "Connection failed: " . $conn->connect_error]));
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_SESSION['user_id'])) {
        $id = $_SESSION['user_id']; 

        // Validate input data
        if (isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['password'])) {
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $password = $_POST['password'];

            // Handle file upload
            $profilePicture = null;
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $targetDir = "uploads/";
                $fileName = basename($_FILES["profile_picture"]["name"]);
                $targetFilePath = $targetDir . $fileName;

                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFilePath)) {
                    $profilePicture = $targetFilePath; // Store the path to the image
                } else {
                    echo json_encode(["message" => "Error uploading file."]);
                    exit;
                }
            }

            // Prepare the SQL statement for updating user details
            if ($profilePicture) {
                $stmt = $conn->prepare("UPDATE users SET firstname = ?, lastname = ?, password = ?, profile_picture = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $firstname, $lastname, $password, $profilePicture, $id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET firstname = ?, lastname = ?, password = ? WHERE id = ?");
                $stmt->bind_param("sssi", $firstname, $lastname, $password, $id);
            }

            if ($stmt->execute()) {
                echo json_encode(["message" => "User updated successfully"]);
            } else {
                echo json_encode(["message" => "Error: " . $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(["message" => "All fields are required."]);
        }
    } else {
        echo json_encode(["message" => "User not logged in."]);
    }
}

$conn->close();
?>
