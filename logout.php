<?php
session_start(); // Start the session

// Clear all session variables
$_SESSION = [];

// If you want to destroy the session completely
session_destroy();

// Optionally, send a JSON response
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");

echo json_encode(["message" => "Logout successful"]);
?>
