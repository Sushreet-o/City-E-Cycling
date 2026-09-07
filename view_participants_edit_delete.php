<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Participants – Cit-E Cycling</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', sans-serif; }
        .top-nav { background: linear-gradient(90deg, #1a237e, #1565c0); padding: 14px 0; }
        .page-header { background: #1565c0; color: white; padding: 35px 0 25px; }
        .table-card { border: none; border-radius: 18px; box-shadow: 0 4px 20px rgba(0,0,0,.09); overflow: hidden; }
        .table thead th {
            background: #1a237e; color: white;
            font-size: .78rem; text-transform: uppercase;
            letter-spacing: .6px; white-space: nowrap;
            border: none; padding: .85rem 1rem;
        }
        .table tbody td { font-size: .9rem; vertical-align: middle; padding: .75rem 1rem; border-color: #f0f4f8; }
        .table tbody tr:hover { background: #e8f0fe; }
        .btn-edit  { border-radius: 8px; font-size: .78rem; padding: .3rem .75rem; font-weight: 600; }
        .btn-del   { border-radius: 8px; font-size: .78rem; padding: .3rem .75rem; font-weight: 600; }
        .filter-box { border-radius: 10px; border: 1.5px solid #dee2e6; padding: .55rem 1rem; font-size: .9rem; }
        .filter-box:focus { border-color: #1565c0; box-shadow: 0 0 0 3px rgba(21,101,192,.15); outline: none; }
        .count-pill { background: rgba(255,255,255,.2); color: white; border-radius: 20px; padding: 3px 12px; font-size: .82rem; }
    </style>
</head>
<body>

<nav class="top-nav">
    <div class="container d-flex justify-content-between align-items-center">
        <span class="text-white fw-bold fs-5">&#128690; Cit-E Cycling</span>
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

<div class="page-header">
    <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h1 class="h4 fw-bold text-white mb-0"><i class="bi bi-people-fill me-2"></i>All Participants</h1>
        </div>
        <span class="count-pill" id="countPill">Loading...</span>
    </div>
</div>

<main class="container py-4 pb-5">

    <div class="mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-funnel text-muted"></i>
        <input type="text" id="tableSearch" class="filter-box"
               placeholder="Filter by name, club or email..." oninput="filterTable()" style="width:280px;">
    </div>

    <?php
    include 'dbconnect.php';
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("
            SELECT p.id, p.firstname, p.surname, p.email,
                   p.power_output, p.distance, c.name AS club_name
            FROM participant p
            LEFT JOIN club c ON p.club_id = c.id
            ORDER BY p.firstname ASC, p.surname ASC
        ");
        $stmt->execute();
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($participants) {
            echo "<div class='card table-card'>";
            echo "<div class='table-responsive'>";
            echo "<table class='table mb-0' id='participantTable'>";
            echo "<thead><tr>
                    <th>#</th>
                    <th>First Name</th>
                    <th>Surname</th>
                    <th>Email</th>
                    <th>Club</th>
                    <th class='text-center'>Power (W)</th>
                    <th class='text-center'>Distance (KM)</th>
                    <th class='text-center'>Actions</th>
                  </tr></thead><tbody>";

            foreach ($participants as $p) {
                echo "<tr>";
                echo "<td class='text-muted small'>" . htmlspecialchars($p['id']) . "</td>";
                echo "<td>" . htmlspecialchars($p['firstname']) . "</td>";
                echo "<td><strong>" . htmlspecialchars($p['surname']) . "</strong></td>";
                echo "<td class='small text-muted'>" . htmlspecialchars($p['email']) . "</td>";
                echo "<td><span class='badge rounded-pill bg-primary bg-opacity-10 text-primary fw-semibold'>" . htmlspecialchars($p['club_name'] ?? 'N/A') . "</span></td>";
                echo "<td class='text-center'><span class='badge bg-warning text-dark'>" . htmlspecialchars($p['power_output']) . "</span></td>";
                echo "<td class='text-center'><span class='badge bg-info text-dark'>" . htmlspecialchars($p['distance']) . "</span></td>";
                echo "<td class='text-center'>";
                echo "  <div class='d-flex gap-1 justify-content-center'>";
                echo "    <a href='edit_participant.php?id=" . htmlspecialchars($p['id']) . "' class='btn btn-warning btn-edit' title='Edit'>
                            <i class='bi bi-pencil-fill'></i> Edit
                          </a>";
                echo "    <a href='delete.php?id=" . htmlspecialchars($p['id']) . "'
                             class='btn btn-danger btn-del' title='Delete'
                             onclick=\"return confirm('Delete " . htmlspecialchars($p['firstname']) . " " . htmlspecialchars($p['surname']) . "?\\nThis action cannot be undone.')\">
                            <i class='bi bi-trash-fill'></i> Delete
                          </a>";
                echo "  </div>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div></div>";
            echo "<script>document.getElementById('countPill').textContent = '" . count($participants) . " riders';</script>";
        } else {
            echo "<div class='alert alert-info d-flex gap-2 align-items-center'>
                    <i class='bi bi-info-circle-fill fs-5'></i>
                    <div>No participants found in the database.</div>
                  </div>";
        }
    }
    catch(PDOException $e) {
        echo "<div class='alert alert-danger d-flex gap-2 align-items-center'>
                <i class='bi bi-database-x fs-5'></i>
                <div><strong>Database Error:</strong> " . $e->getMessage() . "</div>
              </div>";
    }
    ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function filterTable() {
    const q = document.getElementById('tableSearch').value.toLowerCase();
    const rows = document.querySelectorAll('#participantTable tbody tr');
    let visible = 0;
    rows.forEach(row => {
        const match = row.textContent.toLowerCase().includes(q);
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('countPill').textContent = visible + ' riders';
}
</script>
</body>
</html>
