<?php
/**
 * User Login Page
 * 
 * Allows registered users (and admins) to log in.
 * Verifies email and password, initializes sessions, and redirects
 * users based on their access roles (admin to Dashboard, user to Homepage).
 */

// Title for header
$page_title = "User Login";

// Include header
require_once 'includes/header.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if (isAdmin()) {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    $errors = [];
    
    if (empty($email) || empty($password)) {
        $errors[] = "Please fill in all fields.";
    } else {
        // Query user record by email
        $sql = "SELECT id, name, email, password, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password hash
            if (password_verify($password, $user['password'])) {
                // Initialize session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                
                $_SESSION['success_message'] = "Welcome back, " . $user['name'] . "!";
                
                // Redirect user based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    // Redirect back to page user tried to access, or homepage
                    $redirect_to = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'index.php';
                    unset($_SESSION['redirect_url']);
                    header("Location: " . $redirect_to);
                }
                exit();
            } else {
                $errors[] = "Invalid email or password.";
            }
        } else {
            $errors[] = "Invalid email or password.";
        }
        $stmt->close();
    }
    
    // Store errors in session to display inside header
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode("<br>", $errors);
        header("Location: login.php");
        exit();
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card card-portal form-portal">
            <div class="card-header-portal text-center">
                <h3><i class="bi bi-box-arrow-in-right"></i> User Login</h3>
                <p class="mb-0 text-white-50 small">Enter credentials to access booking system</p>
            </div>
            <div class="card-body p-4">
                <form action="login.php" method="POST">
                    
                    <!-- Email Input -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" required placeholder="name@example.com">
                        </div>
                    </div>
                    
                    <!-- Password Input -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Enter password">
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-navy py-2"><i class="bi bi-box-arrow-in-right"></i> Log In</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center bg-light py-3">
                <span class="text-muted">Don't have an account?</span> 
                <a href="register.php" class="text-decoration-none fw-bold">Register Here</a>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once 'includes/footer.php';
?>
