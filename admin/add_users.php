<?php
session_start();
require_once '../config/db.php';

$message = "";

if (isset($_POST['add_user'])) {
    $role = $_POST['role'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if ($role === 'teacher') {
        // Check if email already exists in teachers table
        $check = $conn->prepare("SELECT id FROM teachers WHERE email = ?");
    } elseif ($role === 'student') {
        // Check if email already exists in students table
        $check = $conn->prepare("SELECT id FROM students WHERE email = ?");
    }

    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "❌ User with this email already exists!";
    } else {
        if ($role === 'teacher') {
            $stmt = $conn->prepare("INSERT INTO teachers (name, email, password) VALUES (?, ?, ?)");
        } elseif ($role === 'student') {
            $stmt = $conn->prepare("INSERT INTO students (name, email, password) VALUES (?, ?, ?)");
        }

        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {
            $message = "✅ User added successfully!";
        } else {
            $message = "❌ Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Users</title>
    <style>
        /* Reset some default styles */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Poppins", sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #fff;
            padding: 20px; /* Reduced padding */
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 15px; /* Reduced margin */
            font-weight: 600;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px; /* Reduced gap */
            text-align: left;
        }

        label {
            color: #555;
            font-weight: 500;
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

        input, select {
            padding: 10px; /* Reduced padding */
            border-radius: 10px;
            border: 1px solid #ddd;
            font-size: 16px;
            outline: none;
            transition: all 0.3s ease;
        }

        input:focus, select:focus {
            border-color: #2575fc;
            box-shadow: 0 0 5px rgba(37, 117, 252, 0.2);
        }

        button {
            background-color: #2575fc;
            color: white;
            padding: 10px; /* Reduced padding */
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #1a5ae0;
        }

        p {
            font-size: 14px;
            color: #e74c3c;
        }

        /* Style for error/success messages */
        .message {
            padding: 10px; /* Reduced padding */
            border-radius: 8px;
            font-size: 16px;
        }

        .message.success {
            background-color: #28a745;
            color: #fff;
        }

        .message.error {
            background-color: #dc3545;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="../auth/logout.php" class="logout-btn">Logout</a>
    <h2>Add Teacher or Student</h2>
    <?php if ($message): ?>
        <div class="message <?php echo strpos($message, "❌") !== false ? 'error' : 'success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form method="POST">
        <label for="role">Role:</label>
        <select name="role" id="role" required onchange="toggleClassInput(this.value)">
            <option value="teacher">Teacher</option>
            <option value="student">Student</option>
        </select><br><br>

        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br><br>

        <button type="submit" name="add_user">Add User</button>
    </form>
</div>
</body>
</html>
