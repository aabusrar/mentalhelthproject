<?php
session_start();
include("php/config.php");

// Redirect to login if not authenticated
if (!isset($_SESSION['valid'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables for Profile Settings
$username = $email = $age = $password = "";
$username_err = $email_err = $age_err = $password_err = $profile_pic_err = "";
$success_msg = $error_msg = "";

// Initialize variables for Notification Preferences
$db_reminderFrequency = 'daily'; // default
$db_reminderTime = '09:00:00';   // default

// Initialize variables for Mood & Activity Categories
$db_moodCategories = "";
$db_activityCategories = "";

$id = $_SESSION['id'];

// Fetch Profile Settings
$stmt = $conn->prepare("SELECT username, email, age FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($db_username, $db_email, $db_age);
$stmt->fetch();
$stmt->close();

// Fetch Notification Preferences
$stmt = $conn->prepare("SELECT reminder_frequency, reminder_time FROM notification_preferences WHERE user_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($db_reminderFrequency, $db_reminderTime);
if (!$stmt->fetch()) {
    // If no record exists, insert default preferences
    $stmt->close();
    $insert_stmt = $conn->prepare("INSERT INTO notification_preferences (user_id, reminder_frequency, reminder_time) VALUES (?, 'daily', '09:00:00')");
    $insert_stmt->bind_param("i", $id);
    $insert_stmt->execute();
    $insert_stmt->close();
    // Re-fetch the data
    $stmt = $conn->prepare("SELECT reminder_frequency, reminder_time FROM notification_preferences WHERE user_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($db_reminderFrequency, $db_reminderTime);
    $stmt->fetch();
}
$stmt->close();

// Fetch Mood & Activity Categories
$stmt = $conn->prepare("SELECT mood_categories, activity_categories FROM mood_activity_categories WHERE user_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($db_moodCategories, $db_activityCategories);
if (!$stmt->fetch()) {
    // Insert default categories if none exist
    $stmt->close();
    $insert_stmt = $conn->prepare("INSERT INTO mood_activity_categories (user_id, mood_categories, activity_categories) VALUES (?, '', '')");
    $insert_stmt->bind_param("i", $id);
    $insert_stmt->execute();
    $insert_stmt->close();
    // Re-fetch the data
    $stmt = $conn->prepare("SELECT mood_categories, activity_categories FROM mood_activity_categories WHERE user_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($db_moodCategories, $db_activityCategories);
    $stmt->fetch();
}
$stmt->close();


// Handle Profile Settings Form Submission
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_profile'])){
    // Sanitize and validate username
    if(empty(trim($_POST['username']))){
        $username_err = "Please enter a username.";
    } else {
        $username = htmlspecialchars(trim($_POST['username']));
    }

    // Sanitize and validate email
    if(empty(trim($_POST['email']))){
        $email_err = "Please enter an email.";
    } elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        $email_err = "Please enter a valid email.";
    } else {
        $email = htmlspecialchars(trim($_POST['email']));
    }

    // Sanitize and validate age
    if(empty(trim($_POST['age']))){
        $age_err = "Please enter your age.";
    } elseif(!filter_var($_POST['age'], FILTER_VALIDATE_INT)){
        $age_err = "Please enter a valid age.";
    } else {
        $age = intval($_POST['age']);
    }

    // Handle password update if provided
    if(!empty(trim($_POST['password']))){
        if(strlen(trim($_POST['password'])) < 6){
            $password_err = "Password must have at least 6 characters.";
        } else {
            $password = htmlspecialchars(trim($_POST['password']));
        }
    }

    // Check for errors before updating
    if(empty($username_err) && empty($email_err) && empty($age_err) && empty($password_err)){
        if(!empty($password)){
            // Update with password
            $update_stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, age = ?, password = ? WHERE id = ?");
            $update_stmt->bind_param("ssisi", $username, $email, $age, $password, $id);
        } else {
            // Update without password
            $update_stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, age = ? WHERE id = ?");
            $update_stmt->bind_param("ssii", $username, $email, $age, $id);
        }

        if($update_stmt->execute()){
            $success_msg = "Profile updated successfully!";
            // Update session variables if needed
            $_SESSION['username'] = $username; // Assuming you have this
        } else {
            $error_msg = "Something went wrong. Please try again later.";
        }
        $update_stmt->close();
    }
}

// Handle Notification Preferences Form Submission
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_notifications'])){
    $reminderFrequency = $_POST['reminderFrequency'];
    $reminderTime = $_POST['reminderTime'];

    // Validate reminder frequency
    $allowed_frequencies = ['daily', 'weekly', 'never'];
    if(!in_array($reminderFrequency, $allowed_frequencies)){
        $error_msg_notifications = "Invalid reminder frequency selected.";
    }

    // Validate reminder time (basic validation)
    if(empty($reminderTime)){
        $error_msg_notifications = "Please select a reminder time.";
    }

    if(empty($error_msg_notifications)){
        $stmt = $conn->prepare("UPDATE notification_preferences SET reminder_frequency = ?, reminder_time = ? WHERE user_id = ?");
        $stmt->bind_param("ssi", $reminderFrequency, $reminderTime, $id);
        if($stmt->execute()){
            $success_msg_notifications = "Notification preferences updated successfully!";
            $db_reminderFrequency = $reminderFrequency;
            $db_reminderTime = $reminderTime;
        } else {
            $error_msg_notifications = "Failed to update notification preferences.";
        }
        $stmt->close();
    }
}

// Handle Mood & Activity Categories Form Submission
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_categories'])){
    $moodCategories = htmlspecialchars(trim($_POST['moodCategories']));
    $activityCategories = htmlspecialchars(trim($_POST['activityCategories']));

    $stmt = $conn->prepare("UPDATE mood_activity_categories SET mood_categories = ?, activity_categories = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $moodCategories, $activityCategories, $id);
    if($stmt->execute()){
        $success_msg_categories = "Mood and Activity categories updated successfully!";
        $db_moodCategories = $moodCategories;
        $db_activityCategories = $activityCategories;
    } else {
        $error_msg_categories = "Failed to update categories.";
    }
    $stmt->close();
}
// Handle profile picture upload
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['profile_pic']['type'], $allowed_types)) {
        $profile_pic_err = "Invalid file type. Only JPEG, PNG, and GIF are allowed.";
    } else {
        // Save the uploaded image to a specific folder (e.g., 'uploads/profile_pics/')
        $target_dir = "uploads/profile_pics/";
        
        // Ensure the target directory exists
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true); // Create directory if it doesn't exist
        }
        
        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);

        // Check if the file already exists
      
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                // File uploaded successfully
                $profile_pic_path = $target_file;
                // Update the database with the new profile picture path
                $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
                $stmt->bind_param("si", $profile_pic_path, $id);
                $stmt->execute();
                $stmt->close();
            } else {
                $profile_pic_err = "Error uploading the profile picture.";
            }
    }
}



