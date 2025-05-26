<?php
session_start();
include("php/config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: Login.php");
    exit();
}
$id = $_SESSION['id'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");

while ($result = mysqli_fetch_assoc($query)) {
    $res_Uname = $result['username'];
    $res_Email = $result['email'];
    $res_Age = $result['age'];
    $res_id = $result['id'];
    $res_profile_pic = $result['profile_pic'];
}
$selectedMood = isset($_POST['mood']) ? $_POST['mood'] : '';
$selectedReason = isset($_POST['reason']) ? $_POST['reason'] : '';
$selectedNotes = isset($_POST['description']) ? $_POST['description'] : '';
// عند إرسال الحالة المزاجية
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_mood'])) {
    $user_id = $_SESSION['id'];

    $mood = isset($_POST['mood']) ? $_POST['mood'] : '';
    $reason = isset($_POST['reason']) ? $_POST['reason'] : '';
    $notes = isset($_POST['description']) ? $_POST['description'] : '';
    // date with format 2024-09-01
    $date = date('Y-m-d');


    if ($mood && $reason) {
        $stmt = $conn->prepare("INSERT INTO mood_logs (user_id ,mood, reason, notes, mood_date) VALUES (?, ?, ?, ?, ?)"); // هنا أيضًا تم تصحيح اسم الحقل
        $stmt->bind_param("issss", $user_id, $mood, $reason, $notes, $date);
        $stmt->execute();
        $stmt->close();

        echo '<div class ="custom_message_success">
              <p>Mood entry saved successfully.
              <script>
                setTimeout(function() {
                window.location.href = "dashboard.php";
                }, 2000);
                </script>
              </p>
              
              </div>';


    } else {
        echo '<div class ="custom_message_fail">
              <p>Please select a mood.
              <script>
                setTimeout(function() {
                window.location.href = "dashboard.php";
                }, 2000);
                </script>
              
              </p>
              </div>';
    }

}


