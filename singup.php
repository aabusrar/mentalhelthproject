<?php
include("php/config.php");
if(isset($_POST['submit'])){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $password = $_POST['password'];

    // File upload logic
    $target_dir = "uploads/";  // تأكد أن هذا المجلد موجود
    $profile_pic = "";

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a valid image
        $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size (limit to 2MB for example)
        if ($_FILES["profile_pic"]["size"] > 2000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats (jpg, png, jpeg, gif)
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                // Set the profile picture path if uploaded
                $profile_pic = $target_file;
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    // Set default profile picture if no image is uploaded
    if (empty($profile_pic)) {
        $profile_pic = "uploads/Profile-defult.png"; // تعيين مسار لصورة افتراضية
    }

    // Verify if email is unique
    $verify_query = mysqli_query($conn,"SELECT email FROM users WHERE email ='$email'");
    if(mysqli_num_rows($verify_query) != 0){
        echo '<div class ="message_fail">
                This email used, try another one
        </div>';
    }
    else{
        mysqli_query($conn,"INSERT INTO users(username,email,age,password,profile_pic) VALUES ('$username','$email','$age','$password','$profile_pic')") or die("Error occurred!");
        echo "<div class='message_success'>
                Registration successfully! now you can login,
                <script>
                       setTimeout(function() {
                           window.location.href = 'login.php';
                       }, 1000);
                     </script>
            </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/customstyle.css">
    <!-- Adding Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=/Lora:wght@300;400;500&display=swap">
    <title>Signup</title>
     <style>
        body {
    font-family: cursive;

 }
        body {
            font-family: 'Lora', sans-serif;
        } 
        h2{
            font-weight: 500;
        }
        .message_fail {
            font-family: 'Lora', sans-serif;
            font-weight: 400;
            margin: 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: red;
            font-size: 18px;

            }

            .message_success {
                font-family: 'Lora', sans-serif;
                font-weight: 400;
                margin: 0 auto;
                display: flex;
                justify-content: center;
                align-items: center;
                text-align: center;
                color: green;
                font-size: 18px;
            }

            /* Adjusting container */
            .form-container-SignUp {
                margin-top: 80px;
                max-width: 450px;
                margin-left: auto;
                margin-right: auto;
                text-align: center;
                padding: 20px;
                background-color: white;
                border-radius: 15px;
                box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
                /* animation: fadeIn 2s ease-in-out; */
            }

            /* Profile Picture Container */
            .profile-pic-container {
                position: relative;
                margin-bottom: 20px;
                text-align: center;
            }

            /* Profile Picture Styling */
            .profile-pic {
                width: 120px;
                height: 120px;
                border-radius: 50%;
                object-fit: cover;
                border: 4px solid #007bff;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                cursor: pointer;
                transition: transform 0.3s ease;
            }

            /* On hover, zoom the image */
            .profile-pic:hover {
                transform: scale(1.1);
            }

            /* Form input field styling */
            .form-group {
                margin-bottom: 20px;
            }

            input[type="text"], input[type="email"], input[type="password"], input[type="number"] {
                font-family: 'Lora', sans-serif;
                font-weight: 400;
                width: 100%;
                padding: 10px;
                border-radius: 10px;
                border: 1px solid #ccc;
                background-color: #fff;
                transition: box-shadow 0.3s ease, border 0.3s ease;
            }

            input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus, input[type="number"]:focus {
                border-color: #007bff;
                box-shadow: 0px 0px 5px rgba(0, 123, 255, 0.5);
            }

            /* Submit button styling */
            .btn-signup {
                font-family: 'Lora', sans-serif;
                font-weight: 500;
                width: auto;
                background-color: #5B6C58;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 20px;
                cursor: pointer;
                transition: background-color 0.3s ease, box-shadow 0.3s ease;
            }

            .btn-signup:hover {
                background-color: #A9B7A6;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            /* Media Queries لجعل التصميم متجاوب */

            /* للأجهزة اللوحية */
            @media (max-width: 768px) {
                .form-container-SignUp {
                    max-width: 95%;
                    padding: 15px;
                }

                .profile-pic {
                    width: 100px;
                    height: 100px;
                }

                input[type="text"], input[type="email"], input[type="password"], input[type="number"] {
                    padding: 8px;
                    font-size: 16px;
                }

                .btn-signup {
                    padding: 8px 15px;
                }

                .message_fail, .message_success {
                    font-size: 16px;
                }
            }

            /* للهواتف الذكية */
            @media (max-width: 480px) {
                .form-container-SignUp {
                    max-width: 100%;
                    padding: 10px;
                }

                .profile-pic {
                    width: 80px;
                    height: 80px;
                }

                input[type="text"], input[type="email"], input[type="password"], input[type="number"] {
                    padding: 7px;
                    font-size: 14px;
                }

                .btn-signup {
                    padding: 7px 12px;
                }

                .message_fail, .message_success {
                    font-size: 14px;
                }
            }



            /* Adding fade-in animation for the whole form */
            /* @keyframes fadeIn {
                from {
                    opacity: 0;
                }
                to {
                    opacity: 1;
                } */
            /* } */

     </style>

</head>
<body >
    <div class="" id="signup">
    <div class="form-container-SignUp">
    <h2>Signup</h2>
    <form id="signupForm" method="POST" enctype="multipart/form-data" onsubmit="return validateFormsingup()">
    
        <!-- Profile Picture Input -->
        <div class="form-group profile-pic-container">
            <label for="profile_pic">
                <img src="Images/default-avatar.png" id="profilePreview" class="profile-pic" >
            </label>
            <input type="file" id="profile_pic" name="profile_pic" accept="image/*" onchange="previewProfilePic()" style="display: none;">
        </div>

        <div class="form-group">
            <label for="username">UserName</label>
            <input type="text" id="username" placeholder="UserName" name="username" autocomplete="off" required>
            <small id="usernameError" style="color: red; display: none;">Username must be at least 3 characters long.</small>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" placeholder="Enter your Email" name="email" autocomplete="off" required>
            <small id="emailError" style="color: red; display: none;">Please enter a valid email address.</small>
        </div>

        <div class="form-group">
            <label for="age">Age</label>
            <input type="number" id="age" placeholder="Enter your Age" name="age" autocomplete="off" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" placeholder="Enter your password" name="password" autocomplete="off" required>
            <small id="passwordError" style="color: red; display: none;"></small>
            <i class="fas fa-eye toggle-password" onclick="togglePasswordsingup()"></i>
        </div>

        <input type="submit" name="submit" class="btn-signup" value="Sign Up">
        <div class="links_login">
            Already a member? <a href="login.php">Sign in</a>
        </div>
    </form>
</div>

    </div>

    <!-- JavaScript Validation and Toggle Script -->
     
     <script >
        function validateFormsingup() {
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            let isValid = true;

            // Username validation
            const usernameError = document.getElementById('usernameError');
            if (username.length < 3) {
                usernameError.style.display = 'block';
                isValid = false;
            } else {
                usernameError.style.display = 'none';
            }

            // Email validation
            const emailError = document.getElementById('emailError');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                emailError.style.display = 'block';
                isValid = false;
            } else {
                emailError.style.display = 'none';
            }

            // Password validation
            const passwordError = document.getElementById('passwordError');
            if (!validatePassword(password)) {
                passwordError.style.display = 'block';
                isValid = false;
            } else {
                passwordError.style.display = 'none';
            }

            return isValid; // Prevent form submission if validation fails
        }

        function validatePassword(password) {
            // التحقق من الشروط
            const minLength = password.length >= 8; // لا تقل عن 8 أحرف
            const hasUpperCase = /[A-Z]/.test(password); // تحتوي على حرف كبير
            const hasLowerCase = /[a-z]/.test(password); // تحتوي على حرف صغير
            const hasNumber = /\d/.test(password); // تحتوي على رقم
            const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password); // تحتوي على رمز خاص

            // تحقق الشروط
            if (!minLength || !hasUpperCase || !hasLowerCase || !hasNumber || !hasSpecialChar) {
                // عرض رسالة الخطأ عند فشل أحد الشروط
                passwordError.innerText = 'Password must be at least 8 characters long, with one uppercase, one lowercase, one number, and one special character.';
                return false;
            }
            return true;
        }

        

        function togglePasswordsingup() {
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
        function previewProfilePic() {
            const fileInput = document.getElementById('profile_pic');
            const profilePreview = document.getElementById('profilePreview');

            const file = fileInput.files[0];
            const reader = new FileReader();

            reader.onloadend = function() {
                profilePreview.src = reader.result;
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        }

     </script>
</body>
</html>
