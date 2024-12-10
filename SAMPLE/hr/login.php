<?php
include('../core/dbConfig.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM HR WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $hr = $result->fetch_assoc();
        if (password_verify($password, $hr['password'])) {
            $_SESSION['hr_id'] = $hr['hr_id'];
            $_SESSION['hr_name'] = $hr['hr_name'];
            header('Location: manage_jobs.php');
            exit();
        } else {
            $error_message = "Incorrect password!";
        }
    } else {
        $error_message = "HR not found!";
    }
}

if (isset($_GET['register']) && $_GET['register'] == 'true') {
    $register_form = true;
} else {
    $register_form = false;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $hr_name = $_POST['hr_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $date_of_registration = date('Y-m-d');
    
    $sql = "SELECT * FROM HR WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $error_message = "Email is already registered!";
    } else {
        
        $sql = "INSERT INTO HR (hr_name, email, password, created_at) 
        VALUES ('$hr_name', '$email', '$password', CURRENT_TIMESTAMP)";

        
        if ($conn->query($sql) === TRUE) {
            $success_message = "Registered successfully! Please log in.";
            $register_form = false;
        } else {
            $error_message = "Error: " . $conn->error;
        }
    }
}
?>

<style>
    body {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background: linear-gradient(135deg, #6a82fb, #fc5c7d); 
        font-family: 'Arial', sans-serif;
        color: #fff;
        margin: 0;
    }

    .container {
        background: linear-gradient(135deg, #6a82fb, #fc5c7d);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        width: 100%;
        max-width: 400px;
        text-align: center;
    }

    h1 {
        margin-bottom: 20px;
        font-size: 2rem;
        color: #ffffff;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    input {
        padding: 12px;
        font-size: 1rem;
        border: none;
        border-radius: 10px;
        outline: none;
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        transition: all 0.3s ease;
    }

    input::placeholder {
        color: #eee;
    }

    input:focus {
        background: rgba(255, 255, 255, 0.4);
        box-shadow: 0 0 8px rgba(255, 255, 255, 0.5);
        transform: scale(1.02);
    }

    button {
        padding: 12px;
        font-size: 1.1rem;
        font-weight: bold;
        color: #fff;
        background: linear-gradient(135deg, #6a82fb, #fc5c7d);
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    button:hover {
        background: linear-gradient(135deg, #6a82fb, #fc5c7d); 
        transform: translateY(-3px);
    }

    button:active {
        transform: translateY(1px);
    }

    .error, .success {
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 10px;
        font-weight: bold;
    }

    .error {
        background: linear-gradient(135deg, #6a82fb, #fc5c7d); 
    }

    .success {
        background: linear-gradient(135deg, #6a82fb, #fc5c7d); 
    }

    p {
        margin-top: 15px;
    }

    a {
        color: #fc5c7d;
        text-decoration: none;
        font-weight: bold;
        transition: color 0.3s ease;
    }

    a:hover {
        text-decoration: underline;
        color: #fff;
    }
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Login</title>

<div class="container">

    <?php if (isset($error_message)): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if (isset($success_message)): ?>
        <div class="success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if (!$register_form): ?>
        <h1>HR Login</h1>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit" name="login">Login</button>
        </form>
        
        <p>Don't have an account? <a href="login.php?register=true">Register here</a></p>
    <?php else: ?>
        
        <h1>HR Registration</h1>
        <form method="POST">
            <input type="text" name="hr_name" placeholder="Full Name" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit" name="register">Register</button>
        </form>
        
        <p>Already have an account? <a href="login.php">Login here</a></p>
    <?php endif; ?>
</div>
