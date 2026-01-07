<?php include 'includes/header.php'; ?>

<main>
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2><i data-lucide="receipt" style="vertical-align: middle; margin-right: 8px;"></i> Expenses</h2>
        <div class="header-actions" style="display: flex; gap: 10px; align-items: center;">
            <input type="month" id="export-month" class="form-control" style="width: auto; padding: 6px 12px; height: 38px;">
            <button class="btn btn-outline" onclick="exportCSV()" style="margin-right: 0;">
                <i data-lucide="download"></i> Export CSV
            </button>
            <button class="btn btn-primary" onclick="openExpenseModal()">
                <i data-lucide="plus"></i> Add Expense
            </button>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="expenses-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Merchant</th>
                        <th>Notes</th>
                        <th>Amount</th>
                        <th style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody id="expenses-list">
                    <!-- JS will populate -->
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--pk-text-muted);">Loading expenses...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Add/Edit Expense Modal -->
<div class="modal-overlay" id="expense-modal">
    <div class="modal">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 id="modal-title">Add Expense</h3>
            <i data-lucide="x" style="cursor: pointer;" onclick="closeExpenseModal()"></i>
        </div>

        <form id="expense-form" onsubmit="handleExpenseSubmit(event)">
            <input type="hidden" id="expense-id">

            <div class="form-group">
                <label>Amount (RM)</label>
                <input type="number" step="0.01" id="amount" class="form-control" placeholder="0.00" required>
            </div>

            <div class="form-group">
                <label>Account (Optional)</label>
                <select id="account-id" class="form-control">
                    <!-- Populated via JS -->
                    <option value="">Select Account</option>
                </select>
            </div>

            <div class="form-group">
                <label>Category</label>
                <select id="category" class="form-control" required>
                    <!-- Populated via JS -->
                </select>
            </div>

            <div class="form-group">
                <label>Date</label>
                <input type="date" id="date" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Merchant</label>
                <input type="text" id="merchant" class="form-control" placeholder="e.g. Tesco, Grab">
            </div>

            <div class="form-group">
                <label>Notes</label>
                <input type="text" id="notes" class="form-control" placeholder="Optional details">
            </div>

            <button type="submit" class="btn btn-primary">Save Expense</button>
        </form>
    </div>
</div>

<script>
    // Modal Logic
    const modal = document.getElementById('expense-modal');

    async function openExpenseModal(expense = null) {
        modal.style.display = 'flex';

        // Populate Categories
        const catSelect = document.getElementById('category');
        catSelect.innerHTML = EXPENSE_CATEGORIES.length > 0 ?
            EXPENSE_CATEGORIES.map(c => `<option value="${c}">${c}</option>`).join('') :
            '<option disabled>No categories found</option>';

        // Populate Accounts (Wait for it since it is async)
        if (typeof populateAccountSelect === 'function') {
            await populateAccountSelect('account-id', expense ? expense.account_id : null);
        }

        // Reset or Fill Form
        if (expense) {
            document.getElementById('modal-title').innerText = 'Edit Expense';
            document.getElementById('expense-id').value = expense.id;
            document.getElementById('amount').value = expense.amount;
            document.getElementById('category').value = expense.category;
            document.getElementById('date').value = expense.date;
            document.getElementById('merchant').value = expense.merchant;
            document.getElementById('notes').value = expense.notes;
        } else {
            document.getElementById('modal-title').innerText = 'Add Expense';
            document.getElementById('expense-form').reset();
            document.getElementById('expense-id').value = '';
            document.getElementById('date').valueAsDate = new Date();
        }
    }

    function closeExpenseModal() {
        modal.style.display = 'none';
    }

    // Close on click outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeExpenseModal();
    });


</script>

<?php include 'includes/footer.php'; ?>