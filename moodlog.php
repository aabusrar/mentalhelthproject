
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mood Log</title>
    <link rel="stylesheet" href="css/style.css">
</head><style>
.hidden {
    display: none;
}
.mood-icon {
    margin-left: 10px;
    font-size: 32px;
}

.mood-icon span {
    margin-left: 8px; /* Add some space between emoji and text */
    font-size:20px; /* Adjust font size for the label text if needed */
}

.mood-icon:hover {
    transform: translateY(-5px); /* Slight lift effect on hover */
}
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
        body {
    font-family: cursive;

 }
 .noMoods{
    display: flex;
    flex-direction: column; 
    align-items: center; 
    margin-bottom: 20px;   
 }
 .noMoods img {
    max-width: 100%; /* Ensures image doesn't overflow its container */
    height: auto;    /* Maintains image aspect ratio */
    margin-top: 10px; /* Adds space between the text and image */
}
.noMoods p{
    font-size: 25px;
    color: #A9B7A6;
}

   
</style>

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
<div class="containerMod">
    <main class="main-content">
        <div class="filters">
            <div>
                <label for="filter">View by:</label>
                <select id="filter">
                    <option value="week">Week</option>
                    <option value="month">Month</option>
                    <option value="custom">Custom Range</option>
                </select>
                <input type="date" id="start-date" disabled>
                <input type="date" id="end-date" disabled>
            </div>
        </div>
        <section class="section">
            <h2>Mood Entries</h2>
            <ul class="log-list" id="log-list">
                <!-- Mood entries will be dynamically inserted here -->
                <?php
                include ('php/config.php');
    
        
                session_start();
                if (!isset($_SESSION['id'])){
                    header("Location: login.php"); // Fix the redirect syntax
                    exit;
                }
                $user_id = $_SESSION['id'];

                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch mood logs from the database
                $sql = "SELECT * FROM mood_logs WHERE user_id = $user_id";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        $selectedMood = $row['mood'];
                        echo "<li class='log-item'>
                                <div class='details'>
                                    <span class='date'>" . $row['mood_date'] . "</span>
                                    
                                    <div class='mood-icon " . ($selectedMood == 'Happy' ? '' : 'hidden') . "' data-mood='Happy'>
                                        😊
                                        <span>Happy</span>
                                    </div>
                                    <div class='mood-icon " . ($selectedMood == 'Sad' ? '' : 'hidden') . "' data-mood='Sad'>
                                        😢
                                        <span>Sad</span>
                                    </div>
                                    <div class='mood-icon " . ($selectedMood == 'Anxious' ? '' : 'hidden') . "' data-mood='Anxious'>
                                        😩
                                        <span>Anxious</span>
                                    </div>
                                    
                                    <span>Reason: " . $row["reason"] . "</span>
                                    <p>" . $row["notes"] . "</p>
                                </div>
                                <div>
                                    <button class='btn1' onclick=\"location.href='moodlogsingle.php?id=" . $row["id"] . "'\">Edit</button>
                                </div>
                            </li>";
                    }
                } else {
                    echo "<li class='noMoods'>
        <p>No mood logs found</p>
        <img src='images/e.png' alt='No logs'>
      </li>";

            
                }
                
                
                $conn->close();
                ?>
            </ul>
        </section>
    </main>
</div>
<script>
    const logList = document.getElementById('log-list');
    const filterSelect = document.getElementById('filter');
    const startDateInput = document.getElementById('start-date');
    const endDateInput = document.getElementById('end-date');

    // تخزين جميع العناصر الأصلية في متغير يمكن الوصول إليه لاحقًا
    let allEntries = [...logList.children];

    function parseDate(dateString) {
        // تحويل النص إلى كائن Date بشكل آمن
        return new Date(dateString.replace(/-/g, '/'));
    }

    function filterEntries() {
        const filterValue = filterSelect.value;
        
        // نعيد تعيين العناصر إلى القائمة الكاملة من جديد قبل كل فلترة
        let filteredEntries = [...allEntries];
        const today = new Date();

        if (filterValue === 'week') {
            const oneWeekAgo = new Date(today);
            oneWeekAgo.setDate(today.getDate() - 7); // حساب تاريخ قبل أسبوع

            filteredEntries = filteredEntries.filter(entry => {
                const entryDate = parseDate(entry.querySelector('.date').textContent);
                return entryDate >= oneWeekAgo && entryDate <= today;
            });

        } else if (filterValue === 'month') {
            const oneMonthAgo = new Date(today);
            oneMonthAgo.setMonth(today.getMonth() - 1); // حساب تاريخ قبل شهر

            filteredEntries = filteredEntries.filter(entry => {
                const entryDate = parseDate(entry.querySelector('.date').textContent);
                return entryDate >= oneMonthAgo && entryDate <= today;
            });

        } else if (filterValue === 'custom') {
            const startDate = parseDate(startDateInput.value);
            const endDate = parseDate(endDateInput.value);

            if (!isNaN(startDate) && !isNaN(endDate)) {
                filteredEntries = filteredEntries.filter(entry => {
                    const entryDate = parseDate(entry.querySelector('.date').textContent);
                    return entryDate >= startDate && entryDate <= endDate;
                });
            }
        }

        // إعادة عرض البيانات المصفاة
        logList.innerHTML = '';
        filteredEntries.forEach(entry => logList.appendChild(entry));
    }

    // تمكين/تعطيل حقول النطاق المخصص بناءً على الاختيار
    filterSelect.addEventListener('change', (event) => {
        const value = event.target.value;
        const isCustom = value === 'custom';
        startDateInput.disabled = !isCustom;
        endDateInput.disabled = !isCustom;
        
        // إذا تم اختيار 'Week' أو 'Month'، تطبيق الفلترة فوراً
        filterEntries();
    });

    // تطبيق الفلترة عند تغيير التاريخ المخصص
    startDateInput.addEventListener('change', filterEntries);
    endDateInput.addEventListener('change', filterEntries);

    // تطبيق الفلترة عند تحميل الصفحة لأول مرة
    filterEntries();
</script>

</body>
</html>
