<?php
session_start();
include('../core/dbConfig.php');

if (!isset($_SESSION['applicant_id'])) {
    header("Location: login.php"); 
    exit();
}

$applicant_id = $_SESSION['applicant_id'];
$success_message = $error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_job'])) {
    $job_id = $_POST['job_id'];
    $application_message = mysqli_real_escape_string($conn, $_POST['application_message']);
    $resume_pdf = $_FILES['resume_pdf'];

    $check_query = "SELECT * FROM Applications WHERE job_id = '$job_id' AND applicant_id = '$applicant_id'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $error_message = "You have already applied for this job.";
    } else {
        
        if ($resume_pdf['error'] == 0) {
            $resume_pdf_name = $resume_pdf['name'];
            $resume_pdf_data = file_get_contents($resume_pdf['tmp_name']);

            $query = "INSERT INTO Applications (job_id, applicant_id, application_message, application_status)
                      VALUES ('$job_id', '$applicant_id', '$application_message', 'Pending')";
            if (mysqli_query($conn, $query)) {
                $application_id = mysqli_insert_id($conn);

                $query = "UPDATE Applications SET resume_pdf = ?, resume_pdf_name = ? WHERE application_id = '$application_id'";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'ss', $resume_pdf_data, $resume_pdf_name);
                if (mysqli_stmt_execute($stmt)) {
                
                    header("Location: applicant_dashboard.php?success=1");
                    exit();
                } else {
                    $error_message = "Error while uploading resume!";
                }
            } else {
                $error_message = "Error during application submission!";
            }
        } else {
            $error_message = "Please upload a valid resume!";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message_hr'])) {
    $hr_id = $_POST['hr_id'];
    $message = $_POST['message'];

    $query = "INSERT INTO Messages (hr_id, applicant_id, message) 
              VALUES ('$hr_id', '$applicant_id', '$message')";
    if (mysqli_query($conn, $query)) {
        
        header("Location: applicant_dashboard.php?message_sent=1");
        exit();
    } else {
        $error_message = "Error sending message!";
    }
}

$query = "SELECT * FROM Jobs WHERE status = 'Open'";
$result_jobs = mysqli_query($conn, $query);

$query = "SELECT * FROM HR";
$result_hr = mysqli_query($conn, $query);

$query = "SELECT * FROM Applications 
          JOIN Jobs ON Applications.job_id = Jobs.job_id 
          WHERE Applications.applicant_id = '$applicant_id'";
$result_applications = mysqli_query($conn, $query);

$query = "SELECT * FROM Messages 
          WHERE applicant_id = '$applicant_id' 
          AND hr_id IS NOT NULL";
$result_messages = mysqli_query($conn, $query);

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = "Application submitted successfully!";
} elseif (isset($_GET['message_sent']) && $_GET['message_sent'] == 1) {
    $success_message = "Message sent successfully!";
}

$applicant_id = $_SESSION['applicant_id']; 

$sql_applicant_messages = "SELECT Messages.message_id, Messages.message, Messages.sent_at, Messages.hr_reply
                            FROM Messages
                            WHERE Messages.applicant_id = ?";
