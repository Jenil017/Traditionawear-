<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rameshwar Traditional Wear</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/hero.css">
</head>
<body>
<!-- Premium Natural Header -->
<header class="premium-header">
    <nav class="navbar navbar-expand-lg navbar-light shadow-sm">
        <div class="container-fluid px-4">
            <!-- Brand Section -->
            <div class="navbar-brand-section d-flex align-items-center">
                <a class="navbar-brand d-flex align-items-center text-decoration-none" href="../user/index.php">
                    <div class="logo-container me-3">
                       
                    </div>
                    <div class="brand-text">
                        <h1 class="brand-name mb-0">Rameshwar</h1>
                        <p class="brand-tagline mb-0">Traditional Wear</p>
                    </div>
                </a>
            </div>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Content -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Main Navigation -->
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="../user/index.php">
                            <i class="fas fa-home nav-icon"></i>
                            <span>HOME</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="../user/products.php">
                            <i class="fas fa-tshirt nav-icon"></i>
                            <span>COLLECTIONS</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="../user/about.php">
                            <i class="fas fa-info-circle nav-icon"></i>
                            <span>ABOUT US</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="../user/contact.php">
                            <i class="fas fa-phone nav-icon"></i>
                            <span>CONTACT US</span>
                        </a>
                    </li>
                </ul>

                <!-- User Actions -->
                <div class="navbar-actions d-flex align-items-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Cart Icon -->
                        <a href="../user/cart.php" class="btn btn-cart me-2 position-relative">
                            <i class="fas fa-shopping-cart"></i>
                            <?php 
                            $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                            if ($cart_count > 0): 
                            ?>
                                <span class="cart-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?= $cart_count ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <!-- User Account Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-account dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar me-2">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="user-info d-none d-md-block">
                                    <span class="user-name"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                                    <small class="user-role">Member</small>
                                </div>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end user-dropdown shadow" aria-labelledby="userDropdown">
                                <li class="dropdown-header">
                                    <div class="user-header">
                                        <i class="fas fa-user-circle user-avatar-large"></i>
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($_SESSION['user_name']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($_SESSION['user_email']) ?></small>
                                        </div>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item dropdown-item-custom" href="../user/my-bookings.php">
                                        <i class="fas fa-shopping-bag me-2 text-primary"></i>
                                        My Orders
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item dropdown-item-custom" href="../user/profile.php">
                                        <i class="fas fa-user-edit me-2 text-success"></i>
                                        Profile Settings
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item dropdown-item-custom text-danger" href="../api/logout.php">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <!-- Guest Actions -->
                        <div class="guest-actions d-flex gap-2">
                            <a href="../user/login.php" class="btn btn-outline-primary btn-auth">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                Login
                            </a>
                            <a href="../user/register.php" class="btn btn-primary btn-auth">
                                <i class="fas fa-user-plus me-1"></i>
                                Register
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>
<!-- Modern Marquee Section - Rameshwar Traditional Wear -->
<div class="rtw-marquee-bar">
  <div class="rtw-marquee-track">
    <span>
      Kurta &nbsp;&nbsp; Sherwani &nbsp;&nbsp; Blazer &nbsp;&nbsp; Jodhpuri &nbsp;&nbsp; Indowestern &nbsp;&nbsp; Bandhgala &nbsp;&nbsp; Nehru Jacket &nbsp;&nbsp; Pathani &nbsp;&nbsp; Achkan &nbsp;&nbsp; Safa &nbsp;&nbsp; Mojari &nbsp;&nbsp; Dupatta &nbsp;&nbsp; and more
    </span>
    <span>
      Kurta &nbsp;&nbsp; Sherwani &nbsp;&nbsp; Blazer &nbsp;&nbsp; Jodhpuri &nbsp;&nbsp; Indowestern &nbsp;&nbsp; Bandhgala &nbsp;&nbsp; Nehru Jacket &nbsp;&nbsp; Pathani &nbsp;&nbsp; Achkan &nbsp;&nbsp; Safa &nbsp;&nbsp; Mojari &nbsp;&nbsp; Dupatta &nbsp;&nbsp; and more
    </span>
  </div>
</div>

<div class="container-fluid py-4">
<!-- Page content goes here -->
