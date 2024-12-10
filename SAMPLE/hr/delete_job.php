<?php
session_start();

if (!isset($_SESSION['hr_id'])) {
    header("Location: login.php");
    exit();
}

include('../core/dbConfig.php');

if (isset($_GET['job_id'])) {
    $job_id = $_GET['job_id'];

    $sql = "SELECT * FROM Jobs WHERE job_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $job_id);
    $stmt->execute();
    $job = $stmt->get_result()->fetch_assoc();

    if (!$job) {
        die("Job not found.");
    }
} else {
    die("No job selected.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_delete'])) {
    $sql = "DELETE FROM Jobs WHERE job_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $job_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Job deleted successfully!";
        header("Location: manage_jobs.php");
        exit();
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Job</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6a82fb, #fc5c7d); 
            padding: 20px;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.1); 
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        .job-details {
            margin-top: 20px;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.1); 
            border-radius: 4px;
        }
        form button {
            background-color: #6a82fb;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }
        form button:hover {
            background-color: #fc5c7d;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Confirm Deletion</h1>

        <div class="job-details">
            <h2>Job Title: <?php echo $job['job_title']; ?></h2>
            <p><strong>Job Description:</strong><br><?php echo nl2br($job['job_description']); ?></p>
        </div>

        <form method="POST">
            <button type="submit" name="confirm_delete">Yes, Delete Job</button>
            <a href="manage_jobs.php">Cancel</a>
        </form>
    </div>

</body>
</html>
