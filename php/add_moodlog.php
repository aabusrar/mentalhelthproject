<?php
include 'config.php';
session_start();
//add mood logs
$user_id = $_SESSION['user_id'];
$mood = $_POST['mood'];
$reason = $_POST['reason'];
$description = $_POST['description'];
$date = date('Y-m-d');

$sql = "INSERT INTO mood_logs (user_id, date, mood, reason, description) VALUES ('$user_id', '$date', '$mood', '$reason', '$description')";

if ($conn->query($sql) === TRUE) {
    echo "Mood log added successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
