<?php
session_start();
if (!isset($_SESSION["admin_logged_in"])) { header("Location: admin_login.html"); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Search Results – Cit-E Cycling</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', sans-serif; }
        .top-nav { background: linear-gradient(90deg, #1a237e, #1565c0); padding: 14px 0; }
        .page-header { background: #1565c0; color: white; padding: 35px 0 25px; }
        .result-card { border: none; border-radius: 18px; box-shadow: 0 4px 20px rgba(0,0,0,.09); }
        .result-card .card-body { padding: 1.8rem; }
        .table th { background: #e8f0fe; white-space: nowrap; font-size: .85rem; text-transform: uppercase; letter-spacing: .5px; }
        .table td { font-size: .92rem; vertical-align: middle; }
        .stat-badge {
            border-radius: 12px; padding: .55rem 1rem;
            font-size: .85rem; font-weight: 600;
        }
    </style>
</head>
<body>

<nav class="top-nav">
    <div class="container d-flex justify-content-between align-items-center">
        <span class="text-white fw-bold fs-5">&#128690; Cit-E Cycling</span>
        <div class="d-flex gap-2">
            <a href="search_form.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i>Search
            </a>
            <a href="logout.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="page-header">
    <div class="container">
        <h1 class="h4 fw-bold text-white mb-0"><i class="bi bi-search me-2"></i>Search Results</h1>
    </div>
</div>

<main class="container py-5">
    <?php
        include 'dbconnect.php';
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (isset($_POST['participant']) && $_POST['participant'] == "1") {
                // Participant Search Logic
                $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';

                if (empty($firstname)) {
                    echo "<div class='alert alert-warning d-flex align-items-center gap-2'>
                            <i class='bi bi-exclamation-triangle-fill fs-5'></i>
                            <div><strong>No keyword entered.</strong> Please enter a name to search.</div>
                          </div>";
                } else {
                    $wildcard = "%" . $firstname . "%";
                    $stmt = $conn->prepare("SELECT * FROM participant WHERE firstname LIKE :q OR surname LIKE :q");
                    $stmt->bindParam(':q', $wildcard);
                    $stmt->execute();
                    $riders = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    echo "<h5 class='fw-bold mb-3'><i class='bi bi-person-fill text-primary me-2'></i>Results for: <span class='text-primary'>\"" . htmlspecialchars($firstname) . "\"</span> &mdash; <span class='text-muted fw-normal'>" . count($riders) . " found</span></h5>";

                    if ($riders) {
                        echo "<div class='card result-card'><div class='card-body p-0'>";
                        echo "<div class='table-responsive'><table class='table table-hover mb-0'>";
                        echo "<thead><tr><th>ID</th><th>First Name</th><th>Surname</th><th>Email</th><th>Power (W)</th><th>Distance (KM)</th></tr></thead><tbody>";
                        foreach ($riders as $rider) {
                            echo "<tr>";
                            echo "<td class='text-muted small'>" . htmlspecialchars($rider['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($rider['firstname']) . "</td>";
                            echo "<td><strong>" . htmlspecialchars($rider['surname']) . "</strong></td>";
                            echo "<td class='small text-muted'>" . htmlspecialchars($rider['email']) . "</td>";
                            echo "<td><span class='badge bg-warning text-dark'>" . htmlspecialchars($rider['power_output']) . " W</span></td>";
                            echo "<td><span class='badge bg-info text-dark'>" . htmlspecialchars($rider['distance']) . " KM</span></td>";
                            echo "</tr>";
                        }
                        echo "</tbody></table></div></div></div>";
                    } else {
                        echo "<div class='alert alert-info d-flex align-items-center gap-2'>
                                <i class='bi bi-info-circle-fill fs-5'></i>
                                <div>No participants matched your search.</div>
                              </div>";
                    }
                }
            }
            else {
                // Club Search Logic
                $club_name = isset($_POST['club']) ? trim($_POST['club']) : '';

                if (empty($club_name)) {
                    echo "<div class='alert alert-warning d-flex align-items-center gap-2'>
                            <i class='bi bi-exclamation-triangle-fill fs-5'></i>
                            <div><strong>No club name entered.</strong> Please enter a keyword to search.</div>
                          </div>";
                } else {
                    $wildcard = "%" . $club_name . "%";
                    $club_stmt = $conn->prepare("SELECT * FROM club WHERE name LIKE :q");
                    $club_stmt->bindParam(':q', $wildcard);
                    $club_stmt->execute();
                    $clubs = $club_stmt->fetchAll(PDO::FETCH_ASSOC);

                    echo "<h5 class='fw-bold mb-3'><i class='bi bi-people-fill text-success me-2'></i>Club Results for: <span class='text-success'>\"" . htmlspecialchars($club_name) . "\"</span></h5>";

                    if ($clubs) {
                        foreach ($clubs as $club) {
                            echo "<div class='card result-card mb-4'>";
                            echo "<div class='card-header bg-success text-white rounded-top' style='border-radius:18px 18px 0 0;border:none;padding:1rem 1.5rem;'>";
                            echo "<i class='bi bi-geo-alt-fill me-2'></i><strong>" . htmlspecialchars($club['name']) . "</strong> &mdash; " . htmlspecialchars($club['location']);
                            echo "</div><div class='card-body'>";

                            $member_stmt = $conn->prepare("SELECT * FROM participant WHERE club_id = :cid");
                            $member_stmt->bindParam(':cid', $club['id'], PDO::PARAM_INT);
                            $member_stmt->execute();
                            $members = $member_stmt->fetchAll(PDO::FETCH_ASSOC);

                            if ($members) {
                                echo "<div class='table-responsive mb-3'><table class='table table-hover mb-0'>";
                                echo "<thead><tr><th>Rider Name</th><th>Email</th><th>Power Output</th><th>Distance</th></tr></thead><tbody>";
                                foreach ($members as $m) {
                                    echo "<tr>";
                                    echo "<td><strong>" . htmlspecialchars($m['firstname'] . " " . $m['surname']) . "</strong></td>";
                                    echo "<td class='small text-muted'>" . htmlspecialchars($m['email']) . "</td>";
                                    echo "<td><span class='badge bg-warning text-dark'>" . htmlspecialchars($m['power_output']) . " W</span></td>";
                                    echo "<td><span class='badge bg-info text-dark'>" . htmlspecialchars($m['distance']) . " KM</span></td>";
                                    echo "</tr>";
                                }
                                echo "</tbody></table></div>";

                                // Metrics Aggregation using standard SQL operations
                                $agg_stmt = $conn->prepare("
                                    SELECT SUM(distance) as total_dist, SUM(power_output) as total_power,
                                           AVG(distance) as avg_dist, AVG(power_output) as avg_power
                                    FROM participant WHERE club_id = :cid
                                ");
                                $agg_stmt->bindParam(':cid', $club['id'], PDO::PARAM_INT);
                                $agg_stmt->execute();
                                $metrics = $agg_stmt->fetch(PDO::FETCH_ASSOC);

                                echo "<h6 class='fw-bold mb-2'><i class='bi bi-bar-chart-fill me-1 text-success'></i>Performance Summary</h6>";
                                echo "<div class='row g-2'>";
                                echo "<div class='col-6 col-md-3'><div class='stat-badge bg-primary bg-opacity-10 text-primary text-center w-100'><div class='small text-muted'>Total Distance</div>" . round($metrics['total_dist'], 2) . " KM</div></div>";
                                echo "<div class='col-6 col-md-3'><div class='stat-badge bg-warning bg-opacity-10 text-warning-emphasis text-center w-100'><div class='small text-muted'>Total Power</div>" . round($metrics['total_power'], 2) . " W</div></div>";
                                echo "<div class='col-6 col-md-3'><div class='stat-badge bg-info bg-opacity-10 text-info-emphasis text-center w-100'><div class='small text-muted'>Avg Distance</div>" . round($metrics['avg_dist'], 2) . " KM</div></div>";
                                echo "<div class='col-6 col-md-3'><div class='stat-badge bg-success bg-opacity-10 text-success text-center w-100'><div class='small text-muted'>Avg Power</div>" . round($metrics['avg_power'], 2) . " W</div></div>";
                                echo "</div>";
                            } else {
                                echo "<p class='text-muted mb-0'>No participants found for this club.</p>";
                            }
                            echo "</div></div>";
                        }
                    } else {
                        echo "<div class='alert alert-info d-flex align-items-center gap-2'>
                                <i class='bi bi-info-circle-fill fs-5'></i>
                                <div>No clubs matched your search.</div>
                              </div>";
                    }
                }
            }
        }
        catch(PDOException $e) {
            echo "<div class='alert alert-danger d-flex align-items-center gap-2'>
                    <i class='bi bi-database-x fs-5'></i>
                    <div><strong>Database Error:</strong> " . $e->getMessage() . "</div>
                  </div>";
        }
    ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
