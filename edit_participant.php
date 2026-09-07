<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
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
$updateSucceeded = false;

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

        $powerInput = trim($_POST['power_output'] ?? '');
        $distanceInput = trim($_POST['distance_travelled'] ?? '');
        $submittedToken = $_POST['csrf_token'] ?? '';
        $storedToken = $_SESSION['edit_csrf_token'] ?? '';

        if (
            $id === false ||
            $id === null ||
            !hash_equals($storedToken, $submittedToken)
        ) {
            $message = 'This update request is invalid or has expired. Please try again.';
            $messageType = 'danger';

        } elseif (
            $powerInput === '' ||
            $distanceInput === '' ||
            !is_numeric($powerInput) ||
            !is_numeric($distanceInput)
        ) {
            $message = 'Power output and distance must be valid numeric values.';
            $messageType = 'warning';

        } elseif ((float)$powerInput < 0 || (float)$distanceInput < 0) {
            $message = 'Power output and distance cannot be negative.';
            $messageType = 'warning';

        } else {
            $stmt = $conn->prepare("
                UPDATE participant
                SET power_output = :power, distance = :distance
                WHERE id = :id
            ");

            $stmt->execute([
                ':power' => (float)$powerInput,
                ':distance' => (float)$distanceInput,
                ':id' => $id
            ]);

            unset($_SESSION['edit_csrf_token']);
            $updateSucceeded = true;
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
            $stmt = $conn->prepare("
                SELECT id, firstname, surname, email, power_output, distance
                FROM participant
                WHERE id = :id
            ");

            $stmt->execute([':id' => $id]);
            $participant = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$participant) {
                $message = 'No participant record matched the requested ID.';
                $messageType = 'warning';

            } else {
                $_SESSION['edit_csrf_token'] = bin2hex(random_bytes(32));
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
    <title>Edit Participant Scores – Cit-E Cycling</title>

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

        .page-header {
            background: #1565c0;
            color: white;
            padding: 35px 0 25px;
        }

        .form-card,
        .result-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 30px rgba(0,0,0,.12);
            max-width: 520px;
            width: 100%;
        }

        .form-card .card-body,
        .result-card .card-body {
            padding: 2.5rem;
        }

        .form-control {
            border-radius: 10px;
            padding: .65rem 1rem;
            border: 1.5px solid #dee2e6;
        }

        .form-control:focus {
            border-color: #1565c0;
            box-shadow: 0 0 0 3px rgba(21,101,192,.15);
        }

        .btn {
            border-radius: 10px;
        }

        .btn-save {
            border-radius: 10px;
            font-weight: 600;
            padding: .7rem;
            background: linear-gradient(90deg, #1565c0, #0288d1);
            border: none;
        }

        .rider-avatar,
        .icon-big {
            font-size: 3.5rem;
        }

        .rider-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1565c0, #0288d1);
            color: white;
            font-size: 1.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>

<nav class="top-nav">
    <div class="container d-flex justify-content-between align-items-center">
        <span class="text-white fw-bold fs-5">🚲 Cit-E Cycling</span>

        <div class="d-flex gap-2">
            <a href="view_participants_edit_delete.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>

            <a href="logout.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>

<header class="page-header">
    <div class="container">
        <h1 class="h4 fw-bold text-white mb-0">
            <i class="bi bi-pencil-fill me-2"></i>Edit Participant Scores
        </h1>
    </div>
</header>

<main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
    <div class="px-3 w-100 d-flex justify-content-center">

        <?php if ($updateSucceeded) { ?>

            <div class="card result-card">
                <div class="card-body text-center">
                    <div class="icon-big text-success mb-3">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>

                    <h2 class="h4 fw-bold">Scores Updated</h2>
                    <p class="text-muted">
                        The rider's power output and distance have been saved successfully.
                    </p>

                    <a href="view_participants_edit_delete.php" class="btn btn-primary px-4">
                        <i class="bi bi-people-fill me-1"></i>Return to List
                    </a>
                </div>
            </div>

        <?php } elseif ($participant) { ?>

            <div class="card form-card">
                <div class="card-body">

                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="rider-avatar">🚲</div>

                        <div>
                            <h2 class="h5 mb-0 fw-bold">
                                <?= h($participant['firstname'] . ' ' . $participant['surname']) ?>
                            </h2>

                            <span class="text-muted small">
                                <?= h($participant['email']) ?>
                            </span>
                        </div>
                    </div>

                    <form method="post">
                        <input type="hidden" name="id" value="<?= h($participant['id']) ?>">
                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= h($_SESSION['edit_csrf_token']) ?>"
                        >

                        <div class="mb-3">
                            <label for="power_output" class="form-label fw-semibold">
                                Power Output <span class="text-muted">(watts)</span>
                            </label>

                            <input
                                type="number"
                                id="power_output"
                                name="power_output"
                                class="form-control"
                                value="<?= h($participant['power_output']) ?>"
                                min="0"
                                step="0.01"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label for="distance_travelled" class="form-label fw-semibold">
                                Distance Travelled <span class="text-muted">(KM)</span>
                            </label>

                            <input
                                type="number"
                                id="distance_travelled"
                                name="distance_travelled"
                                class="form-control"
                                value="<?= h($participant['distance']) ?>"
                                min="0"
                                step="0.01"
                                required
                            >
                        </div>

                        <button type="submit" class="btn btn-save btn-primary w-100 text-white">
                            <i class="bi bi-save-fill me-1"></i>Save Changes
                        </button>
                    </form>
                </div>
            </div>

        <?php } else { ?>

            <div class="card result-card">
                <div class="card-body text-center">
                    <div class="icon-big text-<?= h($messageType ?: 'danger') ?> mb-3">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>

                    <h2 class="h4 fw-bold">Unable to Update Participant</h2>
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