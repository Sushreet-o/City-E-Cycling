<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Cit-E Cycling</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a237e 0%, #1565c0 60%, #0288d1 100%);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .card { border: none; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,.3); max-width: 420px; width: 100%; }
        .card-body { padding: 2.5rem; }
        .alert { border-radius: 12px; font-size: .95rem; }
        .btn { border-radius: 10px; }
    </style>
</head>
<body>
<div class="px-3" style="width:100%;max-width:420px;margin:auto;">
    <div class="card">
        <div class="card-body text-center">
            <div style="font-size:2.5rem;">&#128690;</div>
            <h4 class="fw-bold mb-3">Cit-E Cycling</h4>
            <?php
                include 'dbconnect.php';

                if ($_SERVER['REQUEST_METHOD'] == 'POST'){
                    try {
                        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $user_input = isset($_POST['username']) ? trim($_POST['username']) : '';
                        $pass_input = isset($_POST['password']) ? trim($_POST['password']) : '';

                        if (empty($user_input) || empty($pass_input)) {
                            echo "<div class='alert alert-warning d-flex align-items-center gap-2 text-start'>
                                    <i class='bi bi-exclamation-triangle-fill fs-5'></i>
                                    <div><strong>Missing fields</strong><br>Please fill in all login fields.</div>
                                  </div>";
                            echo "<a href='admin_login.html' class='btn btn-outline-secondary w-100'><i class='bi bi-arrow-left me-1'></i>Go Back</a>";
                        } else {
                            $stmt = $conn->prepare("SELECT * FROM user WHERE username = :user AND password = :pass");
                            $stmt->bindParam(':user', $user_input);
                            $stmt->bindParam(':pass', $pass_input);
                            $stmt->execute();
                            $user = $stmt->fetch(PDO::FETCH_ASSOC);

                            if ($user) {
                                $_SESSION['admin_logged_in'] = true;
                                header("Location: admin_menu.php");
                                exit;
                            } else {
                                echo "<div class='alert alert-danger d-flex align-items-center gap-2 text-start'>
                                        <i class='bi bi-shield-x-fill fs-5'></i>
                                        <div><strong>Access Denied</strong><br>The username or password is incorrect.</div>
                                      </div>";
                                echo "<a href='admin_login.html' class='btn btn-primary w-100'><i class='bi bi-arrow-left me-1'></i>Try Again</a>";
                            }
                        }
                    }
                    catch(PDOException $e) {
    echo "<div class='alert alert-danger'>
            <i class='bi bi-database-x me-2'></i>
            The login service is temporarily unavailable. Please try again later.
          </div>";
}
                } else {
                    echo "<div class='alert alert-warning'><i class='bi bi-exclamation-triangle me-2'></i>Direct access not allowed.</div>";
                    echo "<a href='admin_login.html' class='btn btn-outline-secondary w-100'>Back to Login</a>";
                }
            ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
