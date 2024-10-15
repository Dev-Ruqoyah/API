
<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");

include "connect.php";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["message" => "Connection failed: " . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    // $data = json_decode(file_get_contents("php//:input"));
    $firstname = $data->firstname;
    $lastname = $data->lastname;
    $email = $data->email;
    $password = password_hash($data->password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (firstname,lastname,email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $firstname,  $lastname, $email, $password);


    if ($stmt->execute()) {
        echo json_encode(["message" => "User created successfully"]);
    } else {
        echo json_encode(["message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
