<?php
include('../core/dbConfig.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hr_name = $_POST['hr_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $date_of_registration = date('Y-m-d');
    
    $sql = "INSERT INTO HR (hr_name, email, password, date_of_registration) 
            VALUES ('$hr_name', '$email', '$password', '$date_of_registration')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Registered successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<style>
    body {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
        background: linear-gradient(135deg, #6a82fb, #fc5c7d); 
        font-family: 'Arial', sans-serif;
        color: #fff;
    }

    .container {
        background: linear-gradient(135deg, #6a82fb, #fc5c7d); 
        padding: 35px;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(10px);
        max-width: 400px;
        width: 100%;
        text-align: center;
        transition: transform 0.3s;
    }

    .container:hover {
        transform: scale(1.02);
    }

    h1 {
        margin-bottom: 25px;
        font-size: 2rem;
        color: #fff;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    input {
        padding: 12px;
        font-size: 1rem;
        border: none;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.3);
        color: #fff;
        outline: none;
        transition: background 0.3s;
    }

    input::placeholder {
        color: #ddd;
    }

    input:focus {
        background: rgba(255, 255, 255, 0.5);
    }

    button {
        padding: 12px;
        font-size: 1.1rem;
        font-weight: bold;
        color: #fff;
        background: #ff6347;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        transition: background 0.3s, transform 0.2s;
    }

    button:hover {
        background: #e53e30;
        transform: translateY(-3px);
    }

    button:active {
        transform: translateY(1px);
    }

    p {
        margin-top: 15px;
        font-size: 0.95rem;
        color: #fc5c7d;
    }

    a {
        color: #fc5c7d;
        text-decoration: none;
        transition: color 0.3s;
    }

    a:hover {
        color: #fff;
        text-decoration: underline;
    }
</style>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Registration</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1>HR Registration</h1>
        <form method="POST">
            <input type="text" name="hr_name" placeholder="Full Name" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
