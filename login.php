<?php 
session_start();
include("php/config.php");
            // Handle login form submission
            if(isset($_POST['submit'])){
                $email = mysqli_real_escape_string($conn, $_POST['email']);
                $password = mysqli_real_escape_string($conn, $_POST['password']);

                $query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
                $result = mysqli_query($conn, $query) or die("Select Error");
                $row = mysqli_fetch_assoc($result);

                if(is_array($row) && !empty($row)){
                    $_SESSION['valid'] = $row['email'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['age'] = $row['age'];
                    $_SESSION['id'] = $row['id'];
                    // $_SESSION['message'] = "Login successfully.";
                    echo "<div class ='message_success'>
                             Login successfully
                             <script>
                            setTimeout(function() {
                                window.location.href = 'dashboard.php';
                            }, 1000);
                            </script>
                    </div>";
                    
                    
                } else {
                    echo "<div class='message_fail'>
                      Wrong Username or Password 
                       </div>";
                }
            }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registration Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="css/customstyle.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;600&display=swap" rel="stylesheet">

    <style>
       body {
    font-family: cursive;

 }

        h1, h2, .btn, .form-group label {
            font-family: 'Lora', sans-serif;
        }
        .message_fail{
            margin: 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: red;
        }
        .message_success{
            margin: 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: green;

        }


        .containerLogin {
            animation: fadeIn 2s ease-in-out;
        }

        .image-container img {
            animation: slideInRight 1.5s ease-out;
        }

        h1 {
            animation: bounceInDown 2s ease;
        }

        p {
            animation: fadeInUp 2s ease;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        @keyframes slideInRight {
            0% {
                transform: translateX(100%);
            }
            100% {
                transform: translateX(0);
            }
        }

        @keyframes bounceInDown {
            0% {
                transform: translateY(-1000px);
                opacity: 0;
            }
            60% {
                transform: translateY(30px);
                opacity: 1;
            }
            80% {
                transform: translateY(-10px);
            }
            100% {
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            0% {
                transform: translateY(20px);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

    </style>
  
</head>
<body class="login">
<div class="containerLogin">
    <div class="form-container" id="login">
        <h2 class="animate__animated animate__fadeInDown">Login</h2>
        <form id="loginForm" method="post" action="" onsubmit="return validateForm()">

            <!-- Email Input -->
            <div class="form-group">
                <label for="email" class="animate__animated animate__fadeInLeft">Email</label>
                <div class="input-group">
                    <input type="email" id="email" placeholder="Enter your Email" name="email" autocomplete="off" required class="animate__animated animate__fadeInLeft">
                </div>
                <small id="emailError" style="color: red; display: none;">Please enter a valid email address.</small>
            </div>

            <!-- Password Input -->
            <div class="form-group">
                <label for="password" class="animate__animated animate__fadeInLeft">Password</label>
                <div class="input-group">
                    <input type="password" id="password" placeholder="Enter your password" name="password" autocomplete="off" required class="animate__animated animate__fadeInLeft">
                    <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <small id="passwordError" style="color: red; display: none;">Password must be at least 8 characters long, with one uppercase, one lowercase, one number, and one special character.</small>
            </div>

            <!-- Login Button -->
            <div class="buttons">
                <input type="submit" name="submit" value="Login" class="btn btn-login animate__animated animate__fadeInUp">
                <div class="links_login">
                    Don't have an account? <a href="singup.php">Sign Up Now!</a>
                </div>
                <div class="links_login">
                    <a href="forgetpassword.php">Forgot Password?</a>
                </div>
            </div>

          
        </form>
    </div>

    <!-- Image and Motivational Text -->
    <div class="image-container">
        <img src="Images/a.png" alt="Mental Health Professional" class="animate__animated animate__slideInRight">
        <div>
            <h1 class="animate__animated animate__bounceInDown">Start your journey to mental well-being today.</h1>
            <p class="animate__animated animate__fadeInUp">Every step counts towards a healthier mind.</p>
        </div>
    </div>
</div>


    <!-- JavaScript Validation Script -->
    <script src="js/script.js"></script>
</body>

</html>