$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
    font-family: cursive;

 }
        /* Basic styling for error and success messages */
        .error { color: red; }
        .success { color: green;  }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        .settings-section { margin-bottom: 30px; }
        .profile-container {
            display: flex; /* استخدام Flexbox لوضع العناصر في صف */
            align-items: center; /* محاذاة العناصر في المنتصف عموديًا */
        }

        .profile-pic {
            width: 60px; /* عرض الصورة */
            height: 60px; /* ارتفاع الصورة */
            border-radius: 50%; /* جعل الصورة دائرية */
            margin-right: 10px; /* مسافة من اليمين */
            border: 2px solid #ffffff; /* إضافة حدود بيضاء */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2); /* إضافة ظل للصورة */
            object-fit: cover; /* يضمن أن الصورة تمتليء الدائرة بشكل جيد */
            transition: transform 0.3s; /* إضافة تأثير انتقال عند التفاعل */
        }

        .profile-info {
            display: flex;
            flex-direction: column; /* وضع العناصر عموديًا */
        }

        .profile-name {
            font-size: 18px; /* حجم الخط */
            color: #ffffff ; /* لون النص */
            font-weight: bold; /* جعل الخط عريض */
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1); /* تأثير ظل للنص */
        }

        .profile-email {
            font-size: 14px; /* حجم الخط للبريد الإلكتروني */
            color: rgba(255, 255, 255, 0.8); /* لون البريد الإلكتروني بالأبيض الفاتح */
            margin-top: 4px; /* مسافة من الاسم */
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2); /* تأثير ظل للنص */
        }



        .profile-pic:hover {
            transform: scale(1.1); 
        }

    
    </style>
</head>
<body>
<header class="header">
    <nav>
        <a href="dashboard.php">Home</a>
        <a href="calender.php">Calendar</a>
        <a href="moodlog.php">Mood Log</a>
        <a href="report.php">Reports</a>
        <a href="setting.php">Profile</a>
        <a href ='custom_confirm.html'>Logout</a>
    </nav>
