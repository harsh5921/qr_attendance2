<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../qr/phpqrcode/qrlib.php'; // QR Code Library

$filename = '';
$data = '';

// Fetch students from DB for dropdown
$students = [];
$stmt = $conn->prepare("SELECT id, name FROM students");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

// Generate QR code using only student ID and overwrite existing
if (isset($_POST['generate'])) {
    $student_id = $_POST['student_id'];
    $data = "student_id=$student_id";
    $filename = "../qr/qr_student_{$student_id}.png";

    if (file_exists($filename)) {
        unlink($filename);
    }

    QRcode::png($data, $filename, QR_ECLEVEL_L, 5);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate Student QR</title>
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
            min-height: 100vh;
        }

        .qr-container {
            width: 90%;
            max-width: 600px;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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

        label {
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
            color: #444;
        }

        select, button {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        button {
            background-color: #2575fc;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #1a5ae0;
        }

        .qr-preview {
            text-align: center;
            margin-top: 20px;
        }

        .qr-preview img {
            max-width: 200px;
            height: auto;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="qr-container">
    <a href="../auth/logout.php" class="logout-btn">Logout</a>
    <h2>Generate QR Code for Student Attendance</h2>

    <form method="POST">
        <label for="student">Select Student:</label>
        <select name="student_id" id="student" required>
            <option value="">-- Select Student --</option>
            <?php foreach ($students as $student): ?>
                <option value="<?php echo $student['id']; ?>">
                    <?php echo htmlspecialchars($student['name']); ?> (ID: <?php echo $student['id']; ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="generate">Generate QR</button>
    </form>

    <?php if (!empty($filename)): ?>
        <div class="qr-preview">
            <h3>Generated QR Code:</h3>
            <img src="<?php echo $filename; ?>?v=<?php echo time(); ?>" alt="QR Code">
        </div>
    <?php endif; ?>
</div>

</body>
</html>
