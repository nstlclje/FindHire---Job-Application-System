<?php
session_start();

if (!isset($_SESSION['hr_id'])) {
    header("Location: login.php");
    exit();
}

$hr_id = $_SESSION['hr_id'];

include('../core/dbConfig.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['job_title']) && isset($_POST['job_description'])) {
    $job_title = $_POST['job_title'];
    $job_description = $_POST['job_description'];

    $sql = "INSERT INTO Jobs (hr_id, job_title, job_description, status) VALUES (?, ?, ?, 'Open')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $hr_id, $job_title, $job_description);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Job posted successfully!";
        header("Location: manage_jobs.php");
        exit();
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
        header("Location: manage_jobs.php");
        exit();
    }
}

$sql = "SELECT * FROM Jobs WHERE hr_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $hr_id);
$stmt->execute();
$jobs = $stmt->get_result();

$sql_applicants = "SELECT Applications.application_id, Jobs.job_title, Applicants.applicant_name, Applicants.resume_pdf_name, Applications.application_status, Applicants.applicant_id
                   FROM Applications
                   JOIN Jobs ON Applications.job_id = Jobs.job_id
                   JOIN Applicants ON Applications.applicant_id = Applicants.applicant_id
                   WHERE Jobs.hr_id = ?";
$stmt_applicants = $conn->prepare($sql_applicants);
$stmt_applicants->bind_param("s", $hr_id);
$stmt_applicants->execute();
$applications = $stmt_applicants->get_result();

$sql_messages = "SELECT Messages.message_id, Applicants.applicant_name, Messages.message, Messages.sent_at, Messages.applicant_id
                 FROM Messages
                 JOIN Applicants ON Messages.applicant_id = Applicants.applicant_id
                 WHERE Messages.hr_id = ?";
$stmt_messages = $conn->prepare($sql_messages);
$stmt_messages->bind_param("s", $hr_id);
$stmt_messages->execute();
$messages = $stmt_messages->get_result();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['hr_reply']) && isset($_POST['message_id'])) {
    $message_id = $_POST['message_id'];
    $hr_reply = $_POST['hr_reply'];

    $query = "UPDATE Messages SET hr_reply = ? WHERE message_id = ? AND hr_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $hr_reply, $message_id, $hr_id);

    if ($stmt->execute()) {

        $_SESSION['message'] = "Reply sent successfully!";

        header("Location: applicant_dashboard.php");
        exit(); 
    } else {
        $_SESSION['error_message'] = "Error while replying!";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['message_id']) && isset($_POST['hr_reply'])) {
       
        $message_id = $_POST['message_id'];
        $hr_reply = $_POST['hr_reply'];

        $sql = "UPDATE Messages SET hr_reply = ? WHERE message_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hr_reply, $message_id);
        
        if ($stmt->execute()) {
            echo "Reply sent successfully!";
        } else {
            echo "Error sending reply.";
        }
    } else {
        echo "Missing required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #6a82fb, #fc5c7d); 
            color: #fff;
        }

        .container {
            width: 80%;
            margin: 50px auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.1); 
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        h1 {
            font-size: 2.5rem;
            color: #fff;
            text-align: center;
            margin-bottom: 30px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        h2 {
            font-size: 1.8rem;
            color: #fff;
            margin-bottom: 15px;
        }

        form input, form textarea, form button {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border-radius: 10px;
            border: 1px solid #ddd;
            font-size: 16px;
            box-sizing: border-box;
        }

        form button {
            background-color: #fc5c7d; 
            color: white;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            border: none;
        }

        form button:hover {
            background-color: #6a82fb;
            transform: translateY(-3px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        table th, table td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: #f8f9fa;
            color: #333;
        }

        a {
            text-decoration: none;
            color: #fc5c7d;
        }

        a:hover {
            text-decoration: underline;
        }

        .actions a {
            margin-right: 10px;
            color: #fc5c7d;
        }

        .actions a:hover {
            text-decoration: underline;
        }

        .logout-btn {
            padding: 12px 20px;
            color: #fff;
            background-color: #6a82fb;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            position: absolute;
            top: 20px;
            right: 20px;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: #fc5c7d;
        }

        .message-box {
            background-color: #fff;
            color: #333;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .message-box.success {
            background-color: #fc5c7d;
            color: white;
        }

        .message-box.error {
            background-color: #fc5c7d;
            color: white;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Manage Jobs</h1>

        <?php if (isset($_SESSION['message'])) { ?>
            <div class="message-box success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php } ?>

        <h2>Create New Job Post</h2>
        <form method="POST">
            <input type="text" name="job_title" placeholder="Job Title" required><br>
            <textarea name="job_description" placeholder="Job Description" required></textarea><br>
            <button type="submit">Post Job</button>
        </form>

        <h2>Your Job Posts</h2>
        <table>
            <tr>
                <th>Job Title</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $jobs->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['job_title']; ?></td>
                <td><a href="view_job.php?job_id=<?php echo $row['job_id']; ?>" style="color: pink;"><?php echo $row['status']; ?></a></td>
                <td class="actions">
                    <a href="edit_job.php?job_id=<?php echo $row['job_id']; ?>">Edit</a>
                    <a href="delete_job.php?job_id=<?php echo $row['job_id']; ?>">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </table>

        <h2>Applicants Application</h2>
        <table>
            <tr>
                <th>Job Title</th>
                <th>Applicant Name</th>
                <th>Resume</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while ($app = $applications->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $app['job_title']; ?></td>
                <td><?php echo $app['applicant_name']; ?></td>
                <td><a href="resumes/<?php echo $app['resume_pdf_name']; ?>" target="_blank">View Resume</a></td>
                <td><?php echo $app['application_status']; ?></td>
                <td class="actions">
                    <?php if ($app['application_status'] == 'Pending') { ?>
                        <a href="accept_app.php?application_id=<?php echo $app['application_id']; ?>" style="color: pink;">Accept</a>
                        <a href="reject_app.php?application_id=<?php echo $app['application_id']; ?>" style="color: pink;">Reject</a>
                    <?php } elseif ($app['application_status'] == 'Accepted') { ?>
                        <span style="color: purple; font-weight: bold;">Accepted</span>
                    <?php } elseif ($app['application_status'] == 'Rejected') { ?>
                        <span style="color: purple; font-weight: bold;">Rejected</span>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </table>

        <h2>Messages from Applicant</h2>
<table>
    <tr>
        <th>Applicant Name</th>
        <th>Message</th>
        <th>Sent At</th>
        <th>Action</th>
    </tr>
    <?php while ($msg = $messages->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $msg['applicant_name']; ?></td>
        <td><?php echo $msg['message']; ?></td>
        <td><?php echo $msg['sent_at']; ?></td>
        <td>
            <form method="POST" action="manage_jobs.php" style="display:inline;">
                <input type="hidden" name="message_id" value="<?php echo $msg['message_id']; ?>">
                <textarea name="hr_reply" placeholder="Write your reply..." required></textarea><br>
                <button type="submit">Reply</button>
            </form>
        </td>
    </tr>
    <?php } ?>
</table>


        <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
    </div>

</body>
</html>
