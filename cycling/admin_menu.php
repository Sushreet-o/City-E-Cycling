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
    <title>Admin Menu – Cit-E Cycling</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', sans-serif; }
        .top-nav {
            background: linear-gradient(90deg, #1a237e, #1565c0);
            padding: 14px 0;
        }
        .dashboard-header { background: #1565c0; color: white; padding: 40px 0 30px; }
        .menu-card {
            border: none; border-radius: 18px;
            box-shadow: 0 4px 20px rgba(0,0,0,.09);
            transition: transform .2s, box-shadow .2s;
            text-decoration: none; color: inherit;
            display: block;
        }
        .menu-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,.15); color: inherit; }
        .menu-card .card-body { padding: 2rem; }
        .menu-icon {
            width: 56px; height: 56px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; margin-bottom: 1rem;
        }
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

<div class="dashboard-header">
    <div class="container">
        <p class="mb-1 opacity-75 small text-white"><i class="bi bi-speedometer2 me-1"></i>Admin Dashboard</p>
        <h1 class="h3 fw-bold text-white mb-0">Cit-E Cycling Portal</h1>
    </div>
</div>

<main class="container py-5">
    <div class="row g-4">

        <div class="col-12 col-sm-6">
            <a href="search_form.php" class="card menu-card">
                <div class="card-body">
                    <div class="menu-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-search"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Search</h5>
                    <p class="text-muted small mb-0">Find participants or clubs by name</p>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6">
            <a href="view_participants_edit_delete.php" class="card menu-card">
                <div class="card-body">
                    <div class="menu-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Participants</h5>
                    <p class="text-muted small mb-0">View, edit or delete participant records</p>
                </div>
            </a>
        </div>

    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