// عند إضافة نشاط جديد
if (isset($_POST['activity_name'])) {
    $activity_name = $conn->real_escape_string($_POST['activity_name']);
    $user_id = $_SESSION['id']; // تأكد من إضافة معرف المستخدم

    $sql = "INSERT INTO activities (activity_name, user_id) VALUES ('$activity_name', $user_id)";

    if ($conn->query($sql) === TRUE) {
        echo '<div class ="custom_message_success">
              <p>New activity added successfully
              <script>
                setTimeout(function() {
                window.location.href = "dashboard.php";
                }, 2000);
                </script>
              </p>
              </div>';
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// جلب الأنشطة الخاصة بالمستخدم الحالي
$sql = "SELECT activity_name FROM activities WHERE user_id = $id ORDER BY activity_date DESC";
$result = $conn->query($sql);
$sql_mood = "SELECT mood, reason, notes FROM mood_logs WHERE user_id = $id ORDER BY mood_date DESC";
$result_mood = $conn->query($sql_mood);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/customstyle.css">
    <style>
        
body {
    font-family: cursive;

 }
        .text-box,
.dropdown {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    font-size: 17px;
    border: 1px solid #A9B7A6;
    border-radius: 5px;
}

.sidebar {
    background-image: url("images/c.PNg");
    background-repeat: no-repeat;
    background-size: cover;
}

.main-contentHome {
    padding: 0px;
}

.profile-container {
    display: flex;
    align-items: center;
    flex-wrap: wrap; /* للسماح بتغليف العناصر عند الشاشات الأصغر */
}

.profile-pic {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    margin-right: 10px;
    border: 2px solid #ffffff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    object-fit: cover;
    transition: transform 0.3s;
}

.profile-info {
    display: flex;
    flex-direction: column;
    flex: 1; /* للسماح للعمود أن يتمدد لتغطية المساحة المتاحة */
}

.profile-name {
    font-size: 18px;
    color: #E3D5C8;
    font-weight: bold;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
}

.profile-email {
    font-size: 14px;
    color: #E3D5C8;
    margin-top: 4px;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
}

.profile-pic:hover {
    transform: scale(1.1);
}

.custom_message_success,
.custom_message_fail {
    opacity: 0;
    transform: translateY(-20px);
    animation: slideIn 0.5s forwards;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.sectionHomeimg {
    background-color: white;
    padding: 20px;
    margin: 30px;
    display: flex;
    flex-direction: row;
    align-items: center;
    border-radius: 10px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    justify-content: flex-start;
}

.introduction {
    padding-left: 50px;
    display: flex;
    flex-direction: column;
}

.mood-icon {
    font-size: 32px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 7px;
    margin: 5px;
    border-radius: 20%;
    background-color: #f0f0f0;
    color: #745C79;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
}

.mood-icon span {
    font-size: 20px;
}

.mood-icon:hover {
    transform: translateY(-3px);
    background-color: #e0e0e0;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
}

.mood-icon.selected {
    transform: scale(1.1);
    background-color: #A9B7A6;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    color: #fff;
}

/* Media Queries لجعل التصميم متجاوب */
@media (max-width: 768px) {
    .sectionHomeimg {
        flex-direction: column; /* جعل العناصر تظهر بشكل عمودي على الشاشات الصغيرة */
        padding: 10px;
        margin: 15px;
    }

    .introduction {
        padding-left: 0; 
        margin-top: 10px;
    }

    .profile-pic {
        width: 50px;
        height: 50px;
    }

    .profile-name {
        font-size: 16px;
    }

    .profile-email {
        font-size: 12px;
    }

    .mood-icon {
        font-size: 28px; /* تصغير حجم الأيقونة للشاشات الصغيرة */
    }

    .mood-icon span {
        font-size: 18px; /* تصغير حجم النص */
    }
}

@media (max-width: 480px) {
    .sectionHomeimg {
        margin: 10px;
    }

    .profile-pic {
        width: 40px;
        height: 40px;
    }

    .profile-name {
        font-size: 14px;
    }

    .profile-email {
        font-size: 10px;
    }

    .mood-icon {
        font-size: 24px;
    }

    .mood-icon span {
        font-size: 16px;
    }
}

 
    </style>

</head>

<body>

    <div class="containerMod">
        <aside class="sidebar">
            <div class="profile-container">
                <img class="profile-pic"
                    src="<?php echo $res_profile_pic ? $res_profile_pic : 'Images/default-avatar.png'; ?>"
                    alt="Profile Picture">
                <div class="profile-info">
                    <span class="profile-name">
                        <?php echo htmlspecialchars($res_Uname); ?>
                    </span>
                    <span class="profile-email">
                        <?php echo htmlspecialchars($res_Email); ?>
                    </span>
                </div>
            </div> <!-- Added a closing tag here -->
            <br>
            <h2>Quick Access</h2>
            <div class="quick-access">
                <a href="calender.php">View Calendar</a>
                <a href="meditationandrelaxationexcerciess.php">Daily Meditation</a>
            </div>
        </aside>

        <main class="main-contentHome">
            <header class="header">
                <nav>
                    <a href="dashboard.php">Home</a>
                    <a href="calender.php">Calendar</a>
                    <a href="moodlog.php">Mood Log</a>
                    <a href="report.php">Reports</a>
                    <?php echo "<a href='setting.php?id=$res_id'>Profile</a>" ?>
                    <a href ='custom_confirm.html'>Logout</a>
                
                </nav>
            </header>
            <section class="sectionHomeimg" > 
            <img src="images\d.png" >
                <div class ="introduction ">
                <h1 style="color : #5B6C58 "> Hello there !</h1>
                <h3 style="color : #A9B7A6">Start sharing your daily moods and activity here and enjoy </h3>
    </div>
               

    </section>

            <section class="sectionHome"> 

                <h2>Daily Mood Entry</h2>
                <form method="post" action="">
                    <div class="mood-icons">
                        <div class="mood-icon <?php echo ($selectedMood == 'Happy') ? 'selected Happy' : ''; ?>"
                            data-mood="Happy">
                            😊
                            <span>Happy</span>
                        </div>
                        <div class="mood-icon <?php echo ($selectedMood == 'Sad') ? 'selected Sad' : ''; ?>"
                            data-mood="Sad">
                            😢
                            <span>Sad</span>
                        </div>
                        <div class="mood-icon <?php echo ($selectedMood == 'Anxious') ? 'selected Anxious' : ''; ?>"
                            data-mood="Anxious">
                            😩
                            <span>Anxious</span>
                        </div>
                    </div>
                    <select class="dropdown" name="reason">
                        <option value="">Select reason for your mood</option>
                        <option value="Work" <?php echo ($selectedReason == 'Work') ? 'selected' : ''; ?>>Work</option>
                        <option value="Family" <?php echo ($selectedReason == 'Family') ? 'selected' : ''; ?>>Family
                        </option>
                        <option value="Health" <?php echo ($selectedReason == 'Health') ? 'selected' : ''; ?>>Health
                        </option>
                        <option value="Other" <?php echo ($selectedReason == 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>

                    <textarea class="text-box" rows="4" placeholder="Additional notes..."
                        name="description" id="description"><?php echo htmlspecialchars($selectedNotes); ?></textarea>
                    <input type="hidden" name="mood" id="selectedMood"
                        value="<?php echo htmlspecialchars($selectedMood); ?>">
                    <input class="btn1" type="submit" name="submit_mood" value="Save mood">
                </form>

            </section>
            <section class="sectionHome">
                <h2>Recent Activities</h2>
                <form action="" method="post">
                    <div class="recent-activities" id="activityList">
                        <input type="text" name="activity_name" id="activity_name" placeholder="Enter new activity"
                            required>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<li><span>' . htmlspecialchars($row['activity_name']) . '</span></li>';
                            }
                        } else {
                            echo '<li>No activities found</li>';
                        }
                        ?>
                    </div>
                    <input class="btn1" type="submit" value="Add New Activity">
                </form>


            </section>

        </main>
    </div>

    <script>
        document.querySelectorAll('.mood-icon').forEach(icon => {
            icon.addEventListener('click', function () {
                const mood = this.getAttribute('data-mood');
                document.getElementById('selectedMood').value = mood;

                // Remove 'selected' class from all icons
                document.querySelectorAll('.mood-icon').forEach(el => el.classList.remove('selected'));

                // Add 'selected' class to the clicked icon
                this.classList.add('selected');
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
            const moodIcons = document.querySelectorAll('.mood-icon');
            const selectedMoodInput = document.getElementById('selectedMood');
            const selectedReasonInput = document.getElementById('selectedReason');

            // استرجاع البيانات المخزنة من التخزين المحلي
            const storedMood = localStorage.getItem('selectedMood');
            const storedReason = localStorage.getItem('selectedReason');
            const storedNotes = localStorage.getItem('moodNotes');

            if (storedMood) {
                document.querySelectorAll('.mood-icon').forEach(icon => {
                    if (icon.getAttribute('data-mood') === storedMood) {
                        icon.classList.add('selected');
                        selectedMoodInput.value = storedMood;
                    }
                });
            }

            if (storedReason) {
                document.querySelector('select[name="reason"]').value = storedReason;
            }

            if (storedNotes) {
                document.querySelector('textarea[name="mood_notes"]').value = storedNotes;
            }

            // تخزين البيانات في التخزين المحلي عند إرسال النموذج
            document.querySelector('form').addEventListener('submit', function () {
                localStorage.setItem('selectedMood', selectedMoodInput.value);
                localStorage.setItem('selectedReason', document.querySelector('select[name="reason"]').value);
                localStorage.setItem('moodNotes', document.querySelector('textarea[name="mood_notes"]').value);
            });

            // تغيير اللون عند اختيار مود
            moodIcons.forEach(icon => {
                icon.addEventListener('click', function () {
                    moodIcons.forEach(i => i.classList.remove('selected'));
                    this.classList.add('selected');
                    selectedMoodInput.value = this.getAttribute('data-mood');
                });
            });
        });
        // document.querySelectorAll('.mood-icon').forEach(icon => {
        //     icon.addEventListener('click', function() {
        //         // إزالة الفئات السابقة
        //         document.querySelectorAll('.mood-icon').forEach(el => el.classList.remove('selected'));

        //         // الحصول على قيمة data-mood وإضافة الفئة المناسبة
        //         const mood = this.getAttribute('data-mood');
        //         document.getElementById('selectedMood').value = mood;

        //         // إضافة الفئة 'selected' للحالة المزاجية المحددة
        //         this.classList.add('selected');
        //     });
        // });


        // Handle adding new activity
        document.getElementById('addActivityButton').addEventListener('click', function () {
            const newActivity = prompt('Enter new activity:');
            if (newActivity) {
                const activityList = document.getElementById('activityList');
                const newItem = document.createElement('li');
                newItem.textContent = newActivity;
                activityList.appendChild(newItem);
            }
        });
        function confirmLogout(){
            var result = confirm("Are you sure you want to logout ? ");
            if(result){
                window.location.href ="php/logout.php";

            }
        }

    </script>
</body>

</html>