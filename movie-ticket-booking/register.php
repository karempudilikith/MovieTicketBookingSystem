<?php
/**
 * User Registration Page
 * 
 * Allows new customers to register.
 * Performs server-side validations: checks if passwords match, 
 * validates email format, and checks if the email already exists in the database.
 */

// Title for header
$page_title = "User Registration";

// Include header
require_once 'includes/header.php';

// If user is already logged in, redirect them to the home page
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user inputs to prevent HTML/XSS injection
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    
    // Validations
    if (empty($name)) {
        $errors[] = "Full Name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid Email Address is required.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    
    // If no validation errors, proceed to check database
    if (empty($errors)) {
        // Prepare query to check if email already exists
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $errors[] = "This email is already registered. Please login or use a different email.";
        }
        $stmt->close();
        
        // If email is unique, insert new user record
        if (empty($errors)) {
            // Hash the password for security before saving to database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user'; // Normal user role
            
            $insert_sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
            
            if ($insert_stmt->execute()) {
                $_SESSION['success_message'] = "Registration successful! You can now log in.";
                header("Location: login.php");
                exit();
            } else {
                $errors[] = "Database error: Registration failed. Please try again.";
            }
            $insert_stmt->close();
        }
    }
    
    // Store errors in session to display inside header
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode("<br>", $errors);
        // Refresh page to show errors in header
        header("Location: register.php");
        exit();
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card card-portal form-portal">
            <div class="card-header-portal text-center">
                <h3><i class="bi bi-person-plus"></i> User Registration</h3>
                <p class="mb-0 text-white-50 small">Create your account to book movie tickets</p>
            </div>
            <div class="card-body p-4">
                <form action="register.php" method="POST">
                    
                    <!-- Name Input -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="name" name="name" required placeholder="Enter your full name">
                        </div>
                    </div>
                    
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
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Minimum 6 characters">
                        </div>
                    </div>
                    
                    <!-- Confirm Password Input -->
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Re-enter password">
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-navy py-2"><i class="bi bi-person-check"></i> Register Account</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center bg-light py-3">
                <span class="text-muted">Already have an account?</span> 
                <a href="login.php" class="text-decoration-none fw-bold">Login Here</a>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once 'includes/footer.php';
?>
