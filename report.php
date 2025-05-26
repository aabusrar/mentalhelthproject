<?php
// تفعيل عرض الأخطاء في PHP (لأغراض التصحيح)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// تضمين ملف الاتصال بقاعدة البيانات
session_start();
include("php/config.php");
// إعداد المتغيرات لتخزين البيانات
$moodData = [];
$labels = [];

// التحقق من اختيار المستخدم
$filter = isset($_POST['dateRange']) ? $_POST['dateRange'] : 'lastMonth';
$startDate = '';
$endDate = '';

// إعداد الفترات الزمنية بناءً على الفترة المحددة
if ($filter == 'lastWeek') {
    $startDate = date('Y-m-d', strtotime('-1 week'));
    $endDate = date('Y-m-d'); // تاريخ اليوم
} elseif ($filter == 'lastMonth') {
    $startDate = date('Y-m-d', strtotime('-1 month'));
    $endDate = date('Y-m-d'); // تاريخ اليوم
} elseif ($filter == 'custom' && isset($_POST['startDate']) && isset($_POST['endDate'])) {
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
}

// الاستعلام لجلب البيانات بناءً على الفلترة الزمنية
if ($startDate && $endDate) {
    $sql = "SELECT mood, mood_date FROM mood_logs WHERE mood_date BETWEEN '$startDate' AND '$endDate' ORDER BY mood_date ASC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $labels[] = $row['mood_date'];
            $moodData[] = $row['mood'];
        }
    } else {
        echo "No mood data found.";
    }
}

// إغلاق الاتصال بقاعدة البيانات
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Page</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.0.1/chart.min.css" rel="stylesheet">
    <style>
        body {
    font-family: cursive;

 }
    .report-header {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 5px;
    }
    
    .report-header label {
        margin-right: 5px;
    }

    .report-header h2 {
        margin: 0;
    }

    .download-report-button {
        background-color: #4CAF50;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-left: 5px;
    }

    .report-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
    }

    .report-list {
        margin-top: 0;
    }

    .report-item {
        margin: 0;
    }

    .card ul.report-list {
        margin-top: 0;
    }

    .weekly-summary-card h3 {
        margin-bottom: 2px;
    }

    .weekly-summary-card ul.report-list {
        margin-top: 0;
    }

    .customRangeInputs {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 5px;
    }

    .customRangeInputs label {
        margin-right: 5px;
    }

    .container {
    display: flex;
    flex-direction: column; /* لضبط الاتجاه إلى عمود */
    align-items: center; /* محاذاة البطاقات في المنتصف */
    margin-top: 40px;
    margin-top: 20px;
    
}

.cards-row {
    display: flex; /* استخدام الفليكس لتنسيق الصف */
    justify-content: center; /* محاذاة البطاقتين في المنتصف */
    gap: 20px; /* إضافة مساحة بين البطاقات */
}

