<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if (isset($_POST['create'])) {
    $class_name = $_POST['class_name'];
    $teacher_id = $_SESSION['teacher_id'];

    // Check if the class already exists for the teacher
    $stmt = $conn->prepare("SELECT * FROM classes WHERE teacher_id = ? AND class_name = ?");
    $stmt->bind_param("is", $teacher_id, $class_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $message = "Class with this name already exists!";
    } else {
        // If the class doesn't exist, proceed with inserting
        $stmt = $conn->prepare("INSERT INTO classes (teacher_id, class_name) VALUES (?, ?)");
        $stmt->bind_param("is", $teacher_id, $class_name);

        if ($stmt->execute()) {
            $message = "Class created successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Class</title>
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

        .form-container {
            width: 80%;
            max-width: 600px;
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
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

        h2 {
            color: #333;
            margin-bottom: 30px;
            font-weight: 600;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #2575fc;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #1a5ae0;
        }

        .message {
            margin: 20px 0;
            font-size: 16px;
        }

        .message.success {
            color: green;
        }

        .message.error {
            color: red;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <a href="../auth/logout.php" class="logout-btn">Logout</a>
        <h2>Create a New Class</h2>

        <?php if ($message) { ?>
            <div class="message <?php echo (strpos($message, 'Error') !== false) ? 'error' : 'success'; ?>">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <form method="POST">
            <input type="text" name="class_name" placeholder="Enter Class Name" required>
            <button type="submit" name="create">Create</button>
        </form>
    </div>

</body>
</html>