<?php
session_start();

if (!isset($_SESSION['hr_id'])) {
    header("Location: login.php");
    exit();
}

$hr_id = $_SESSION['hr_id'];

include('../core/dbConfig.php');

if (isset($_GET['job_id'])) {
    $job_id = $_GET['job_id'];

    $sql = "SELECT * FROM Jobs WHERE job_id = ? AND hr_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $job_id, $hr_id);
    $stmt->execute();
    $job = $stmt->get_result()->fetch_assoc();

    if (!$job) {
        die("Job not found.");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $job_title = $_POST['job_title'];
    $job_description = $_POST['job_description'];

    $sql = "UPDATE Jobs SET job_title = ?, job_description = ? WHERE job_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $job_title, $job_description, $job_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Job updated successfully!";
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
    <title>Edit Job</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #6a82fb, #fc5c7d);
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #6a82fb, #fc5c7d); 
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #333;
        }
        form input, form textarea, form button {
            background: linear-gradient(135deg, #6a82fb, #fc5c7d);
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        form button {
            background-color: #6a82fb;
            color: white;
            cursor: pointer;
        }
        form button:hover {
            background-color: #6a82fb;
        }
        .success {
            background-color: #fc5c7d;
            color: white;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
}

        .error {
            background-color: #fc5c7d;
            color: white;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Edit Job Post</h1>

        <?php if (isset($_SESSION['message'])) { ?>
            <div class="success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php } ?>

        <form method="POST">
            <input type="text" name="job_title" placeholder="Job Title" value="<?php echo $job['job_title']; ?>" required><br>
            <textarea name="job_description" placeholder="Job Description" required><?php echo $job['job_description']; ?></textarea><br>
            <button type="submit">Update Job</button>
        </form>

    </div>

</body>
</html>
