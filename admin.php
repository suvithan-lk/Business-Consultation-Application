<?php
// admin.php - Simple admin panel to view form submissions
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'consultation_db';
$username = 'root';
$password = '';

// Simple authentication (you should implement proper authentication)
$admin_password = 'admin123'; // Change this!

if (isset($_POST['login'])) {
    if ($_POST['admin_password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $login_error = 'Invalid password';
    }
}

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Update status if requested
if (isset($_POST['update_status']) && isset($_SESSION['admin_logged_in'])) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("UPDATE consultation_applications SET status = ? WHERE id = ?");
        $stmt->execute([$_POST['new_status'], $_POST['submission_id']]);
        
        $success_message = 'Status updated successfully!';
    } catch (PDOException $e) {
        $error_message = 'Error updating status: ' . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultation Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .status-badge {
            font-size: 0.8rem;
        }
        .details-card {
            background: #f8f9fa;
            border-left: 4px solid #0d6efd;
        }
    </style>
</head>
<body class="bg-light">

<?php if (!isset($_SESSION['admin_logged_in'])): ?>
    <!-- Login Form -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><i class="bi bi-shield-lock"></i> Admin Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($login_error)): ?>
                            <div class="alert alert-danger"><?= $login_error ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="admin_password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- Admin Panel -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <span class="navbar-brand">
                <i class="bi bi-gear"></i> Consultation Admin Panel
            </span>
            <form method="POST" class="d-flex">
                <button type="submit" name="logout" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $success_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $error_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col">
                <h2><i class="bi bi-list-ul"></i> Consultation Applications</h2>
            </div>
        </div>

        <?php
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Get statistics
            $stats = $pdo->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_count,
                    SUM(CASE WHEN status = 'contacted' THEN 1 ELSE 0 END) as contacted_count,
                    SUM(CASE WHEN status = 'scheduled' THEN 1 ELSE 0 END) as scheduled_count,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count
                FROM consultation_applications
            ")->fetch(PDO::FETCH_ASSOC);
            
            ?>
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-primary"><?= $stats['total'] ?></h3>
                            <p class="card-text">Total Applications</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-warning"><?= $stats['new_count'] ?></h3>
                            <p class="card-text">New</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-info"><?= $stats['contacted_count'] ?></h3>
                            <p class="card-text">Contacted</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-success"><?= $stats['completed_count'] ?></h3>
                            <p class="card-text">Completed</p>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            // Get all applications
            $stmt = $pdo->query("
                SELECT * FROM consultation_applications 
                ORDER BY created_at DESC
            ");
            $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <!-- Applications Table -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-table"></i> All Applications</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($applications)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <p class="text-muted">No applications submitted yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Business</th>
                                        <th>Stage</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applications as $app): ?>
                                        <tr>
                                            <td><?= $app['id'] ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($app['full_name']) ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars($app['phone'] ?: 'No phone') ?></small>
                                            </td>
                                            <td><?= htmlspecialchars($app['email']) ?></td>
                                            <td><?= htmlspecialchars($app['business_name'] ?: 'N/A') ?></td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?= ucwords(str_replace('_', ' ', $app['business_stage'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $status_colors = [
                                                    'new' => 'warning',
                                                    'contacted' => 'info',
                                                    'scheduled' => 'primary',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                                $color = $status_colors[$app['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?= $color ?> status-badge">
                                                    <?= ucfirst($app['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small><?= date('M j, Y', strtotime($app['created_at'])) ?></small>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#detailModal<?= $app['id'] ?>">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Detail Modal -->
                                        <div class="modal fade" id="detailModal<?= $app['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            <i class="bi bi-person-circle"></i>
                                                            <?= htmlspecialchars($app['full_name']) ?>
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="details-card p-3 rounded mb-3">
                                                                    <h6><i class="bi bi-person"></i> Contact Information</h6>
                                                                    <p><strong>Email:</strong> <?= htmlspecialchars($app['email']) ?></p>
                                                                    <p><strong>Phone:</strong> <?= htmlspecialchars($app['phone'] ?: 'Not provided') ?></p>
                                                                    <p><strong>Location:</strong> <?= htmlspecialchars($app['location'] ?: 'Not provided') ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="details-card p-3 rounded mb-3">
                                                                    <h6><i class="bi bi-building"></i> Business Details</h6>
                                                                    <p><strong>Business:</strong> <?= htmlspecialchars($app['business_name'] ?: 'Not provided') ?></p>
                                                                    <p><strong>Website:</strong> 
                                                                        <?php if ($app['website']): ?>
                                                                            <a href="<?= htmlspecialchars($app['website']) ?>" target="_blank">
                                                                                <?= htmlspecialchars($app['website']) ?>
                                                                            </a>
                                                                        <?php else: ?>
                                                                            Not provided
                                                                        <?php endif; ?>
                                                                    </p>
                                                                    <p><strong>Stage:</strong> <?= ucwords(str_replace('_', ' ', $app['business_stage'])) ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="details-card p-3 rounded mb-3">
                                                            <h6><i class="bi bi-card-text"></i> Business Type</h6>
                                                            <p><?= nl2br(htmlspecialchars($app['business_type'])) ?></p>
                                                        </div>

                                                        <div class="details-card p-3 rounded mb-3">
                                                            <h6><i class="bi bi-target"></i> Consultation Goals</h6>
                                                            <p><?= nl2br(htmlspecialchars($app['consultation_goals'])) ?></p>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="details-card p-3 rounded mb-3">
                                                                    <h6><i class="bi bi-globe"></i> Online Presence</h6>
                                                                    <p><?= $app['online_presence'] ? htmlspecialchars($app['online_presence']) : 'None specified' ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="details-card p-3 rounded mb-3">
                                                                    <h6><i class="bi bi-tools"></i> Support Needs</h6>
                                                                    <p><?= $app['support_needs'] ? htmlspecialchars($app['support_needs']) : 'None specified' ?></p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="details-card p-3 rounded mb-3">
                                                                    <h6><i class="bi bi-currency-dollar"></i> Budget Range</h6>
                                                                    <p><?= $app['budget_range'] ? ucwords(str_replace('_', ' ', $app['budget_range'])) : 'Not specified' ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="details-card p-3 rounded mb-3">
                                                                    <h6><i class="bi bi-telephone"></i> Contact Method</h6>
                                                                    <p><?= ucfirst($app['contact_method']) ?></p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <?php if ($app['preferred_datetime']): ?>
                                                            <div class="details-card p-3 rounded mb-3">
                                                                <h6><i class="bi bi-calendar"></i> Preferred Date/Time</h6>
                                                                <p><?= date('F j, Y g:i A', strtotime($app['preferred_datetime'])) ?></p>
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php if ($app['additional_details']): ?>
                                                            <div class="details-card p-3 rounded mb-3">
                                                                <h6><i class="bi bi-chat-text"></i> Additional Details</h6>
                                                                <p><?= nl2br(htmlspecialchars($app['additional_details'])) ?></p>
                                                            </div>
                                                        <?php endif; ?>

                                                        <!-- Status Update Form -->
                                                        <form method="POST" class="mt-3">
                                                            <div class="row align-items-end">
                                                                <div class="col-md-8">
                                                                    <label class="form-label">Update Status</label>
                                                                    <select name="new_status" class="form-select">
                                                                        <option value="new" <?= $app['status'] === 'new' ? 'selected' : '' ?>>New</option>
                                                                        <option value="contacted" <?= $app['status'] === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                                                                        <option value="scheduled" <?= $app['status'] === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                                                                        <option value="completed" <?= $app['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                                                        <option value="cancelled" <?= $app['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input type="hidden" name="submission_id" value="<?= $app['id'] ?>">
                                                                    <button type="submit" name="update_status" class="btn btn-primary w-100">
                                                                        <i class="bi bi-check"></i> Update
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        <?php
        } catch (PDOException $e) {
            echo '<div class="alert alert-danger">Database Error: ' . $e->getMessage() . '</div>';
        }
        ?>
    </div>

<?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>