<?php include 'includes/header.php'; ?>

<main>
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2><i data-lucide="pie-chart" style="vertical-align: middle; margin-right: 8px;"></i> Budgets</h2>
        <div style="color: var(--pk-text-muted);" id="current-month-display">
            <!-- Populated via JS -->
        </div>
    </div>

    <!-- Budget Grid -->
    <div class="dashboard-grid" id="budget-grid">
        <!-- Budget Cards will be injected here -->
        <div class="card" style="text-align: center; color: var(--pk-text-muted);">
            Loading Budgets...
        </div>
    </div>



</main>

<!-- Budget Edit Modal -->
<div class="modal-overlay" id="budget-modal">
    <div class="modal">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3>Set Budget</h3>
            <i data-lucide="x" style="cursor: pointer;" onclick="closeBudgetModal()"></i>
        </div>
        <form id="budget-form" onsubmit="handleBudgetSubmit(event)">
            <input type="hidden" id="budget-category-hidden">

            <p id="budget-modal-category" style="margin-bottom: 15px; font-weight: 600; color: var(--pk-primary); font-size: 1.1rem;"></p>

            <div class="form-group">
                <label>Monthly Limit (RM)</label>
                <input type="number" step="10" id="budget-limit" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Save Budget</button>
        </form>
    </div>
</div>

<script>
    const budgetModal = document.getElementById('budget-modal');

    function openBudgetModal(category, currentLimit) {
        document.getElementById('budget-category-hidden').value = category;
        document.getElementById('budget-modal-category').innerText = category;
        document.getElementById('budget-limit').value = currentLimit || 0;
        budgetModal.style.display = 'flex';
    }

    function closeBudgetModal() {
        budgetModal.style.display = 'none';
    }

    budgetModal.addEventListener('click', (e) => {
        if (e.target === budgetModal) closeBudgetModal();
    });
</script>

<?php include 'includes/footer.php'; ?>