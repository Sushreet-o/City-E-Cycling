<?php
include 'dbconnect.php';

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$individuals = [];
$clubs = [];
$error = '';

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$database",
        $username,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $individualQuery = "
        SELECT
            participant.id,
            participant.firstname,
            participant.surname,
            participant.distance,
            participant.power_output,
            club.name AS club_name
        FROM participant
        LEFT JOIN club ON participant.club_id = club.id
        ORDER BY participant.distance DESC,
                 participant.power_output DESC,
                 participant.surname ASC
        LIMIT 10
    ";

    $individuals = $conn->query($individualQuery)->fetchAll(PDO::FETCH_ASSOC);

    $clubQuery = "
        SELECT
            club.id,
            club.name,
            club.location,
            COUNT(participant.id) AS member_count,
            SUM(participant.distance) AS total_distance,
            AVG(participant.power_output) AS average_power
        FROM club
        INNER JOIN participant ON participant.club_id = club.id
        GROUP BY club.id, club.name, club.location
        ORDER BY total_distance DESC, average_power DESC
        LIMIT 10
    ";

    $clubs = $conn->query($clubQuery)->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = 'The leaderboard is temporarily unavailable. Please try again later.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard – Cit-E Cycling</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', sans-serif; }
        .top-nav { background: linear-gradient(90deg, #1a237e, #1565c0); padding: 14px 0; }
        .page-header { background: #1565c0; color: white; padding: 40px 0 32px; }
        .leaderboard-card {
            border: none;
            border-radius: 18px;
            box-shadow: 0 4px 20px rgba(0,0,0,.09);
            overflow: hidden;
        }
        .rank {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            background: #e8f0fe;
            color: #1565c0;
        }
        .rank-1 { background: #fff3cd; color: #9a6700; }
        .rank-2 { background: #e9ecef; color: #495057; }
        .rank-3 { background: #fce5cd; color: #8a4b08; }
        .table th {
            background: #e8f0fe;
            font-size: .82rem;
            letter-spacing: .5px;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .table td { vertical-align: middle; }
    </style>
</head>
<body>

<nav class="top-nav">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="index.html" class="text-white text-decoration-none fw-bold fs-5">
            🚲 Cit-E Cycling
        </a>
        <a href="admin_menu.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
            <i class="bi bi-grid me-1"></i>Admin Dashboard
        </a>
    </div>
</nav>

<header class="page-header">
    <div class="container">
        <p class="mb-1 opacity-75 small">
            <i class="bi bi-trophy me-1"></i>Competition Performance
        </p>
        <h1 class="h3 fw-bold mb-0">Cit-E Cycling Leaderboard</h1>
    </div>
</header>

<main class="container py-5">

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php else: ?>

        <div class="row g-4">

            <div class="col-12 col-lg-7">
                <section class="card leaderboard-card h-100">
                    <div class="card-header bg-primary text-white p-3 border-0">
                        <h2 class="h5 mb-0">
                            <i class="bi bi-person-fill me-2"></i>Top Individual Cyclists
                        </h2>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Cyclist</th>
                                    <th>Club</th>
                                    <th>Distance</th>
                                    <th>Power</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($individuals as $index => $cyclist): ?>
                                    <?php $rank = $index + 1; ?>
                                    <tr>
                                        <td>
                                            <span class="rank <?= $rank <= 3 ? 'rank-' . $rank : '' ?>">
                                                <?= $rank ?>
                                            </span>
                                        </td>
                                        <td class="fw-semibold">
                                            <?= e($cyclist['firstname'] . ' ' . $cyclist['surname']) ?>
                                        </td>
                                        <td><?= e($cyclist['club_name'] ?? 'Individual entry') ?></td>
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                <?= e($cyclist['distance']) ?> KM
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark">
                                                <?= e($cyclist['power_output']) ?> W
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <div class="col-12 col-lg-5">
                <section class="card leaderboard-card h-100">
                    <div class="card-header bg-success text-white p-3 border-0">
                        <h2 class="h5 mb-0">
                            <i class="bi bi-people-fill me-2"></i>Top Cycling Clubs
                        </h2>
                    </div>

                    <div class="list-group list-group-flush">
                        <?php foreach ($clubs as $index => $club): ?>
                            <?php $rank = $index + 1; ?>
                            <div class="list-group-item p-3">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="rank <?= $rank <= 3 ? 'rank-' . $rank : '' ?>">
                                        <?= $rank ?>
                                    </span>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold"><?= e($club['name']) ?></div>
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt me-1"></i><?= e($club['location']) ?>
                                            · <?= e($club['member_count']) ?> cyclists
                                        </small>
                                    </div>
                                </div>

                                    <div class="mt-3 d-flex gap-2">
                                        <span class="badge bg-info text-dark">
                                            <?= number_format((float)$club['total_distance'], 2) ?> KM total
                                        </span>

                                        <span class="badge bg-warning text-dark">
                                            <?= number_format((float)$club['average_power'], 2) ?> W average
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                </section>
            </div>
        </div>

    <?php endif; ?>

</main>

</body>
</html>