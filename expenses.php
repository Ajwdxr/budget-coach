<?php include 'includes/header.php'; ?>

<main>
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2><i data-lucide="receipt" style="vertical-align: middle; margin-right: 8px;"></i> Expenses</h2>
        <div class="header-actions" style="display: flex; gap: 10px; align-items: center;">
            <input type="month" id="filter-month" class="form-control" style="width: auto; padding: 6px 12px; height: 38px;" onchange="loadExpenses(this.value)">
            <button class="btn btn-outline" id="btn-download-pdf">
                <i data-lucide="file-text"></i> Download PDF
            </button>
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
                    <!-- Skeleton Rows -->
                    <tr>
                        <td><div class="skeleton" style="height: 16px; width: 80px;"></div></td>
                        <td><div class="skeleton" style="height: 24px; width: 100px; border-radius: 20px;"></div></td>
                        <td><div class="skeleton" style="height: 16px; width: 120px;"></div></td>
                        <td><div class="skeleton" style="height: 16px; width: 150px;"></div></td>
                        <td><div class="skeleton" style="height: 16px; width: 60px;"></div></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><div class="skeleton" style="height: 16px; width: 80px;"></div></td>
                        <td><div class="skeleton" style="height: 24px; width: 90px; border-radius: 20px;"></div></td>
                        <td><div class="skeleton" style="height: 16px; width: 100px;"></div></td>
                        <td><div class="skeleton" style="height: 16px; width: 120px;"></div></td>
                        <td><div class="skeleton" style="height: 16px; width: 50px;"></div></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><div class="skeleton" style="height: 16px; width: 80px;"></div></td>
                        <td><div class="skeleton" style="height: 24px; width: 110px; border-radius: 20px;"></div></td>
                        <td><div class="skeleton" style="height: 16px; width: 110px;"></div></td>
                        <td><div class="skeleton" style="height: 16px; width: 100px;"></div></td>
                        <td><div class="skeleton" style="height: 16px; width: 70px;"></div></td>
                        <td></td>
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

    // PDF Download Button - Wait for app.js module to load
    document.getElementById('btn-download-pdf').addEventListener('click', () => {
        if (typeof window.downloadPDF === 'function') {
            window.downloadPDF();
        } else {
            // Fallback if function not yet loaded
            alert('PDF feature is loading, please try again in a moment.');
        }
    });


</script>

<!-- PDF Report Template (Hidden) -->
<div id="pdf-report" style="display: none;">
    <div style="padding: 20px; font-family: sans-serif;">
        <div class="pdf-header" style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 20px;">
            <img src="favicon.svg" style="width: 48px; height: 48px; margin-bottom: 10px; display: inline-block;">
            <h2 style="color: #1e293b; margin: 0; font-size: 24px;">Monthly Expense Report</h2>
            <p id="pdf-period" style="color: #64748b; margin: 5px 0; font-size: 14px;">Period: YYYY-MM</p>
        </div>
        
        <div class="pdf-summary" style="margin-bottom: 30px; padding: 20px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
            <h3 style="margin: 0 0 15px 0; color: #334155; font-size: 16px;">Summary</h3>
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
                <div>
                    <span style="color: #64748b; font-size: 12px;">Total Expenses</span>
                    <div id="pdf-total" style="font-size: 24px; font-weight: bold; color: #0f172a;">RM 0.00</div>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 12px;">Total Budget</span>
                    <div id="pdf-budget" style="font-size: 24px; font-weight: bold; color: #3b82f6;">RM 0.00</div>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 12px;">Budget Remaining</span>
                    <div id="pdf-remaining" style="font-size: 24px; font-weight: bold; color: #10b981;">RM 0.00</div>
                </div>
                <div style="text-align: right;">
                    <span style="color: #64748b; font-size: 12px;">Total Items</span>
                    <div id="pdf-count" style="font-size: 24px; font-weight: bold; color: #0f172a;">0</div>
                </div>
            </div>
        </div>

        <table class="pdf-table" style="width: 100%; border-collapse: collapse; font-size: 12px;">
            <thead>
                <tr style="background: #3b82f6; color: white;">
                    <th style="padding: 12px; text-align: left; border-radius: 6px 0 0 6px;">Date</th>
                    <th style="padding: 12px; text-align: left;">Category</th>
                    <th style="padding: 12px; text-align: left;">Merchant</th>
                    <th style="padding: 12px; text-align: right; border-radius: 0 6px 6px 0;">Amount</th>
                </tr>
            </thead>
            <tbody id="pdf-list">
                <!-- Items -->
            </tbody>
        </table>
        
        <div class="pdf-footer" style="margin-top: 40px; text-align: center; font-size: 10px; color: #94a3b8; border-top: 1px solid #eee; padding-top: 20px;">
            Generated on <span id="pdf-generated-date"></span> by <strong>Budget Coach</strong>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>