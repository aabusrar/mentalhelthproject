<?php 
session_start();
include 'php/config.php'; 

// Check if the user is logged in
if (!isset($_SESSION['valid'])) {
    header("Location: Login.php");
    exit();
}

// Get the logged-in user's ID
$id = $_SESSION['id'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");

// Fetch user details
while ($result = mysqli_fetch_assoc($query)) {
    $res_Uname = $result['username'];
    $res_Email = $result['email'];
    $res_Age = $result['age'];
    $res_id = $result['id'];
    $res_profile_pic = $result['profile_pic']; 
}

// Fetch favorite exercises with play count for the logged-in user
$favoritesSql = "
    SELECT e.id, e.title, e.description, COALESCE(ues.play_count, 0) AS play_count 
    FROM exercises e
    LEFT JOIN exercise_counters ues ON e.id = ues.exercise_id AND ues.user_id = $id
    WHERE e.type = 'favorite'";
$favoritesResult = mysqli_query($conn, $favoritesSql);
$favoriteExercises = [];
while ($row = mysqli_fetch_assoc($favoritesResult)) {
    $favoriteExercises[] = $row;
}

// Fetch regular exercises with play count for the logged-in user
$exercisesSql = "
    SELECT e.id, e.title, e.description, COALESCE(ues.play_count, 0) AS play_count 
    FROM exercises e
    LEFT JOIN exercise_counters ues ON e.id = ues.exercise_id AND ues.user_id = $id
    WHERE e.type = 'regular'";
$exercisesResult = mysqli_query($conn, $exercisesSql);
$regularExercises = [];
while ($row = mysqli_fetch_assoc($exercisesResult)) {
    $regularExercises[] = $row;
}

// If the play counter is updated via AJAX, handle the database update here
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['exercise_id'])) {
    $exerciseId = $_POST['exercise_id'];

    // Check if the exercise has already been played by the user
    $checkSql = "SELECT * FROM exercise_counters WHERE user_id = $id AND exercise_id = $exerciseId";
    $checkResult = mysqli_query($conn, $checkSql);

    if (mysqli_num_rows($checkResult) > 0) {
        // If already exists, increment the play count
        $updateSql = "UPDATE exercise_counters SET play_count = play_count + 1 WHERE user_id = $id AND exercise_id = $exerciseId";
        mysqli_query($conn, $updateSql);
    } else {
        // If doesn't exist, create a new record with play_count = 1
        $insertSql = "INSERT INTO exercise_counters (user_id, exercise_id, play_count) VALUES ($id, $exerciseId, 1)";
        mysqli_query($conn, $insertSql);
    }

    // Return success response
    echo json_encode(['status' => 'success']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meditation & Relaxation Exercises</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
    font-family: cursive;

 }
        /* Add some margin between the buttons */
        .btn1 {
            margin-right: 10px;
        }
    </style>
</head>
<body>
<header class="header">
        <div class="greeting">
            <!-- Greeting area -->
        </div>
        <nav>
            <a href="dashboard.php">Home</a>
            <a href="calender.php">Calendar</a>
            <a href="moodlog.php">Mood Log</a>
            <a href="report.php">Reports</a>
            <?php echo "<a href='setting.php?id=$res_id'>Profile</a>" ?>
            <a href ='custom_confirm.html'>Logout</a>
        </nav>
</header>

<div class="containerMod">
    <div class="sectionExercise">
        <h3>Favorite Exercises</h3>
        <ul class="exercise-list">
            <?php foreach ($favoriteExercises as $exercise): ?>
                <li class="exercise-item">
                    <div class="exercise-info">
                        <strong><?php echo $exercise['title']; ?></strong>
                        <span class="explaning"><?php echo $exercise['description']; ?></span>
                        <span id="counter-<?php echo $exercise['id']; ?>" class="progress">Played: <?php echo $exercise['play_count']; ?> times</span>
                    </div>
                    <button id="play-<?php echo $exercise['id']; ?>" class="btn1 play-button" onclick="startExercise('<?php echo $exercise['id']; ?>')">Start</button>
                    <button id="stop-<?php echo $exercise['id']; ?>" class="btn1 stop-button" onclick="stopExercise('<?php echo $exercise['id']; ?>')" disabled>Stop</button>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="sectionExercise">
        <h3>Available Exercises</h3>
        <ul class="exercise-list">
            <?php foreach ($regularExercises as $exercise): ?>
                <li class="exercise-item">
                    <div class="exercise-info">
                        <strong><?php echo $exercise['title']; ?></strong>
                        <span class="explaning"><?php echo $exercise['description']; ?></span>
                        <span id="counter-<?php echo $exercise['id']; ?>" class="progress">Played: <?php echo $exercise['play_count']; ?> times</span>
                    </div>
                    <button id="play-<?php echo $exercise['id']; ?>" class="btn1 play-button" onclick="startExercise('<?php echo $exercise['id']; ?>')">Start</button>
                    <button id="stop-<?php echo $exercise['id']; ?>" class="btn1 stop-button" onclick="stopExercise('<?php echo $exercise['id']; ?>')" disabled>Stop</button>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<script>
    function startExercise(exerciseId) {
        // Change the play button color to white
        var playButton = document.getElementById('play-' + exerciseId);
        var stopButton = document.getElementById('stop-' + exerciseId);

        playButton.style.backgroundColor = 'white';
        playButton.disabled = true; // Disable play button
        stopButton.disabled = false; // Enable stop button
    }

    function stopExercise(exerciseId) {
        // Restore the play button color and enable it
        var playButton = document.getElementById('play-' + exerciseId);
        var stopButton = document.getElementById('stop-' + exerciseId);
        var counterElement = document.getElementById('counter-' + exerciseId);

        // Restore original button color
        playButton.style.backgroundColor = '';
        playButton.disabled = false; // Enable play button
        stopButton.disabled = true; // Disable stop button

        // Increment the counter
        var playCount = parseInt(counterElement.textContent.split(': ')[1]) + 1;
        counterElement.textContent = 'Played: ' + playCount + ' times';

        // Send AJAX request to update play count in the database
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('exercise_id=' + exerciseId);
    }
</script>

</body>
</html>
