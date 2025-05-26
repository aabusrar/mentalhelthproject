<?php 
include 'config.php';
session_start(); 
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    echo "No registration user";
}


$id = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
while ($result = mysqli_fetch_assoc($query)) {
    $res_Uname = $result['username'];
    $res_Email = $result['email'];
    $res_Age = $result['age'];
    $res_password =$result['password'];
    $res_id = $result['id'];
    $res_profile_pic = $result['profile_pic']; 
}
?>