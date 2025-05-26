<?php 
session_start();  // Start the session to access user ID
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

// Get user ID from session
$user_id = $_SESSION['id'];

// Fetch mood logs from the database for the logged-in user
$sql = "SELECT id, mood_date, mood, reason, notes FROM mood_logs WHERE user_id = '$user_id' ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

// Store events in an array
$events = [];
while ($row = mysqli_fetch_assoc($result)) {
    $events[] = [
        'title' => $row['mood'],  // Title will be the mood
        'start' => $row['mood_date'],  // Start will be the mood date
        'color' => ($row['mood'] == 'Happy') ? '#2ecc71' : (($row['mood'] == 'Anxious') ? '#f1c40f' : '#e74c3c'), // Assign color based on mood
        'reason' => $row['reason'],  // Add reason to the event object
        'notes' => $row['notes']    // Add notes to the event object
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
 
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<style>
    body {
    font-family: cursive;

 }
    </style>
<body>
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
    <div class="containerCalendar">
        <div class="calendar" id="calendar"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            
            // Get events from PHP array
            var events = <?php echo json_encode($events); ?>;

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                eventClick: function(info) {
                    // Show modal with details when an event is clicked
                    showModal(info.event.startStr, info.event.title, info.event.extendedProps.reason, info.event.extendedProps.notes);
                },
                events: events // Use the events fetched from the database
            });
            calendar.render();
        });

        function showModal(date, mood, activities, notes) {
            alert(`Date: ${date}\nMood: ${mood}\nReason: ${activities}\nNotes: ${notes}`);
        }
    </script>
</body>
</html>
