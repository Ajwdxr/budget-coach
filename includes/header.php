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
        <div class="app-container">
            <header>
                <div class="brand">
                    <i data-lucide="wallet"></i> Budget Coach
                </div>
                <nav>
                    <ul>
                        <li><a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>
                        <li><a href="expenses.php" class="<?= $current_page == 'expenses.php' ? 'active' : '' ?>">Expenses</a></li>
                        <li><a href="budgets.php" class="<?= $current_page == 'budgets.php' ? 'active' : '' ?>">Budgets</a></li>
                        <li><a href="categories.php" class="<?= $current_page == 'categories.php' ? 'active' : '' ?>">Categories</a></li>
                        <li><a href="accounts.php" class="<?= $current_page == 'accounts.php' ? 'active' : '' ?>">Accounts</a></li>
                        <li><a href="#" onclick="signOut()">Logout</a></li>
                    </ul>
                </nav>
            </header>
        <?php endif; ?>