$stmt_applicant_messages = $conn->prepare($sql_applicant_messages);
$stmt_applicant_messages->bind_param("s", $applicant_id);
$stmt_applicant_messages->execute();
$applicant_messages = $stmt_applicant_messages->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6a82fb, #fc5c7d); 
            color: #333;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: linear-gradient(135deg, #6a82fb, #fc5c7d); 
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        .message, .success, .error {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .success { background-color: #fc5c7d; color: white; }
        .error { background-color: #fc5c7d; color: white; }
        .message { background-color: #fc5c7d; color: white; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        form input, form textarea, form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            
        }
        form button {
            padding: 10px 20px;
            background-color: #fc5c7d; 
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        form button:hover {
            background-color: #6a82fb;
        }
        .job-description {
            display: block;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            max-width: 300px;
        }
        .job-description.full {
            white-space: normal;
            max-width: 100%;
        }
        .read-more-btn {
            color: #fc5c7d;
            cursor: pointer;
            display: inline-block;
            margin-top: 10px;
        }

        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 12px;
            color: #fff;
            background-color: #6a82fb;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .logout-btn:hover {
            background-color: #fc5c7d;
        }

        .message-section {
            margin-top: 30px;
        }

        .message-reply {
            color: #6a82fb;
            font-weight: bold;
        }

        .message-box {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
    <script>
        function toggleDescription(jobId) {
            var description = document.getElementById("description-" + jobId);
            var button = document.getElementById("read-more-btn-" + jobId);
            if (description.classList.contains('full')) {
                description.classList.remove('full');
                button.innerHTML = 'Read More';
            } else {
                description.classList.add('full');
                button.innerHTML = 'Read Less';
            }
        }

        window.onload = function() {
            if (<?php echo isset($_GET['message_sent']) && $_GET['message_sent'] == 1 ? 'true' : 'false'; ?>) {
                setTimeout(function() {
                    var successMessage = document.getElementById('message-success');
                    if (successMessage) {
                        successMessage.style.display = 'none';
                    }
                }, 3000);
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Applicant Dashboard</h1>

        <?php if (isset($message_replied)) { ?>
        <div class="message-box"><?php echo $message_replied; ?></div>
    <?php } ?>

        <h2>Available Job Posts</h2>
        <table>
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Job Description</th>
                    <th>Apply</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($job = mysqli_fetch_assoc($result_jobs)): ?>
                    <tr>
                        <td><?php echo $job['job_title']; ?></td>
                        <td>
                            <div class="job-description" id="description-<?php echo $job['job_id']; ?>">
                                <?php echo $job['job_description']; ?>
                            </div>
                            <span class="read-more-btn" id="read-more-btn-<?php echo $job['job_id']; ?>" onclick="toggleDescription(<?php echo $job['job_id']; ?>)">Read More</span>
                        </td>
                        <td>
                            <form method="POST" action="applicant_dashboard.php" enctype="multipart/form-data">
                                <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
                                <textarea name="application_message" placeholder="Describe why you are the best applicant" required></textarea>
                                <input type="file" name="resume_pdf" accept=".pdf" required>
                                <button type="submit" name="apply_job">Apply</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Your Applied Jobs</h2>
        <table>
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Application Status</th>
                    <th>Resume</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($application = mysqli_fetch_assoc($result_applications)): ?>
                    <tr>
                        <td><?php echo $application['job_title']; ?></td>
                        <td><?php echo $application['application_status']; ?></td>
                        <td>
                            <?php if ($application['resume_pdf_name']): ?>
                                <a href="view_resume.php?application_id=<?php echo $application['application_id']; ?>" target="_blank">View Resume</a>
                            <?php else: ?>
                                No resume uploaded
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="message-section">
        <h2>Messages with HR</h2>
<div class="message-section">
    <form method="POST" action="applicant_dashboard.php">
        <label for="hr_id">Select HR Representative:</label>
        <select name="hr_id" required>
            <?php while ($hr = mysqli_fetch_assoc($result_hr)): ?>
                <option value="<?php echo $hr['hr_id']; ?>">
                    <?php echo htmlspecialchars($hr['hr_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <textarea name="message" placeholder="Type your message to HR..." required></textarea>
        <button type="submit" name="message_hr">Send Message</button>
    </form>

    <h2>Your Message History</h2>
<table>
    <tr>
        <th>HR Reply</th>
        <th>Message</th>
        <th>Sent At</th>
    </tr>
    <?php while ($msg = $applicant_messages->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $msg['hr_reply']; ?></td>
        <td><?php echo $msg['message']; ?></td>
        <td><?php echo $msg['sent_at']; ?></td>
    </tr>
    <?php } ?>
</table>


</div>


        <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>

    </div>
</body>
</html>
