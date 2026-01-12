<?php include 'includes/header.php'; ?>

<main>
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2><i data-lucide="layout-dashboard" style="vertical-align: middle; margin-right: 8px;"></i> Dashboard</h2>
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
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0;">Spend by Category</h3>
                <div style="display: flex; gap: 10px; background: var(--pk-bg); padding: 4px; border-radius: 8px;">
                    <button id="chart-tab-month" onclick="updateChartPeriod('month')" 
                        style="background: var(--pk-card-bg); border: none; padding: 4px 12px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; font-weight: 600; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">Month</button>
                    <button id="chart-tab-year" onclick="updateChartPeriod('year')" 
                        style="background: transparent; border: none; padding: 4px 12px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; font-weight: 600; color: var(--pk-text-muted);">Year</button>
                </div>
            </div>
            <div style="height: 300px; position: relative;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
        <div class="card">
            <h3>Recent Expenses</h3>
            <div id="recent-expenses-list" style="margin-top: 20px;">
                <!-- Skeleton State -->
                <div style="display:flex; justify-content:space-between; padding: 10px 0; border-bottom: 1px solid var(--pk-border);">
                    <div>
                        <div class="skeleton" style="height: 16px; width: 120px; margin-bottom: 6px;"></div>
                        <div class="skeleton" style="height: 12px; width: 80px;"></div>
                    </div>
                    <div class="skeleton" style="height: 16px; width: 60px;"></div>
                </div>
                <div style="display:flex; justify-content:space-between; padding: 10px 0; border-bottom: 1px solid var(--pk-border);">
                    <div>
                        <div class="skeleton" style="height: 16px; width: 100px; margin-bottom: 6px;"></div>
                        <div class="skeleton" style="height: 12px; width: 70px;"></div>
                    </div>
                    <div class="skeleton" style="height: 16px; width: 50px;"></div>
                </div>
                <div style="display:flex; justify-content:space-between; padding: 10px 0; border-bottom: 1px solid var(--pk-border);">
                     <div>
                        <div class="skeleton" style="height: 16px; width: 110px; margin-bottom: 6px;"></div>
                        <div class="skeleton" style="height: 12px; width: 60px;"></div>
                    </div>
                    <div class="skeleton" style="height: 16px; width: 70px;"></div>
                </div>
            </div>
            <div style="margin-top: 20px; text-align: center;">
                <a href="expenses.php" class="btn btn-outline" style="width: auto; padding: 8px 16px; font-size: 0.8rem;">View All</a>
            </div>
        </div>
        <div class="card">
            <h3>Top 5 Merchants</h3>
            <div id="top-merchants-list" style="margin-top: 20px;">
                <!-- Skeleton State -->
                <div style="display:flex; justify-content:space-between; padding: 10px 0; border-bottom: 1px solid var(--pk-border);">
                     <div style="display:flex; align-items:center;">
                        <div class="skeleton" style="height: 14px; width: 14px; margin-right: 10px; border-radius: 50%;"></div>
                        <div class="skeleton" style="height: 16px; width: 90px;"></div>
                     </div>
                     <div class="skeleton" style="height: 16px; width: 50px;"></div>
                </div>
                <div style="display:flex; justify-content:space-between; padding: 10px 0; border-bottom: 1px solid var(--pk-border);">
                     <div style="display:flex; align-items:center;">
                        <div class="skeleton" style="height: 14px; width: 14px; margin-right: 10px; border-radius: 50%;"></div>
                        <div class="skeleton" style="height: 16px; width: 70px;"></div>
                     </div>
                     <div class="skeleton" style="height: 16px; width: 40px;"></div>
                </div>
                <div style="display:flex; justify-content:space-between; padding: 10px 0; border-bottom: 1px solid var(--pk-border);">
                     <div style="display:flex; align-items:center;">
                        <div class="skeleton" style="height: 14px; width: 14px; margin-right: 10px; border-radius: 50%;"></div>
                        <div class="skeleton" style="height: 16px; width: 80px;"></div>
                     </div>
                     <div class="skeleton" style="height: 16px; width: 45px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accounts Section -->
    <div style="margin-top: 30px;">
        <h3 style="margin-bottom: 20px; color: var(--pk-text-muted);">My Accounts</h3>
        <div class="dashboard-grid" id="dashboard-accounts-grid">
            <!-- Skeleton State -->
            <div class="card">
                <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom: 10px;">
                     <div class="skeleton" style="height: 20px; width: 100px;"></div>
                     <div class="skeleton" style="height: 20px; width: 50px; border-radius: 12px;"></div>
                </div>
                <div class="skeleton" style="height: 32px; width: 140px; margin-bottom: 8px;"></div>
            </div>
            <div class="card">
                <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom: 10px;">
                     <div class="skeleton" style="height: 20px; width: 80px;"></div>
                     <div class="skeleton" style="height: 20px; width: 50px; border-radius: 12px;"></div>
                </div>
                <div class="skeleton" style="height: 32px; width: 140px; margin-bottom: 8px;"></div>
            </div>
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