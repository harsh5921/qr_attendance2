<!DOCTYPE html>
<html>
<head>
    <title>QR Attendance System</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background-color: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 320px;
            animation: fadeIn 1s ease-in-out;
        }

        h2 {
            margin-bottom: 25px;
            color: #333;
        }

        label {
            font-size: 16px;
            color: #555;
        }

        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            margin-top: 8px;
        }

        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background: #4facfe;
            border: none;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #00c6fb;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome to QR Attendance System</h2>

        <form method="POST" action="">
            <label>Select Role:</label><br><br>
            <select name="role" required>
                <option value="">-- Select Role --</option>
                <option value="admin">Admin</option>
                <option value="teacher">Teacher</option>
                <option value="student">Student</option>
            </select><br><br>
            <button type="submit">Continue</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $role = $_POST['role'];
            switch ($role) {
                case 'admin':
                    header("Location: admin/login.php");
                    exit();
                case 'teacher':
                    header("Location: teacher/login.php");
                    exit();
                case 'student':
                    header("Location: student/login.php");
                    exit();
                default:
                    echo "<p style='color:red;'>Invalid role selected!</p>";
            }
        }
        ?>
    </div>
</body>
</html>
