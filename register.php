<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register your interest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', sans-serif; min-height: 100vh; display: flex; align-items: center; }
        .result-card { border: none; border-radius: 20px; box-shadow: 0 4px 30px rgba(0,0,0,.12); max-width: 460px; width: 100%; }
        .result-card .card-body { padding: 2.5rem; }
        .icon-big { font-size: 3.5rem; }
        .btn { border-radius: 10px; }
    </style>
</head>
<body>
<div class="container d-flex justify-content-center">
    <div class="card result-card">
        <div class="card-body text-center">
        <?php
        include 'dbconnect.php';
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
                $surname   = isset($_POST['surname'])   ? trim($_POST['surname'])   : '';
                $email     = isset($_POST['email'])     ? trim($_POST['email'])     : '';
                $terms     = isset($_POST['terms'])     ? 1 : 0;

                // Robust validation to prevent blank empty rows
                if (empty($firstname) || empty($surname) || empty($email) || $terms == 0) {
                    echo "<div class='icon-big text-warning mb-3'><i class='bi bi-exclamation-triangle-fill'></i></div>";
                    echo "<h4 class='fw-bold'>Incomplete Form</h4>";
                    echo "<p class='text-muted'>Please complete all fields and accept the terms before submitting.</p>";
                    echo "<a href='register_form.html' class='btn btn-outline-primary px-4'><i class='bi bi-arrow-left me-1'></i>Go Back</a>";
                } else {
                    $stmt = $conn->prepare("INSERT INTO interest (firstname, surname, email, terms) VALUES (:f, :s, :e, :t)");
                    $stmt->bindParam(':f', $firstname);
                    $stmt->bindParam(':s', $surname);
                    $stmt->bindParam(':e', $email);
                    $stmt->bindParam(':t', $terms);

                    if ($stmt->execute()) {
                        echo "<div class='icon-big text-success mb-3'><i class='bi bi-check-circle-fill'></i></div>";
                        echo "<h4 class='fw-bold'>You're Registered!</h4>";
                        echo "<p class='text-muted'>Thanks <strong>" . htmlspecialchars($firstname) . "</strong>, we've noted your interest and will be in touch soon.</p>";
                        echo "<a href='index.html' class='btn btn-primary px-4'><i class='bi bi-house-fill me-1'></i>Back to Home</a>";
                    }
                }
            }
        }
        catch(PDOException $e) {
            echo "<div class='icon-big text-danger mb-3'><i class='bi bi-database-x'></i></div>";
            echo "<h4 class='fw-bold'>Something went wrong</h4>";
            echo "<p class='text-muted small'>" . $e->getMessage() . "</p>";
            echo "<a href='register_form.html' class='btn btn-outline-secondary px-4'>Try Again</a>";
        }
        ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
