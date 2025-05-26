<?php
include 'php/config.php';

$id = $_GET['id'];
$selectedMood = '';
$selectedReason = '';
$selectedNotes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedMood = $_POST['mood'];
    $selectedReason = $_POST['reason'];
    $selectedNotes = $_POST['description'];

    $update_sql = "UPDATE mood_logs SET mood='$selectedMood', reason='$selectedReason', notes='$selectedNotes' WHERE id=$id";
    if ($conn->query($update_sql) === TRUE) {
        // return back;
        header("Location: moodlog.php");
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else {
    $sql = "SELECT * FROM mood_logs WHERE id=$id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $selectedMood = $row['mood'];
        $selectedReason = $row['reason'];
        $selectedNotes = $row['notes'];
    } else {
        echo "No mood log found with ID $id";
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
    <title>Edit Mood Log</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<style>
    body {
    font-family: cursive;

 }
 .text-box,
        .dropdown {
            width: 100%;
            margin-bottom: 15px;
            font-size: 17px;
            border: 1px solid #ccc;
            border-radius: 5px;
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
    color:#745C79; 
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease; /* Smooth transitions */
}
.mood-icon span {

    font-size: 20px;
}

.mood-icon:hover {
    transform: translateY(-3px); /* Lift effect on hover */
    background-color: #e0e0e0; /* Slightly darker background on hover */
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15); /* More prominent shadow on hover */
}

.mood-icon.selected {
    transform: scale(1.1); /* Slightly increase size when selected */
    background-color: #A9B7A6; /* Bright background for selected mood */
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); /* Larger shadow when selected */
    color: #fff; /* White icon color for better contrast */
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
    <div class="main-content">
        <section class="sectionHome " >
            <h2>Edit Mood Entry</h2>
            <form method="post" action="">
                <div class="mood-icons">
                    <div class="mood-icon <?php echo ($selectedMood == 'Happy') ? 'selected Happy' : ''; ?>" data-mood="Happy">
                        😊
                        <span>Happy</span>
                    </div>
                    <div class="mood-icon <?php echo ($selectedMood == 'Sad') ? 'selected Sad' : ''; ?>" data-mood="Sad">
                        😢
                        <span>Sad</span>
                    </div>
                    <div class="mood-icon <?php echo ($selectedMood == 'Anxious') ? 'selected Anxious' : ''; ?>" data-mood="Anxious">
                        😩
                        <span>Anxious</span>
                    </div>
                </div>
                <select class="dropdown" name="reason">
                    <option value="">Select reason for your mood</option>
                    <option value="Work" <?php echo ($selectedReason == 'Work') ? 'selected' : ''; ?>>Work</option>
                    <option value="Family" <?php echo ($selectedReason == 'Family') ? 'selected' : ''; ?>>Family</option>
                    <option value="Health" <?php echo ($selectedReason == 'Health') ? 'selected' : ''; ?>>Health</option>
                    <option value="Other" <?php echo ($selectedReason == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
                <textarea class="text-box" rows="4" placeholder="Additional notes..." name="description"><?php echo htmlspecialchars($selectedNotes); ?></textarea>
                <input type="hidden" name="mood" id="selectedMood" value="<?php echo htmlspecialchars($selectedMood); ?>">
                
                <div>
                                        <button class='btn1' onclick=\"location.href='moodlogsingle.php?id=" . $row["id"] . "'\">Edit</button>
                                    </div>
            </form>
        </section>
</div>
</div>
<script>
    document.querySelectorAll('.mood-icon').forEach(icon => {
        icon.addEventListener('click', function() {
            document.querySelectorAll('.mood-icon').forEach(icon => icon.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('selectedMood').value = this.getAttribute('data-mood');
        });
    });
</script>
</body>
</html>
