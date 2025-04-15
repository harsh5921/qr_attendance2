<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$student_id = $_SESSION['student_id'];

// Fetch class list for dropdown
$classes = [];
$class_stmt = $conn->prepare("SELECT id, class_name FROM classes");
$class_stmt->execute();
$result = $class_stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}

// Attendance submission logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qr_data = trim($_POST['qr_data']);
    $class_id = $_POST['class_id'];
    $date = date("Y-m-d");

    if (!empty($qr_data) && !empty($class_id)) {
        parse_str($qr_data, $parsed);

        if (isset($parsed['student_id'])) {
            $qr_student_id = $parsed['student_id'];

            if ($qr_student_id != $student_id) {
                $message = "<span style='color:red;'>⚠️ QR code does not belong to you!</span>";
            } else {
                // Check for existing attendance
                $check = $conn->prepare("SELECT * FROM attendance WHERE student_id = ? AND class_id = ? AND date = ?");
                $check->bind_param("iis", $student_id, $class_id, $date);
                $check->execute();
                $result = $check->get_result();

                if ($result->num_rows > 0) {
                    $message = "✅ Attendance already marked!";
                } else {
                    $stmt = $conn->prepare("INSERT INTO attendance (student_id, class_id, date, status) VALUES (?, ?, ?, 'Present')");
                    $stmt->bind_param("iis", $student_id, $class_id, $date);
                    $stmt->execute();
                    $message = "✅ Attendance marked successfully!";
                }
            }
        } else {
            $message = "<span style='color:red;'>❌ Invalid QR data!</span>";
        }
    } else {
        $message = "<span style='color:red;'>⚠️ All fields are required!</span>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mark Attendance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Poppins", sans-serif;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
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

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 90%;
            max-width: 500px;
        }

        h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: 500;
            display: block;
            margin-bottom: 8px;
            color: #444;
        }

        select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 15px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #2575fc;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #1a5ae0;
        }

        #reader {
            width: 100%;
            margin-top: 20px;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            font-size: 15px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .info {
            color: orange;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="../auth/logout.php" class="logout-btn">Logout</a>
        <h2>Mark Your Attendance</h2>

        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST" id="attendanceForm">
            <div class="form-group">
                <label for="class_id">Select Class:</label>
                <select name="class_id" id="class_id" required>
                    <option value="">-- Select Class --</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['class_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="reader"></div>
            <input type="hidden" name="qr_data" id="qr_data">
            <button type="submit" id="submitBtn">Submit Attendance</button>
        </form>
    </div>

    <script>
        function onScanSuccess(qrData) {
            document.getElementById('qr_data').value = qrData;
            const classSelected = document.getElementById("class_id").value;
            if (classSelected !== "") {
                document.getElementById('attendanceForm').submit();
            } else {
                alert("Please select a class before scanning the QR.");
            }
        }

        function onScanError(error) {
            console.warn("QR Scan Error: ", error);
        }

        const html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess, onScanError);
    </script>
</body>
</html>