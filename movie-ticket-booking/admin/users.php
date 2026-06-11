<?php
/**
 * Admin Users List Page
 * 
 * Lists all registered users on the CinePass platform.
 * Displays names, emails, roles (admin/user), and registration dates.
 */

// Title for header
$page_title = "Manage Users";

// Include header
require_once __DIR__ . '/../includes/header.php';

// Restrict access to admin only
checkAdmin();

// Fetch users from database ordered by signup date (newest first)
$users_result = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <div>
        <h2 class="text-navy fw-bold mb-0"><i class="bi bi-people"></i> Registered Users</h2>
        <p class="text-muted mb-0">Overview of user profiles and administrator privileges on the system.</p>
    </div>
</div>

<div class="card card-portal overflow-hidden shadow-sm">
    <div class="card-header-portal">
        <i class="bi bi-people-fill"></i> Users Registry Database
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-portal mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 80px;">User ID</th>
                        <th>Full Name</th>
                        <th>Email Address</th>
                        <th>Assigned Role</th>
                        <th>Created Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users_result && $users_result->num_rows > 0): ?>
                        <?php while ($user = $users_result->fetch_assoc()): ?>
                            <tr>
                                <td><span class="badge bg-secondary">U-<?php echo str_pad($user['id'], 4, '0', STR_PAD_LEFT); ?></span></td>
                                <td class="fw-bold text-navy"><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="badge bg-danger text-white px-3 py-2"><i class="bi bi-shield-fill"></i> Administrator</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary text-white px-3 py-2"><i class="bi bi-person-fill"></i> Customer</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted"><?php echo date("d M Y h:i A", strtotime($user['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <i class="bi bi-people display-4 text-muted d-block mb-2"></i>
                                <span class="text-muted">No registered users in the database.</span>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="dashboard.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
</div>

<?php
// Include footer
require_once __DIR__ . '/../includes/footer.php';
?>
