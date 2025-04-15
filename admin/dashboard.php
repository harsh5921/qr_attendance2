<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: "Poppins", sans-serif;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .dashboard-container {
            width: 80%;
            max-width: 900px;
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .logout-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            position: absolute;
            top: 20px;
            right: 20px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }

        .nav-list {
            list-style-type: none;
            padding: 0;
        }

        .nav-list li {
            margin-bottom: 15px;
        }

        .nav-list li a {
            text-decoration: none;
            color: #2575fc;
            font-size: 18px;
            transition: color 0.3s;
        }

        .nav-list li a:hover {
            color: #1a5ae0;
        }

    </style>
</head>
<body>

    <div class="dashboard-container">
        <a href="../auth/logout.php" class="logout-btn">Logout</a>
        <h2>Welcome, <?php echo $_SESSION['admin_name']; ?>!</h2>
        <ul class="nav-list">
            <li><a href="add_users.php">Add Teachers/Students</a></li>
            <!-- Add more admin features here -->
        </ul>
    </div>

</body>
</html>
