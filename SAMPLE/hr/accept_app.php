<?php
session_start();

if (!isset($_SESSION['hr_id'])) {
    header("Location: login.php");
    exit();
}

include('../core/dbConfig.php');

if (isset($_GET['application_id'])) {
    $application_id = $_GET['application_id'];

    $sql = "UPDATE Applications SET application_status = 'Accepted' WHERE application_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $application_id);

    if ($stmt->execute()) {

        $message = "Congratulations! Your application has been accepted.";
        $insert_message = "INSERT INTO Messages (applicant_id, hr_id, message, sent_at)
                           SELECT applicant_id, ? AS hr_id, ? AS message, NOW() 
                           FROM Applications WHERE application_id = ?";
        $stmt_message = $conn->prepare($insert_message);
        $stmt_message->bind_param("isi", $_SESSION['hr_id'], $message, $application_id);
        $stmt_message->execute();

        $_SESSION['message'] = "Application accepted successfully, and the applicant has been notified.";
    } else {
        $_SESSION['message'] = "Error updating application: " . $stmt->error;
    }
} else {
    $_SESSION['message'] = "No application ID provided.";
}

header("Location: manage_jobs.php");
exit();
?>
