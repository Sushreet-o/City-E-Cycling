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
    <title>Delete Participant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', sans-serif; min-height: 100vh; display: flex; flex-direction: column; }
        .top-nav { background: linear-gradient(90deg, #1a237e, #1565c0); padding: 14px 0; }
        .result-card { border: none; border-radius: 20px; box-shadow: 0 4px 30px rgba(0,0,0,.12); max-width: 460px; width: 100%; }
        .result-card .card-body { padding: 2.5rem; }
        .icon-big { font-size: 3.5rem; }
        .btn { border-radius: 10px; }
    </style>
</head>
<body>

<nav class="top-nav">
    <div class="container d-flex justify-content-between align-items-center">
        <span class="text-white fw-bold fs-5">&#128690; Cit-E Cycling</span>
        <a href="logout.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
            <i class="bi bi-box-arrow-right me-1"></i>Logout
        </a>
    </div>
</nav>

<div class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
    <div class="px-3 w-100 d-flex justify-content-center">
        <?php
        include 'dbconnect.php';
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $id = $_GET['id'];
                $stmt = $conn->prepare("DELETE FROM participant WHERE id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    echo "<div class='card result-card'>
                            <div class='card-body text-center'>
                              <div class='icon-big text-success mb-3'><i class='bi bi-person-check-fill'></i></div>
                              <h4 class='fw-bold'>Participant Removed</h4>
                              <p class='text-muted'>The rider has been permanently deleted from the system.</p>
                              <a href='view_participants_edit_delete.php' class='btn btn-primary px-4'>
                                <i class='bi bi-people-fill me-1'></i>Return to List
                              </a>
                            </div>
                          </div>";
                }
            } else {
                echo "<div class='card result-card'>
                        <div class='card-body text-center'>
                          <div class='icon-big text-danger mb-3'><i class='bi bi-exclamation-triangle-fill'></i></div>
                          <h4 class='fw-bold'>Invalid Request</h4>
                          <p class='text-muted'>No valid participant ID was provided.</p>
                          <a href='view_participants_edit_delete.php' class='btn btn-outline-secondary px-4'>
                            <i class='bi bi-arrow-left me-1'></i>Return to List
                          </a>
                        </div>
                      </div>";
            }
        }
        catch(PDOException $e) {
            echo "<div class='card result-card'>
                    <div class='card-body text-center'>
                      <div class='icon-big text-danger mb-3'><i class='bi bi-database-x'></i></div>
                      <h4 class='fw-bold'>Database Error</h4>
                      <p class='text-muted small'>" . $e->getMessage() . "</p>
                    </div>
                  </div>";
        }
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
