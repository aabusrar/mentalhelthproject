<?php
include 'php/config.php';

// Retrieve and sanitize form inputs
$user = $conn->real_escape_string($_POST['username']);
$email = $conn->real_escape_string($_POST['email']);
$password = $conn->real_escape_string($_POST['password']);
$reminderFrequency = $conn->real_escape_string($_POST['reminderFrequency']);
$reminderTime = $conn->real_escape_string($_POST['reminderTime']);
$moodCategories = $conn->real_escape_string($_POST['moodCategories']);
$activityCategories = $conn->real_escape_string($_POST['activityCategories']);
$dataBackup = $conn->real_escape_string($_POST['dataBackup']);
$accountActions = $conn->real_escape_string($_POST['accountActions']);

// Prepare and execute SQL query
$sql = "INSERT INTO user_settings (username, email, password, reminder_frequency, reminder_time, mood_categories, activity_categories, data_backup, account_actions)
VALUES ('$user', '$email', '$password', '$reminderFrequency', '$reminderTime', '$moodCategories', '$activityCategories', '$dataBackup', '$accountActions')";

if ($conn->query($sql) === TRUE) {
    echo "Settings saved successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the connection
$conn->close();
?>