.weekly-summary-card {
    margin-top: 20px; /* إضافة مساحة بين الصفين */
}
    .card h3 {
        margin-bottom: 2px;
    }
    .card{
        padding: 100px;
        margin-bottom: 20px;
    }

    .chart-placeholder {
        width: 100%;
        height: 300px;
        background-color: #eaeaea;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 5px;
    }

    .report-container {
        margin: 15px;
    }

    .trends-chart, .activity-impact-chart {
        border-radius: 5px;
        padding: 5px;
        position: relative;
        margin-top: 5px; /* تقليل المسافة الفوقية */
        margin-bottom: 5px; /* تقليل المسافة السفلية */
    }

    .download-container {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 15px;
    }

    .dropdown-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
    }

    .dropdown-container form {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .dropdown-container select {
        padding: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    .btn1 {
        color: white;
        padding: 8px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn1:hover {
        background-color: #45a049;
    }

    .dropdown-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .dropdown-container form,
    .btn1 {
        margin: 0;
        flex-grow: 1;
    }

    .dropdown-container select {
        margin-right: 15px;
        width: 130px;
    }

    .dropdown-container .btn1 {
        max-width: 180px;
        text-align: center;
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
            <a href="custom_confirm.html">Logout</a>
        </nav>
    </header>

    <div class="report-container">
        <!-- Date selection and Download Button -->
        <div class="dropdown-container">
            <form method="POST" id="dateForm">
                <select id="dateRange" name="dateRange" onchange="toggleCustomRange(); document.getElementById('dateForm').submit();">
                    <option value="lastWeek" <?php if ($filter == 'lastWeek') echo 'selected'; ?>>Last Week</option>
                    <option value="lastMonth" <?php if ($filter == 'lastMonth') echo 'selected'; ?>>Last Month</option>
                    <option value="custom" <?php if ($filter == 'custom') echo 'selected'; ?>>Custom Range</option>
                </select>
                <div id="customRangeInputs" style="display: <?php echo $filter == 'custom' ? 'block' : 'none'; ?>;">
                    <label for="startDate">Start Date:</label>
                    <input type="date" id="startDate" name="startDate" value="<?php echo isset($_POST['startDate']) ? $_POST['startDate'] : ''; ?>" required>
                    <label for="endDate">End Date:</label>
                    <input type="date" id="endDate" name="endDate" value="<?php echo isset($_POST['endDate']) ? $_POST['endDate'] : ''; ?>" required>
                </div>
            </form>
            <button class="btn1" onclick="downloadReport()">Download Report</button>
        </div>

        <!-- Mood Trends and Activity Impact sections side by side -->
        <div class="container">
    <div class="cards-row">
        <div class="card">
            <h3>Mood Trends</h3>
            <div class="trends-chart"></div>
            <ul class="report-list">
                <?php
                include('php/config.php');

                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }

                if (!isset($_SESSION['id'])) {
                    header("Location: login.php");
                    exit;
                }
                $user_id = $_SESSION['id'];

                $conn = new mysqli($servername, $username, $password, $dbname);
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "SELECT mood, COUNT(mood) as count FROM mood_logs WHERE user_id = $user_id GROUP BY mood";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<li class='report-item'>Mood: " . $row['mood'] . " (Count: " . $row['count'] . ")</li>";
                    }
                } else {
                    echo "<li class='report-item'>No mood data found.</li>";
                }
                ?>
            </ul>
        </div>

        <div class="card">
            <h3>Activity Impact</h3>
            <div class="activity-impact-chart"></div>
            <ul class="report-list">
                <?php
                // Fetch activity impact based on mood logs
                $sql_activity_impact = "SELECT activity_categories, COUNT(*) as count FROM mood_activity_categories WHERE user_id = $user_id GROUP BY activity_categories";
                $result_activity_impact = $conn->query($sql_activity_impact);

                if ($result_activity_impact->num_rows > 0) {
                    while ($row = $result_activity_impact->fetch_assoc()) {
                        echo "<li class='report-item'>Activity: " . $row['activity_categories'] . " (Count: " . $row['count'] . ")</li>";
                    }
                } else {
                    echo "<li class='report-item'>No activity impact data found.</li>";
                }
                ?>
            </ul>
        </div>
    </div>

    <!-- Weekly Summary Section spanning across both columns -->
    <div class="card">
        <h3>Weekly Summary</h3>
        <div class="Weekly-Summary-chart"></div>
        <ul class="report-list">
            <?php
            // Fetch weekly summary
            $sql_weekly_summary = "SELECT mood, COUNT(*) as count FROM mood_logs WHERE user_id = $user_id AND mood_date >= CURDATE() - INTERVAL 7 DAY GROUP BY mood";
            $result_weekly_summary = $conn->query($sql_weekly_summary);

            if ($result_weekly_summary->num_rows > 0) {
                while ($row = $result_weekly_summary->fetch_assoc()) {
                    echo "<li class='report-item'>Mood: " . $row['mood'] . " (Count: " . $row['count'] . ")</li>";
                }
            } else {
                echo "<li class='report-item'>No mood logs found for the past week</li>";
            }

            $conn->close();
            ?>
        </ul>
    </div>
</div>


    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.0.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>

    <script>
    // تمرير البيانات من PHP إلى JavaScript
    const moodLabels = <?php echo json_encode($labels); ?>;
    const moodData = <?php echo json_encode($moodData); ?>;

    // إعداد الرسم البياني لمزاجات المستخدم
    const ctx = document.getElementById('moodTrendsChart').getContext('2d');
    const moodTrendsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: moodLabels,
            datasets: [{
                label: 'Mood',
                data: moodData.map(mood => mood === 'Happy' ? 3 : mood === 'Anxious' ? 2 : mood === 'Sad' ? 1 : 0),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            switch (value) {
                                case 1: return 'Sad';
                                case 2: return 'Anxious';
                                case 3: return 'Happy';
                                default: return '';
                            }
                        }
                    }
                }
            }
        }
    });

    // وظيفة لتحميل التقرير كملف PDF
    function downloadReport() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // إعداد العنوان
        doc.setFontSize(18);
        doc.text('Mood Report - <?php echo ucfirst($filter); ?>', 10, 10);

        // إضافة جدول بالبيانات
        doc.setFontSize(12);
        let moodData = <?php echo json_encode($moodData); ?>;
        let moodLabels = <?php echo json_encode($labels); ?>;

        // إعداد الجدول
        let tableColumn = ["Date", "Mood"];
        let tableRows = [];

        moodData.forEach((mood, index) => {
            let date = moodLabels[index];
            tableRows.push([date, mood]);
        });

        // إضافة الجدول إلى PDF
        doc.autoTable({
            head: [tableColumn],
            body: tableRows,
            startY: 20,
            theme: 'grid',
            styles: {
                fontSize: 11,
                cellPadding: 4,
                minCellHeight: 10,
                valign: 'middle',
                halign: 'center'
            },
            headStyles: {
                fillColor: [75, 192, 192],
                textColor: [255, 255, 255],
                fontSize: 12,
                fontStyle: 'bold',
                halign: 'center'
            },
            bodyStyles: {
                fillColor: [255, 255, 255],
                textColor: [0, 0, 0],
                halign: 'center'
            }
        });

        // حفظ ملف PDF
        doc.save('mood_report_<?php echo $filter; ?>.pdf');
    }

    // وظيفة لتبديل ظهور حقول النطاق المخصص
    function toggleCustomRange() {
        const customRangeInputs = document.getElementById('customRangeInputs');
        const dateRangeSelect = document.getElementById('dateRange');
        customRangeInputs.style.display = dateRangeSelect.value === 'custom' ? 'block' : 'none';
    }

    // استدعاء الوظيفة لتحديث الحقول عند التحميل
    window.onload = toggleCustomRange;
    </script>

</body>
</html>
