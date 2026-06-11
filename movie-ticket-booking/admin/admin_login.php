<?php
/**
 * Admin Login Page
 * 
 * Specific portal for administrators.
 * Verifies email and password, checks if role is 'admin', 
 * and redirects to the admin dashboard.
 */

// Title for header
$page_title = "Admin Portal Login";

// Since this is in the 'admin' subfolder, we adjust path to header
require_once __DIR__ . '/../includes/header.php';

// Redirect if already logged in as admin
if (isAdmin()) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    $errors = [];
    
    if (empty($email) || empty($password)) {
        $errors[] = "Please fill in all fields.";
    } else {
        // Query user with admin role or checks role afterwards
        $sql = "SELECT id, name, email, password, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Check password hash
            if (password_verify($password, $user['password'])) {
                // Check if user is actually an administrator
                if ($user['role'] === 'admin') {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    $_SESSION['success_message'] = "Admin login successful! Welcome to the control dashboard.";
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $errors[] = "Access denied! You do not have administrator privileges.";
                }
            } else {
                $errors[] = "Invalid administrator email or password.";
            }
        } else {
            $errors[] = "Invalid administrator email or password.";
        }
        $stmt->close();
    }
    
    // Store errors in session to display in header
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode("<br>", $errors);
        header("Location: admin_login.php");
        exit();
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card card-portal form-portal shadow">
            <div class="card-header-portal text-center bg-danger border-warning">
                <h3><i class="bi bi-shield-lock-fill"></i> Admin Console</h3>
                <p class="mb-0 text-white-50 small">Restricted to authorized system administrators only</p>
            </div>
            <div class="card-body p-4">
                <form action="admin_login.php" method="POST">
                    
                    <!-- Email Input -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Admin Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person-badge"></i></span>
                            <input type="email" class="form-control" id="email" name="email" required placeholder="admin@gmail.com">
                        </div>
                    </div>
                    
                    <!-- Password Input -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Enter password">
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-danger py-2"><i class="bi bi-unlock-fill"></i> Access Dashboard</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center bg-light py-3">
                <a href="<?php echo BASE_URL; ?>index.php" class="text-decoration-none text-muted small"><i class="bi bi-house-door"></i> Back to Main Homepage</a>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once __DIR__ . '/../includes/footer.php';
?>
