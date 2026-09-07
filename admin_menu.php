<?php
session_start();

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: admin_login.html");
    exit;
}

include 'dbconnect.php';

$stats = [
    'participants' => 0,
    'clubs' => 0,
    'total_distance' => 0,
    'average_power' => 0
];

$statsAvailable = true;

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$database;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $participantStats = $conn->query("
        SELECT
            COUNT(*) AS participant_count,
            COALESCE(SUM(distance), 0) AS total_distance,
            COALESCE(AVG(power_output), 0) AS average_power
        FROM participant
    ")->fetch(PDO::FETCH_ASSOC);

    $clubStats = $conn->query("
        SELECT COUNT(*) AS club_count
        FROM club
    ")->fetch(PDO::FETCH_ASSOC);

    $stats['participants'] = (int)$participantStats['participant_count'];
    $stats['clubs'] = (int)$clubStats['club_count'];
    $stats['total_distance'] = (float)$participantStats['total_distance'];
    $stats['average_power'] = (float)$participantStats['average_power'];

} catch (PDOException $exception) {
    $statsAvailable = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard – Cit-E Cycling</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f0f4f8;
            font-family: 'Segoe UI', sans-serif;
        }

        .top-nav {
            background: linear-gradient(90deg, #1a237e, #1565c0);
            padding: 14px 0;
        }

        .dashboard-header {
            background: #1565c0;
            color: white;
            padding: 40px 0 30px;
        }

        .stat-card {
            border: none;
            border-radius: 18px;
            box-shadow: 0 4px 20px rgba(0,0,0,.09);
            height: 100%;
        }

        .stat-card .card-body {
            padding: 1.35rem;
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .stat-number {
            font-size: 1.6rem;
            font-weight: 700;
            line-height: 1.1;
        }

        .menu-card {
            border: none;
            border-radius: 18px;
            box-shadow: 0 4px 20px rgba(0,0,0,.09);
            transition: transform .2s, box-shadow .2s;
            text-decoration: none;
            color: inherit;
            display: block;
            height: 100%;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,.15);
            color: inherit;
        }

        .menu-card .card-body {
            padding: 2rem;
        }

        .menu-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
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

<header class="dashboard-header">
    <div class="container">
        <p class="mb-1 opacity-75 small text-white">
            <i class="bi bi-speedometer2 me-1"></i>Admin Dashboard
        </p>

        <h1 class="h3 fw-bold text-white mb-0">Cit-E Cycling Portal</h1>
    </div>
</header>

<main class="container py-5">

    <?php if (!$statsAvailable) { ?>
        <div class="alert alert-warning">
            Dashboard statistics are temporarily unavailable.
        </div>
    <?php } ?>

    <section aria-label="Competition statistics">
        <div class="row g-3 mb-5">

            <div class="col-6 col-lg-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary mb-3">
                            <i class="bi bi-people-fill"></i>
                        </div>

                        <div class="stat-number"><?= $stats['participants'] ?></div>
                        <div class="small text-muted">Participants</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="stat-icon bg-success bg-opacity-10 text-success mb-3">
                            <i class="bi bi-shield-fill"></i>
                        </div>

                        <div class="stat-number"><?= $stats['clubs'] ?></div>
                        <div class="small text-muted">Cycling Clubs</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="stat-icon bg-info bg-opacity-10 text-info mb-3">
                            <i class="bi bi-signpost-split-fill"></i>
                        </div>

                        <div class="stat-number">
                            <?= number_format($stats['total_distance'], 1) ?>
                        </div>

                        <div class="small text-muted">Total KM Travelled</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning mb-3">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>

                        <div class="stat-number">
                            <?= number_format($stats['average_power'], 1) ?>
                        </div>

                        <div class="small text-muted">Average Power (W)</div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section>
        <h2 class="h5 fw-bold mb-3">Management Tools</h2>

        <div class="row g-4">

            <div class="col-12 col-md-6 col-lg-4">
                <a href="search_form.php" class="card menu-card">
                    <div class="card-body">
                        <div class="menu-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-search"></i>
                        </div>

                        <h3 class="h5 fw-bold mb-1">Search</h3>
                        <p class="text-muted small mb-0">
                            Find participants or clubs by name.
                        </p>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <a href="view_participants_edit_delete.php" class="card menu-card">
                    <div class="card-body">
                        <div class="menu-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-people-fill"></i>
                        </div>

                        <h3 class="h5 fw-bold mb-1">Participants</h3>
                        <p class="text-muted small mb-0">
                            View, edit, or delete participant records.
                        </p>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <a href="leaderboard.php" class="card menu-card">
                    <div class="card-body">
                        <div class="menu-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-trophy-fill"></i>
                        </div>

                        <h3 class="h5 fw-bold mb-1">Leaderboard</h3>
                        <p class="text-muted small mb-0">
                            View the top cyclists and clubs.
                        </p>
                    </div>
                </a>
            </div>

        </div>
    </section>

</main>

</body>
</html>