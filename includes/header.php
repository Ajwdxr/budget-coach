<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MYR Budget Coach</title>

    <!-- Supabase JS -->
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- App Styles -->
    <link rel="stylesheet" href="css/style.css">
    
    <!-- html2pdf for PDF export -->
    <script src="js/html2pdf.bundle.min.js"></script>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
</head>

<body>
    
    <!-- Global Loader -->
    <div id="globalLoader" class="global-loader">
        <div class="loader-spinner"></div>
    </div>

    <script>
        // Global Loader Control
        window.hideGlobalLoader = function() {
            const loader = document.getElementById('globalLoader');
            if (loader && !loader.classList.contains('hidden')) {
                loader.classList.add('hidden');
            }
        };

        // Safety fallback: Hide after 5 seconds if not triggered manually
        setTimeout(() => {
            window.hideGlobalLoader();
        }, 5000);
    </script>

    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    if ($current_page !== 'index.php'):
    ?>
        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" onclick="toggleSidebar()">
            <i data-lucide="menu"></i>
        </button>

        <div class="app-layout">
            <!-- Sidebar -->
            <aside class="sidebar" id="appSidebar">
                <div class="sidebar-header">
                    <a href="dashboard.php" class="brand">
                        <i data-lucide="wallet"></i> Budget Coach
                    </a>
                </div>

                <nav class="sidebar-nav">
                    <a href="dashboard.php" class="nav-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                        <i data-lucide="layout-dashboard"></i> Dashboard
                    </a>
                    <a href="expenses.php" class="nav-link <?= $current_page == 'expenses.php' ? 'active' : '' ?>">
                        <i data-lucide="receipt"></i> Expenses
                    </a>
                    <a href="budgets.php" class="nav-link <?= $current_page == 'budgets.php' ? 'active' : '' ?>">
                        <i data-lucide="pie-chart"></i> Budgets
                    </a>
                    <a href="categories.php" class="nav-link <?= $current_page == 'categories.php' ? 'active' : '' ?>">
                        <i data-lucide="tags"></i> Categories
                    </a>
                    <a href="accounts.php" class="nav-link <?= $current_page == 'accounts.php' ? 'active' : '' ?>">
                        <i data-lucide="credit-card"></i> Accounts
                    </a>
                </nav>

                <div class="sidebar-footer">
                    <div class="user-profile" onclick="signOut()" style="cursor: pointer;">
                        <div class="user-avatar">
                            <i data-lucide="user"></i>
                        </div>
                        <div class="user-info">
                            <div class="user-name" id="sidebar-user-name">Loading...</div>
                            <div class="user-role">Sign Out</div>
                        </div>
                        <i data-lucide="log-out" style="width: 16px; height: 16px; color: var(--pk-text-muted);"></i>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="main-content">
                <!-- Overlay for mobile when sidebar is open -->
                <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <?php endif; ?>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('appSidebar');
            sidebar.classList.toggle('active');
        }
    </script>