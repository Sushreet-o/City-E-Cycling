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

$participants = [];
$error = '';

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$database;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("
        SELECT
            p.id,
            p.firstname,
            p.surname,
            p.email,
            p.power_output,
            p.distance,
            c.name AS club_name
        FROM participant p
        LEFT JOIN club c ON p.club_id = c.id
        ORDER BY p.firstname ASC, p.surname ASC
    ");

    $stmt->execute();
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $exception) {
    $error = 'The participant list is temporarily unavailable. Please try again later.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participants – Cit-E Cycling</title>

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

        .page-header {
            background: #1565c0;
            color: white;
            padding: 35px 0 25px;
        }

        .table-card {
            border: none;
            border-radius: 18px;
            box-shadow: 0 4px 20px rgba(0,0,0,.09);
            overflow: hidden;
        }

        .table thead th {
            background: #1a237e;
            color: white;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .6px;
            white-space: nowrap;
            border: none;
            padding: .85rem 1rem;
        }

        .table tbody td {
            font-size: .9rem;
            vertical-align: middle;
            padding: .75rem 1rem;
            border-color: #f0f4f8;
        }

        .table tbody tr:hover {
            background: #e8f0fe;
        }

        .btn-edit,
        .btn-del {
            border-radius: 8px;
            font-size: .78rem;
            padding: .3rem .75rem;
            font-weight: 600;
        }

        .filter-box {
            border-radius: 10px;
            border: 1.5px solid #dee2e6;
            padding: .55rem 1rem;
            font-size: .9rem;
        }

        .filter-box:focus {
            border-color: #1565c0;
            box-shadow: 0 0 0 3px rgba(21,101,192,.15);
            outline: none;
        }

        .count-pill {
            background: rgba(255,255,255,.2);
            color: white;
            border-radius: 20px;
            padding: 3px 12px;
            font-size: .82rem;
        }
    </style>
</head>
<body>

<nav class="top-nav">
    <div class="container d-flex justify-content-between align-items-center">
        <span class="text-white fw-bold fs-5">🚲 Cit-E Cycling</span>

        <div class="d-flex gap-2">
            <a href="admin_menu.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="bi bi-grid me-1"></i>Menu
            </a>

            <a href="logout.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>

<header class="page-header">
    <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h1 class="h4 fw-bold text-white mb-0">
            <i class="bi bi-people-fill me-2"></i>All Participants
        </h1>

        <span class="count-pill" id="countPill">
            <?= count($participants) ?> riders
        </span>
    </div>
</header>

<main class="container py-4 pb-5">

    <?php if ($error) { ?>

        <div class="alert alert-danger d-flex gap-2 align-items-center">
            <i class="bi bi-database-x fs-5"></i>
            <div><?= h($error) ?></div>
        </div>

    <?php } elseif (empty($participants)) { ?>

        <div class="alert alert-info d-flex gap-2 align-items-center">
            <i class="bi bi-info-circle-fill fs-5"></i>
            <div>No participants found in the database.</div>
        </div>

    <?php } else { ?>

        <div class="mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-funnel text-muted"></i>

            <input
                type="text"
                id="tableSearch"
                class="filter-box"
                placeholder="Filter by name, club or email..."
                oninput="filterTable()"
                style="width:280px;"
            >
        </div>

        <div class="card table-card">
            <div class="table-responsive">
                <table class="table mb-0" id="participantTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Surname</th>
                            <th>Email</th>
                            <th>Club</th>
                            <th class="text-center">Power (W)</th>
                            <th class="text-center">Distance (KM)</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($participants as $participant) { ?>
                            <tr>
                                <td class="text-muted small">
                                    <?= h($participant['id']) ?>
                                </td>

                                <td><?= h($participant['firstname']) ?></td>

                                <td>
                                    <strong><?= h($participant['surname']) ?></strong>
                                </td>

                                <td class="small text-muted">
                                    <?= h($participant['email']) ?>
                                </td>

                                <td>
                                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary fw-semibold">
                                        <?= h($participant['club_name'] ?? 'N/A') ?>
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-warning text-dark">
                                        <?= h($participant['power_output']) ?>
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-info text-dark">
                                        <?= h($participant['distance']) ?>
                                    </span>
                                </td>

                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a
                                            href="edit_participant.php?id=<?= urlencode((string)$participant['id']) ?>"
                                            class="btn btn-warning btn-edit"
                                            title="Edit participant"
                                        >
                                            <i class="bi bi-pencil-fill"></i> Edit
                                        </a>

                                        <a
                                            href="delete.php?id=<?= urlencode((string)$participant['id']) ?>"
                                            class="btn btn-danger btn-del"
                                            title="Open delete confirmation"
                                        >
                                            <i class="bi bi-trash-fill"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php } ?>

</main>

<script>
function filterTable() {
    const searchBox = document.getElementById('tableSearch');
    const table = document.getElementById('participantTable');

    if (!searchBox || !table) {
        return;
    }

    const query = searchBox.value.toLowerCase();
    const rows = table.querySelectorAll('tbody tr');
    let visibleCount = 0;

    rows.forEach(row => {
        const matches = row.textContent.toLowerCase().includes(query);

        row.style.display = matches ? '' : 'none';

        if (matches) {
            visibleCount++;
        }
    });

    document.getElementById('countPill').textContent = visibleCount + ' riders';
}
</script>

</body>
</html>