</header>
<div class="container">
    <!-- Profile Settings Section -->
    <div class="settings-section">
        <h3>Profile Settings</h3>
        <?php
        if(!empty($success_msg)){
            echo "<div class='success'>$success_msg</div>";
        }
        if(!empty($error_msg)){
            echo "<div class='error'>$error_msg</div>";
        }
        ?>
       <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" id="profileForm" enctype="multipart/form-data" novalidate>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="description" name="username" placeholder="Enter your username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : htmlspecialchars($db_username); ?>" required>
                <span class="error"><?php echo $username_err; ?></span>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="description" name="email" placeholder="Enter your email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($db_email); ?>" required>
                <span class="error"><?php echo $email_err; ?></span>
            </div>

            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" id="description" name="age" placeholder="Enter your Age" value="<?php echo isset($_POST['age']) ? htmlspecialchars($_POST['age']) : htmlspecialchars($db_age); ?>" required>
                <span class="error"><?php echo $age_err; ?></span>
            </div>

            <div class="form-group">
                <label for="password">Password (Leave blank to keep current password)</label>
                <input type="password" id="description" name="password" placeholder="Enter your password">
                <span class="error"><?php echo $password_err; ?></span>
            </div>

            <!-- خيار تغيير الصورة الشخصية -->
            <div class="form-group">
                <label for="profile_pic">Profile Picture</label>
                <input type="file" id="profile_pic" name="profile_pic" accept="image/*">
                <span class="error"><?php echo $profile_pic_err; ?></span>
            </div>

            <input type="submit" name="submit_profile" value="Save Changes" class="btn1">
        </form>
    </div>
    <?php // End of Profile Settings Section ?>

    <!-- Notification Preferences Section -->
    <div class="settings-section">
        <h3>Notification Preferences</h3>
        <?php
        if(!empty($success_msg_notifications)){
            echo "<div class='success'>$success_msg_notifications</div>";
        }
        if(!empty($error_msg_notifications)){
            echo "<div class='error'>$error_msg_notifications</div>";
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" id="notificationForm">
            <div class="form-group">
                <label for="reminderFrequency">Reminder Frequency</label>
                <select id="description" name="reminderFrequency">
                    <option value="daily" <?php echo ($db_reminderFrequency == 'daily') ? 'selected' : ''; ?>>Daily</option>
                    <option value="weekly" <?php echo ($db_reminderFrequency == 'weekly') ? 'selected' : ''; ?>>Weekly</option>
                    <option value="never" <?php echo ($db_reminderFrequency == 'never') ? 'selected' : ''; ?>>Never</option>
                </select>
            </div>
            <div class="form-group">
                <label for="reminderTime">Reminder Time</label>
                <input type="time" id="description" name="reminderTime" value="<?php echo htmlspecialchars($db_reminderTime); ?>">
            </div>
            <input type="submit" name="save_notifications" value="Save Preferences" class="btn1">
        </form>
    </div>

    <!-- Mood & Activity Categories Section -->
    <div class="settings-section">
        <h3>Mood & Activity Categories</h3>
        <?php
        if(!empty($success_msg_categories)){
            echo "<div class='success'>$success_msg_categories</div>";
        }
        if(!empty($error_msg_categories)){
            echo "<div class='error'>$error_msg_categories</div>";
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" id="categoriesForm">
            <div class="form-group">
                <label for="moodCategories">Mood Categories</label>
                <textarea id="description" name="moodCategories" rows="3" placeholder="Happy, Sad, Anxious..."><?php echo htmlspecialchars($db_moodCategories); ?></textarea>
            </div>
            <div class="form-group">
                <label for="activityCategories">Activity Categories</label>
                <textarea id="description" name="activityCategories" rows="3" placeholder="Exercise, Reading, Meditation..."><?php echo htmlspecialchars($db_activityCategories); ?></textarea>
            </div>
            <input type="submit" name="save_categories" value="Save Categories" class="btn1">
        </form>
    </div>





    
<!-- Client-side Validation Scripts (Optional but Recommended) -->
<script>
    // Example: Client-side validation for Profile Settings
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        let valid = true;
        // Clear previous error messages
        document.querySelectorAll('.settings-section .error').forEach(el => el.textContent = '');

        // Username validation
        const username = document.getElementById('username').value.trim();
        if(username === '') {
            document.querySelector('#profileForm .error').textContent = 'Username is required.';
            valid = false;
        }

        // Email validation
        const email = document.getElementById('email').value.trim();
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if(email === '') {
            document.querySelector('#profileForm .error').textContent = 'Email is required.';
            valid = false;
        } else if(!emailPattern.test(email)) {
            document.querySelector('#profileForm .error').textContent = 'Invalid email format.';
            valid = false;
        }

        // Age validation
        const age = document.getElementById('age').value.trim();
        if(age === '' || isNaN(age) || age <= 0){
            document.querySelector('#profileForm .error').textContent = 'Please enter a valid age.';
            valid = false;
        }

        // Password validation
        const password = document.getElementById('password').value.trim();
        if(password.length > 0 && password.length < 6){
            document.querySelector('#profileForm .error').textContent = 'Password must be at least 6 characters.';
            valid = false;
        }

        if(!valid){
            e.preventDefault(); // Prevent form submission
        }
    });

    // Similarly, you can add client-side validations for other forms
</script>
</body>
</html>

