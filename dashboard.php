<?php include 'includes/header.php'; ?>

<main>
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2><i data-lucide="layout-dashboard" style="vertical-align: middle; margin-right: 8px;"></i> Dashboard</h2>
        <div id="user-info" style="color: var(--pk-text-muted);"></div>
    </div>

    <!-- Summary Cards -->
    <div class="dashboard-grid">
        <div class="card">
            <h3>Total Balance</h3>
            <div class="amount" id="total-balance">RM 0.00</div>
            <div class="trend" id="balance-trend">Across all accounts</div>
        </div>

        <div class="card">
            <h3>Total Spent (This Month)</h3>
            <div class="amount" id="total-spent">RM 0.00</div>
            <div class="trend" id="spending-trend">Loading...</div>
        </div>

        <div class="card">
            <h3>Budget Remaining</h3>
            <div class="amount" id="budget-remaining">RM 0.00</div>
            <div class="trend" id="budget-status">Loading...</div>
        </div>


    </div>

    <!-- Charts Area -->
    <div class="dashboard-grid charts-grid" style="grid-template-columns: 2fr 1fr 1fr;">
        <div class="card">
            <h3>Spend by Category</h3>
            <div style="height: 300px; position: relative;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
        <div class="card">
            <h3>Recent Expenses</h3>
            <div id="recent-expenses-list" style="margin-top: 20px;">
                <!-- Populated by JS -->
                <p style="color: var(--pk-text-muted);">Loading...</p>
            </div>
            <div style="margin-top: 20px; text-align: center;">
                <a href="expenses.php" class="btn btn-outline" style="width: auto; padding: 8px 16px; font-size: 0.8rem;">View All</a>
            </div>
        </div>
        <div class="card">
            <h3>Top 5 Merchants</h3>
            <div id="top-merchants-list" style="margin-top: 20px;">
                <!-- Populated by JS -->
                <p style="color: var(--pk-text-muted);">Loading...</p>
            </div>
        </div>
    </div>

    <!-- Accounts Section -->
    <div style="margin-top: 30px;">
        <h3 style="margin-bottom: 20px; color: var(--pk-text-muted);">My Accounts</h3>
        <div class="dashboard-grid" id="dashboard-accounts-grid">
            <!-- Populated by JS -->
            <div class="card">Loading...</div>
        </div>
    </div>

</main>

<!-- Floating Action Button -->
<div class="fab" onclick="window.location.href='expenses.php?action=add'">
    <i data-lucide="plus"></i>
</div>

<script>
    // Dashboard Logic script will be added here or in app.js
    // For now, it's separated in logic, but runs on load.
</script>

<?php include 'includes/footer.php'; ?>