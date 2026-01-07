<?php include 'includes/header.php'; ?>

<main>
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2><i data-lucide="wallet" style="vertical-align: middle; margin-right: 8px;"></i> Accounts</h2>
        <button class="btn btn-primary btn-sm" onclick="openAccountModal()">
            <i data-lucide="plus"></i> Add Account
        </button>
    </div>

    <!-- Accounts Grid -->
    <div id="accounts-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        <!-- JS will populate -->
        <div class="card" style="text-align: center; color: var(--pk-text-muted);">
            Loading accounts...
        </div>
    </div>
</main>

<!-- Add/Edit Account Modal -->
<div class="modal-overlay" id="account-modal">
    <div class="modal">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 id="acc-modal-title">Add Account</h3>
            <i data-lucide="x" style="cursor: pointer;" onclick="closeAccountModal()"></i>
        </div>

        <form id="account-form" onsubmit="handleAccountSubmit(event)">
            <input type="hidden" id="acc-id">

            <div class="form-group">
                <label>Account Name</label>
                <input type="text" id="acc-name" class="form-control" placeholder="e.g. Maybank, Wallet" required>
            </div>

            <div class="form-group">
                <label>Account Type</label>
                <select id="acc-type" class="form-control">
                    <option value="Bank">Bank</option>
                    <option value="Cash">Cash</option>
                    <option value="E-Wallet">E-Wallet</option>
                    <option value="Investment">Investment</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label>Current Balance (MYR)</label>
                <input type="number" step="0.01" id="acc-balance" class="form-control" placeholder="0.00" required>
                <small style="color: var(--pk-text-muted);">Initial balance. It will update automatically with expenses.</small>
            </div>

            <button type="submit" class="btn btn-primary">Save Account</button>
        </form>
    </div>
</div>

<script>
    // Close on click outside
    document.getElementById('account-modal').addEventListener('click', (e) => {
        if (e.target.id === 'account-modal') closeAccountModal();
    });
</script>

<?php include 'includes/footer.php'; ?>