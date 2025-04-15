<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$students = [];
$class_id = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = $_POST['class_id'];
    $date = $_POST['date'];
    $student_ids = $_POST['student_id'];
    $statuses = $_POST['status'];

    foreach ($student_ids as $index => $student_id) {
        $status = $statuses[$index];

        // Prepare update query
        $update = $conn->prepare("UPDATE attendance SET status = ? WHERE student_id = ? AND class_id = ? AND date = ?");
        if (!$update) {
            die("Prepare failed: " . $conn->error);
        }

        $update->bind_param("siis", $status, $student_id, $class_id, $date);
        $update->execute();
    }

    $message = "✅ Attendance updated successfully!";
}

// Fetch students and current attendance status
if (isset($_GET['class_id']) && isset($_GET['date'])) {
    $class_id = $_GET['class_id'];
    $date = $_GET['date'];

    $stmt = $conn->prepare("
        SELECT s.id, s.name, a.status 
        FROM students s
        LEFT JOIN attendance a ON s.id = a.student_id AND a.class_id = ? AND a.date = ?
        WHERE a.class_id = ?
    ");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("isi", $class_id, $date, $class_id);
    $stmt->execute();
    $students = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Attendance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            font-family: "Poppins", sans-serif;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 30px 10px;
        }

        .container {
            width: 100%;
            max-width: 900px;
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 500;
            margin: 10px 0 5px;
        }

        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        button {
            margin-top: 15px;
            padding: 12px 20px;
            background-color: #2575fc;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #1a5ae0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: #f0f0f0;
        }

        .success-msg {
            color: green;
            text-align: center;
            margin-bottom: 15px;
        }

        .error-msg {
            color: red;
            text-align: center;
            margin-top: 15px;
        }

        @media screen and (max-width: 600px) {
            table th, table td {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <a href="../auth/logout.php" class="logout-btn">Logout</a>
    <h2>Update Attendance (Manual)</h2>

    <?php if ($message): ?>
        <p class="success-msg"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="GET">
        <label>Class ID:</label>
        <input type="number" name="class_id" required value="<?php echo htmlspecialchars($class_id); ?>">

        <label>Date:</label>
        <input type="date" name="date" required value="<?php echo htmlspecialchars($_GET['date'] ?? date('Y-m-d')); ?>">

        <button type="submit">Load Attendance</button>
    </form>

    <?php if ($students && $students->num_rows > 0): ?>
        <form method="POST">
            <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_id); ?>">
            <input type="hidden" name="date" value="<?php echo htmlspecialchars($_GET['date']); ?>">

            <table>
                <tr>
                    <th>Student Name</th>
                    <th>Status</th>
                </tr>
                <?php while ($row = $students->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td>
                            <input type="hidden" name="student_id[]" value="<?php echo $row['id']; ?>">
                            <select name="status[]">
                                <option value="Present" <?php echo $row['status'] == 'Present' ? 'selected' : ''; ?>>Present</option>
                                <option value="Absent" <?php echo $row['status'] == 'Absent' ? 'selected' : ''; ?>>Absent</option>
                            </select>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <button type="submit">Update Attendance</button>
        </form>
    <?php elseif ($class_id && isset($_GET['date'])): ?>
        <p class="error-msg">No attendance data found for Class ID <?php echo htmlspecialchars($class_id); ?> on <?php echo htmlspecialchars($_GET['date']); ?>.</p>
    <?php endif; ?>
</div>

</body>
</html>