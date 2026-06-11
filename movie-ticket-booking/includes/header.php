<?php
/**
 * Shared Header Template
 * 
 * This file contains the standard HTML head section, Bootstrap 5 imports,
 * custom CSS references, and the dynamic top navigation bar.
 */

// Include config and functions files to ensure sessions and helpers are active
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . " - " . APP_NAME : APP_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS (Premium Portal Style via CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom Style Sheet -->
    <link href="<?php echo BASE_URL; ?>css/style.css" rel="stylesheet">
    
    <!-- Bootstrap Icons (Useful for clean portal UI graphics) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>

    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <!-- Brand Logo / Name -->
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>index.php">
                <i class="bi bi-ticket-perforated-fill"></i> Cine<span>Pass</span>
            </a>
            
            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navbar Links -->
            <div class="collapse navbar-collapse" id="navbarText">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-link-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>index.php">
                            <i class="bi bi-house-door"></i> Home
                        </a>
                    </li>
                    
                    <?php if (isLoggedIn() && !isAdmin()): ?>
                        <!-- Standard logged-in user links -->
                        <li class="nav-link-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'booking_history.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>booking_history.php">
                                <i class="bi bi-clock-history"></i> My Bookings
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (isAdmin()): ?>
                        <!-- Admin Dashboard direct shortcut if logged in as admin -->
                        <li class="nav-link-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>admin/dashboard.php">
                                <i class="bi bi-speedometer2"></i> Admin Panel
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <!-- Right Side Account Options -->
                <span class="navbar-text">
                    <ul class="navbar-nav">
                        <?php if (isLoggedIn()): ?>
                            <!-- Account name badge and logout button -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle active text-warning" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle"></i> Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <?php if (isAdmin()): ?>
                                        <li><a class="dropdown-menu-item dropdown-item" href="<?php echo BASE_URL; ?>admin/dashboard.php"><i class="bi bi-shield-lock"></i> Dashboard</a></li>
                                    <?php else: ?>
                                        <li><a class="dropdown-menu-item dropdown-item" href="<?php echo BASE_URL; ?>booking_history.php"><i class="bi bi-ticket-detailed"></i> Bookings</a></li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-menu-item dropdown-item text-danger" href="<?php echo BASE_URL; ?>logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <!-- Registration and Login forms buttons -->
                            <li class="nav-item">
                                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>login.php">
                                    <i class="bi bi-box-arrow-in-right"></i> Login
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'register.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>register.php">
                                    <i class="bi bi-person-plus"></i> Register
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </span>
            </div>
        </div>
    </nav>

    <!-- Error/Success Status Alerts Container -->
    <div class="container mt-3">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Container Wrapper -->
    <main class="container my-4">
