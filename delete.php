<?php
session_start();

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin_login.html");
    exit;
}

include 'dbconnect.php';

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$participant = null;
$message = '';
$messageType = '';
$deleteSucceeded = false;

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$database;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = filter_input(
            INPUT_POST,
            'id',
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        $submittedToken = $_POST['csrf_token'] ?? '';
        $storedToken = $_SESSION['delete_csrf_token'] ?? '';

        if ($id === false || $id === null || !hash_equals($storedToken, $submittedToken)) {
            $message = 'This delete request is invalid or has expired. Please try again.';
            $messageType = 'danger';
        } else {
            $stmt = $conn->prepare(
                "DELETE FROM participant WHERE id = :id"
            );
            $stmt->execute([':id' => $id]);

            unset($_SESSION['delete_csrf_token']);

            if ($stmt->rowCount() === 1) {
                $deleteSucceeded = true;
            } else {
                $message = 'The participant could not be found. It may already have been deleted.';
                $messageType = 'warning';
            }
        }

    } else {
        $id = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if ($id === false || $id === null) {
            $message = 'No valid participant ID was provided.';
            $messageType = 'danger';
        } else {
            $stmt = $conn->prepare(
                "SELECT id, firstname, surname, email
                 FROM participant
                 WHERE id = :id"
            );
            $stmt->execute([':id' => $id]);
            $participant = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$participant) {
                $message = 'No participant record matched the requested ID.';
                $messageType = 'warning';
            } else {
                $_SESSION['delete_csrf_token'] = bin2hex(random_bytes(32));
            }
        }
    }

} catch (PDOException $exception) {
    $message = 'The database is temporarily unavailable. Please try again later.';
    $messageType = 'danger';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Participant – Cit-E Cycling</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f0f4f8;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .top-nav {
            background: linear-gradient(90deg, #1a237e, #1565c0);
            padding: 14px 0;
        }

        .result-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 30px rgba(0,0,0,.12);
            max-width: 500px;
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

<nav class="top-nav">
    <div class="container d-flex justify-content-between align-items-center">
        <span class="text-white fw-bold fs-5">🚲 Cit-E Cycling</span>

        <a href="logout.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
            <i class="bi bi-box-arrow-right me-1"></i>Logout
        </a>
    </div>
</nav>

<main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
    <div class="px-3 w-100 d-flex justify-content-center">

        <?php if ($deleteSucceeded) { ?>

            <div class="card result-card">
                <div class="card-body text-center">
                    <div class="icon-big text-success mb-3">
                        <i class="bi bi-person-check-fill"></i>
                    </div>

                    <h1 class="h4 fw-bold">Participant Removed</h1>
                    <p class="text-muted">
                        The rider has been permanently deleted from the system.
                    </p>

                    <a href="view_participants_edit_delete.php" class="btn btn-primary px-4">
                        <i class="bi bi-people-fill me-1"></i>Return to List
                    </a>
                </div>
            </div>

        <?php } elseif ($participant) { ?>

            <div class="card result-card">
                <div class="card-body text-center">
                    <div class="icon-big text-danger mb-3">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>

                    <h1 class="h4 fw-bold">Confirm Participant Deletion</h1>

                    <p class="text-muted mb-1">
                        You are about to permanently delete:
                    </p>

                    <p class="fw-bold mb-1">
                        <?= h($participant['firstname'] . ' ' . $participant['surname']) ?>
                    </p>

                    <p class="small text-muted mb-4">
                        <?= h($participant['email']) ?>
                    </p>

                    <form method="post">
                        <input type="hidden" name="id" value="<?= h($participant['id']) ?>">
                        <input type="hidden" name="csrf_token" value="<?= h($_SESSION['delete_csrf_token']) ?>">

                        <div class="d-flex gap-2 justify-content-center">
                            <a href="view_participants_edit_delete.php" class="btn btn-outline-secondary px-4">
                                Cancel
                            </a>

                            <button type="submit" class="btn btn-danger px-4">
                                <i class="bi bi-trash-fill me-1"></i>Delete Permanently
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        <?php } else { ?>

            <div class="card result-card">
                <div class="card-body text-center">
                    <div class="icon-big text-<?= h($messageType ?: 'danger') ?> mb-3">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>

                    <h1 class="h4 fw-bold">Unable to Delete Participant</h1>
                    <p class="text-muted"><?= h($message) ?></p>

                    <a href="view_participants_edit_delete.php" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-arrow-left me-1"></i>Return to List
                    </a>
                </div>
            </div>

        <?php } ?>

    </div>
</main>

</body>
</html>