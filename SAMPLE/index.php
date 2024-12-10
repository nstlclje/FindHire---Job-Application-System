<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindHire - Job Application System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #6a82fb, #fc5c7d); 
            color: #fff;
            overflow: hidden;
        }

        .container {
            text-align: center;
            background: linear-gradient(135deg, #6a82fb, #fc5c7d); 
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(10px); 
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 100%;
            animation: fadeIn 1s ease-in-out;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 25px;
            color: #fff;
            text-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
        }

        .choice-buttons {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .role-btn {
            padding: 15px;
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: 1px;
            color: #fff;
            background: linear-gradient(135deg, #6a82fb, #fc5c7d); 
            border: none;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .role-btn:hover {
            transform: scale(1.05);
            background: linear-gradient(135deg, #6a82fb, #fc5c7d); 
        }

        .role-btn:active {
            transform: scale(0.98);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 2rem;
            }

            .role-btn {
                font-size: 1rem;
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to FindHire</h1>
        <div class="choice-buttons">
            <a href="applicants/login.php"><button class="role-btn">Applicant</button></a>
            <a href="hr/login.php"><button class="role-btn">HR</button></a>
        </div>
    </div>
</body>
</html>
