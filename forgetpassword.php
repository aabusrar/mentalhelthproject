<?php
include 'php/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // التحقق مما إذا كان البريد الإلكتروني موجودًا
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // إنشاء رمز إعادة تعيين ورابط
        $token  = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // تحديث قاعدة البيانات بالرمز وتاريخ انتهاء صلاحية الرابط
        $sql = "UPDATE users SET reset_token = ?, token_expiry = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();

        // إرسال بريد إلكتروني يحتوي على رابط إعادة التعيين
        $resetLink = "http://localhost/mentalhelthproject-7/mentalhelthproject/reset_password.php?token=$token";
        $subject = "Reset Password";
        $message = "Click here to reset your password: " . $resetLink;
        $headers = "From: no-reply@gmail.com";

        if (mail($email, $subject, $message, $headers)) {
            echo "<div class='message_success'>
                    Email sent to reset password
                  </div>";
        } else {
            echo "<div class='message_fail'>
                    Sending email failed! try again...
                  </div>";
        }
    } else {
        echo "<div class='message_fail'>
                Email not found! try another one...
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
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/customstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
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

/* Styling for the container */
.container-ForgetPassword {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    animation: fadeIn 2s ease-in-out;
}

/* Form Styling */
form {
    background-color: #f7f7f7;
    padding: 70px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 300px;
}

/* Input fields styling */
input[type="email"], input[type="submit"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    border: 1px solid #ddd;
    transition: border-color 0.3s ease;
}

input[type="email"]:focus {
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

/* Animation for the entire container */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Media Queries لتصميم متجاوب */

/* للأجهزة اللوحية */
@media (max-width: 768px) {
    form {
        width: 80%;
        padding: 50px;
    }

    input[type="email"], input[type="submit"] {
        padding: 8px;
    }

    input[type="submit"] {
        font-size: 14px;
    }
    
    .message_fail, .message_success {
        font-size: 16px;
    }
}

/* للهواتف الذكية */
@media (max-width: 480px) {
    form {
        width: 90%;
        padding: 40px;
    }

    input[type="email"], input[type="submit"] {
        padding: 7px;
        font-size: 14px;
    }

    input[type="submit"] {
        font-size: 13px;
    }
    
    .message_fail, .message_success {
        font-size: 14px;
    }
}



    </style>
</head>
<body class="">
    <div class="container-ForgetPassword">
        <form action="" method="post" class="animate__animated animate__zoomIn">
            <h2 class="animate__animated animate__fadeInDown">Forgot Password</h2>
            <p class="animate__animated animate__fadeInLeft">Enter your email address to receive a password reset link.</p>
            <input type="email" id="email" name="email" placeholder="Enter your email" required class="animate__animated animate__fadeInLeft">
            <input type="submit" value="Send" class="animate__animated animate__fadeInUp">
        </form>
    </div>
</body>
</html>
