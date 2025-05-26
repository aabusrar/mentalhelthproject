<?php 
include 'php/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = trim($_POST['token']);

    if (!$token) {
        echo "<div class='message_fail'>
                The recorder does not exist. Make sure you have used the correct link!
              </div>";
        exit;
    }

    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // البحث عن المستخدم بناءً على الرمز
    $sql = "SELECT * FROM users WHERE reset_token = ? AND token_expiry > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
   
   

    if ($result->num_rows > 0) {
        // تحديث كلمة المرور
        $sql = "UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE reset_token = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_password, $token);
        $stmt->execute();

        echo "<div class='message_success'>
                Password has been successfully reset
              </div>";
    } else {
        echo "<div class='message_fail'>
                Invalid or expired recorder!
              </div>";
    }
   
    
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/customstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        body {
    font-family: cursive;

 }
        body.reset_password {
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            }

        .container_reset_password {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            
        }

        form {
            background-color: #f7f7f7;
            padding: 70px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }

        /* Input fields styling */
        input[type="password"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
            transition: border-color 0.3s ease;
        }

        input[type="password"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
        }

        input[type="submit"] {
            margin: auto;
            font-size: 15px;
            background-color: #5B6C58;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #3C473A;
        }

        /* Error & Success Message Styling */
        .message_fail {
            margin: 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: red;
            font-size: 18px;
        }

        .message_success {
            margin: 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: green;
            font-size: 18px;
        }
        /* Password Container */
        .password-container {
            position: relative;
            width: 100%;
            max-width: 400px;
        }

        /* Password Input Field Styling */
        input[type="password"], input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s ease; /* Add transition for smooth border color changes */
        }

        /* Toggle Password Icon */
        .toggle-password {
            position: absolute;
            top: 12px; /* Adjusted for better alignment */
            right: 10px;
            cursor: pointer;
            color: #333; /* Icon color for consistency */
            font-size: 20px; /* Adjust the size of the icon */
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            form {
                width: 80%;
                padding: 50px;
            }

            input[type="password"], input[type="submit"] {
                padding: 8px;
            }

            input[type="submit"] {
                font-size: 14px;
            }

            .message_fail, .message_success {
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            form {
                width: 90%;
                padding: 40px;
            }

            input[type="password"], input[type="submit"] {
                padding: 7px;
                font-size: 14px;
            }

            input[type="submit"] {
                font-size: 13px;
            }

            .message_fail, .message_success {
                font-size: 14px;
            }

            .toggle-password {
                font-size: 18px; /* Adjust icon size for smaller screens */
            }
        }




        
    </style>
</head>
<body class="reset_password">
    <div class="container_reset_password">
        <form action="" method="POST" onsubmit="return validatepassword()">
            <h2>Add New Password</h2>
            <p>Enter a new password you want</p>
            <input type="hidden" name="token" value="<?php echo isset($_GET['token']) ? $_GET['token'] : ''; ?>">
            
        <div class="password-container">
                <input type="password" name="password" id="password" placeholder="New password" required>
                <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
                <small id="passwordError" style="color: red; display: none;">Password must be at least 6 characters long.</small>
        </div>


            <input type="submit" value="Reset">
        </form>
    </div>

    <script>
        function validatepassword() {
            const password = document.getElementById('password').value;
            let isValid = true;
            const passwordError = document.getElementById('passwordError');
            if (password.length < 6) {
                passwordError.style.display = 'block';
                isValid = false;
            } else {
                passwordError.style.display = 'none';
            }

            return isValid;
        }

        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

    </script>
</body>
</html>
