<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Update Participant Scores – Cit-E Cycling</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f0f4f8; font-family: 'Segoe UI', sans-serif; }
        .top-nav { background: linear-gradient(90deg, #1a237e, #1565c0); padding: 14px 0; }
        .page-header { background: #1565c0; color: white; padding: 35px 0 25px; }
        .form-card { border: none; border-radius: 20px; box-shadow: 0 4px 30px rgba(0,0,0,.10); }
        .form-card .card-body { padding: 2.2rem; }
        .form-control {
            border-radius: 10px; padding: .65rem 1rem;
            border: 1.5px solid #dee2e6;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus { border-color: #1565c0; box-shadow: 0 0 0 3px rgba(21,101,192,.15); }
        .form-control:disabled { background: #f8f9fa; color: #6c757d; }
        .disabled-label { color: #6c757d; font-size: .8rem; }
        .btn-save { border-radius: 10px; font-weight: 600; padding: .7rem; background: linear-gradient(90deg,#1565c0,#0288d1); border: none; transition: opacity .2s; }
        .btn-save:hover { opacity: .9; }
        .rider-avatar {
            width: 64px; height: 64px; border-radius: 50%;
            background: linear-gradient(135deg,#1565c0,#0288d1);
            color: white; font-size: 1.6rem;
            display: flex; align-items: center; justify-content: center;
        }
    </style>
</head>
<body>

<nav class="top-nav">
    <div class="container d-flex justify-content-between align-items-center">
        <span class="text-white fw-bold fs-5">&#128690; Cit-E Cycling</span>
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

<div class="page-header">
    <div class="container">
        <h1 class="h4 fw-bold text-white mb-0"><i class="bi bi-pencil-fill me-2"></i>Edit Participant Scores</h1>
    </div>
</div>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card form-card">
                <div class="card-body">

                    <!-- Rider identity display -->
                    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                        <div class="rider-avatar">&#128690;</div>
                        <div>
                            <h5 class="mb-0 fw-bold"><?php echo htmlspecialchars(($participant['firstname'] ?? '') . ' ' . ($participant['surname'] ?? '')); ?></h5>
                            <span class="text-muted small"><?php echo htmlspecialchars($participant['email'] ?? ''); ?></span>
                        </div>
                    </div>

                    <form action="edit_participant.php" method="POST" id="editForm" novalidate>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Participant Firstname <span class="disabled-label">(read-only)</span></label>
                            <input type="text" class="form-control" name="firstname" disabled
                                   value="<?php echo htmlspecialchars($participant['firstname'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Participant Surname <span class="disabled-label">(read-only)</span></label>
                            <input type="text" class="form-control" name="surname" disabled
                                   value="<?php echo htmlspecialchars($participant['surname'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Power Output <span class="text-muted">(watts)</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="border-radius:10px 0 0 10px;"><i class="bi bi-lightning-charge-fill text-warning"></i></span>
                                <input type="text" class="form-control" name="power_output"
                                       value="<?php echo htmlspecialchars($participant['power_output'] ?? ''); ?>"
                                       placeholder="e.g. 250" required style="border-radius:0 10px 10px 0!important;">
                            </div>
                            <div class="invalid-feedback">Please enter a valid numeric value.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Distance Travelled <span class="text-muted">(KM)</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="border-radius:10px 0 0 10px;"><i class="bi bi-geo-fill text-info"></i></span>
                                <input type="text" class="form-control" name="distance_travelled"
                                       value="<?php echo htmlspecialchars($participant['distance'] ?? ''); ?>"
                                       placeholder="e.g. 42.5" required style="border-radius:0 10px 10px 0!important;">
                            </div>
                            <div class="invalid-feedback">Please enter a valid numeric value.</div>
                        </div>

                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($participant['id'] ?? ''); ?>">

                        <button type="submit" class="btn btn-save btn-primary w-100 text-white">
                            <i class="bi bi-save-fill me-1"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('editForm').addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        e.preventDefault(); e.stopPropagation();
    }
    this.classList.add('was-validated');
});
</script>
</body>
</html>
