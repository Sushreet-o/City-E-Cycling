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
    <title>Search – Cit-E Cycling</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', sans-serif; }
        .top-nav { background: linear-gradient(90deg, #1a237e, #1565c0); padding: 14px 0; }
        .page-header { background: #1565c0; color: white; padding: 35px 0 25px; }
        .search-card { border: none; border-radius: 18px; box-shadow: 0 4px 20px rgba(0,0,0,.09); }
        .search-card .card-body { padding: 2rem; }
        .search-card .card-header {
            border-radius: 18px 18px 0 0 !important;
            border: none; padding: 1.1rem 2rem;
            font-weight: 600; font-size: 1rem;
        }
        .form-control {
            border-radius: 10px; padding: .65rem 1rem;
            border: 1.5px solid #dee2e6;
        }
        .form-control:focus { border-color: #1565c0; box-shadow: 0 0 0 3px rgba(21,101,192,.15); }
        .btn-search { border-radius: 10px; font-weight: 600; padding: .6rem 1.5rem; }
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
    <div class="container">
        <h1 class="h4 fw-bold text-white mb-0"><i class="bi bi-search me-2"></i>Search Participants &amp; Clubs</h1>
    </div>
</div>

<main class="container py-5">
    <div class="row g-4">

        <div class="col-12 col-md-6">
            <div class="card search-card h-100">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-person-fill me-2"></i>Search by Participant
                </div>
                <div class="card-body">
                    <p class="text-muted small">Find a cyclist by their first name or surname.</p>
                    <form action="search_result.php" method="POST">
                        <div class="mb-3">
                            <label for="firstname" class="form-label fw-semibold small">Firstname or Surname</label>
                            <input type="text" class="form-control" id="firstname"
                                   name="firstname" placeholder="e.g. Jane or Smith" required>
                        </div>
                        <input type="hidden" name="participant" value="1">
                        <button type="submit" class="btn btn-primary btn-search w-100">
                            <i class="bi bi-search me-1"></i>Search Participants
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card search-card h-100">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-people-fill me-2"></i>Search by Club
                </div>
                <div class="card-body">
                    <p class="text-muted small">Find a club and view all its members and stats.</p>
                    <form action="search_result.php" method="POST">
                        <div class="mb-3">
                            <label for="club" class="form-label fw-semibold small">Club Name</label>
                            <input type="text" class="form-control" id="club"
                                   name="club" placeholder="e.g. Roker Rollers" required>
                        </div>
                        <button type="submit" class="btn btn-success btn-search w-100">
                            <i class="bi bi-search me-1"></i>Search Clubs
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
