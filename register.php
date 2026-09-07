<?php
include 'dbconnect.php';

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$success = false;
$message = '';
$messageType = '';
$firstname = '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $message = 'Please use the registration form to submit your interest.';
    $messageType = 'warning';

} else {
    $firstname = trim($_POST['firstname'] ?? '');
    $surname = trim($_POST['surname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $termsAccepted = ($_POST['terms'] ?? '') === 'yes';

    $validNamePattern = "/^[\p{L}][\p{L}\s'-]{0,49}$/u";

    if (
        !preg_match($validNamePattern, $firstname) ||
        !preg_match($validNamePattern, $surname)
    ) {
        $message = 'Please enter a valid first name and surname.';
        $messageType = 'warning';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
        $message = 'Please enter a valid email address.';
        $messageType = 'warning';

    } elseif (!$termsAccepted) {
        $message = 'You must accept the terms and conditions to register.';
        $messageType = 'warning';

    } else {
        try {
            $conn = new PDO(
                "mysql:host=$servername;dbname=$database;charset=utf8mb4",
                $username,
                $password
            );

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("
                INSERT INTO interest (firstname, surname, email, terms)
                VALUES (:firstname, :surname, :email, :terms)
            ");

            $stmt->execute([
                ':firstname' => $firstname,
                ':surname' => $surname,
                ':email' => $email,
                ':terms' => 1
            ]);

            $success = true;

        } catch (PDOException $exception) {
            $message = 'We could not save your registration right now. Please try again later.';
            $messageType = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration – Cit-E Cycling</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f0f4f8;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .result-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 30px rgba(0,0,0,.12);
            max-width: 460px;
            width: 100%;
        }

        .result-card .card-body {
            padding: 2.5rem;
        }

        .icon-big {
            font-size: 3.5rem;
        }

        .btn {
            border-radius: 10px;
        }
    </style>
</head>
<body>

<main class="container d-flex justify-content-center">
    <div class="card result-card">
        <div class="card-body text-center">

            <?php if ($success) { ?>

                <div class="icon-big text-success mb-3">
                    <i class="bi bi-check-circle-fill"></i>
                </div>

                <h1 class="h4 fw-bold">You’re Registered!</h1>

                <p class="text-muted">
                    Thanks <strong><?= h($firstname) ?></strong>. We have noted your interest
                    and will be in touch about future Cit-E Cycling events.
                </p>

                <a href="index.html" class="btn btn-primary px-4">
                    <i class="bi bi-house-fill me-1"></i>Back to Home
                </a>

            <?php } else { ?>

                <div class="icon-big text-<?= h($messageType ?: 'danger') ?> mb-3">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>

                <h1 class="h4 fw-bold">Registration Not Completed</h1>

                <p class="text-muted"><?= h($message) ?></p>

                <a href="register_form.html" class="btn btn-outline-primary px-4">
                    <i class="bi bi-arrow-left me-1"></i>Back to Form
                </a>

            <?php } ?>

        </div>
    </div>
</main>

</body>
</html>