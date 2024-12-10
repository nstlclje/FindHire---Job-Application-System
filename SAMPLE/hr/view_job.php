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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Job</title>
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
            background: linear-gradient(135deg, #6a82fb, #fc5c7d); 
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1, p {
            color: #333;
        }
        .job-details {
            margin-top: 20px;
            padding: 10px;
            background: linear-gradient(135deg, #6a82fb, #fc5c7d); 
            border-radius: 4px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1><?php echo $job['job_title']; ?></h1>

        <div class="job-details">
            <h2>Job Description</h2>
            <p><?php echo nl2br($job['job_description']); ?></p>
        </div>

        <a href="manage_jobs.php">Back to Job List</a>
    </div>

</body>
</html>
