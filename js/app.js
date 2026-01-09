
// Initialize Supabase Client
// TODO: Replace these with your actual Supabase URL and Key
const SUPABASE_URL = 'https://tnmgmbbasoyjrocfppte.supabase.co';
const SUPABASE_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InRubWdtYmJhc295anJvY2ZwcHRlIiwicm9sZSI6ImFub24iLCJpYXQiOjE3Njc3NTcyOTUsImV4cCI6MjA4MzMzMzI5NX0.LrTh736RBowkVveuyv_N7bXgtarn6k0JZCkIAZ-TWqc';

let supabase;

try {
    supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_KEY);
    console.log('Supabase initialized');
} catch (error) {
    console.error('Supabase initialization failed:', error);
}

// Auth State Listener
async function initAuthCheck() {
    const { data: { session } } = await supabase.auth.getSession();

    // Check if we are on the login page (index.php)
    const isLoginPage = window.location.pathname.includes('index.php') || window.location.pathname.endsWith('/budget/') || window.location.pathname === '/budget/';

    if (session) {
        // User is logged in
        if (isLoginPage) {
            window.location.href = 'dashboard.php';
        }
    } else {
        // User is not logged in
        if (!isLoginPage) {
            window.location.href = 'index.php';
        }
    }

    supabase.auth.onAuthStateChange((event, session) => {
        if (event === 'SIGNED_IN') {
            if (isLoginPage) window.location.href = 'dashboard.php';
        }
        if (event === 'SIGNED_OUT') {
            if (!isLoginPage) window.location.href = 'index.php';
        }
    });
}

// Login Function
async function signInWithProvider(provider) {
    const { data, error } = await supabase.auth.signInWithOAuth({
        provider: provider,
        options: {
            redirectTo: window.location.origin + '/budget/dashboard.php'
        }
    });
    if (error) console.error('Login error:', error);
}

// Make globally available
window.signInWithProvider = signInWithProvider;

// Logout Function
async function signOut() {
    const { error } = await supabase.auth.signOut();
    if (error) console.error('Logout error:', error);
}

// Make globally available
window.signOut = signOut;

// Utility: Format Currency (MYR)
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-MY', {
        style: 'currency',
        currency: 'MYR'
    }).format(amount);
}

// Utility: Animate Count Up
function animateCountUp(element, finalValue, duration = 1500) {
    if (!element) return;

    // Ensure finalValue is a number
    finalValue = parseFloat(finalValue) || 0;
    const startValue = 0;
    let startTimestamp = null;

    // Ease Out Cubic function for smooth deceleration
    const easeOutCubic = (x) => 1 - Math.pow(1 - x, 3);

    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const easeProgress = easeOutCubic(progress);

        const currentVal = startValue + (finalValue - startValue) * easeProgress;

        element.innerText = formatCurrency(currentVal);

        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else {
            element.innerText = formatCurrency(finalValue); // Ensure exact final value
        }
    };
    window.requestAnimationFrame(step);
}

// Run Auth Check on Load
document.addEventListener('DOMContentLoaded', async () => {
    if (typeof window.supabase !== 'undefined') {
        initAuthCheck();

        // Only run app logic if we are NOT on the login page and we have a session
        const { data: { session } } = await supabase.auth.getSession();
        const isLoginPage = window.location.pathname.includes('index.php') || window.location.pathname.endsWith('/budget/') || window.location.pathname === '/budget/';

        if (session && !isLoginPage) {
            await initializePage();
            if (window.hideGlobalLoader) window.hideGlobalLoader();
        } else if (isLoginPage) {
            // On login page, just hide loader
            if (window.hideGlobalLoader) window.hideGlobalLoader();
        }
    } else {
        console.error('Supabase SDK not loaded.');
    }
});

// --- Core App Logic ---

// initializePage is redefined at the bottom. Reference removed to avoid conflict.

// Global Categories List
window.EXPENSE_CATEGORIES = [];

async function fetchCategories() {
    const { data, error } = await supabase.from('categories').select('*').order('name');
    if (error) {
        console.error('Error fetching categories:', error);
        return;
    }

    // If no categories, map to names.
    window.EXPENSE_CATEGORIES = data.map(c => c.name);
}

// --- Dashboard Functions ---

async function loadDashboard() {
    // Date Logic: First day of current month to first day of next month
    const now = new Date();
    const firstDay = new Date(now.getFullYear(), now.getMonth(), 1).toLocaleDateString('en-CA'); // YYYY-MM-DD
    const nextMonth = new Date(now.getFullYear(), now.getMonth() + 1, 1).toLocaleDateString('en-CA');
    const monthStr = now.toISOString().slice(0, 7); // For budget text matching

    console.log(`Fetching Data: ${firstDay} to ${nextMonth}`);

    // Yearly Range
    const startOfYear = `${now.getFullYear()}-01-01`;
    const endOfYear = `${now.getFullYear() + 1}-01-01`;

    // Parallel fetch for efficiency
    const [resExpenses, resBudgets, resAccounts, resYearlyExpenses] = await Promise.all([
        supabase.from('expenses').select('*')
            .gte('date', firstDay)
            .lt('date', nextMonth)
            .order('date', { ascending: false }),
        supabase.from('budgets').select('amount_limit').eq('month', monthStr),
        supabase.from('accounts').select('*').order('name'),
        supabase.from('expenses').select('category, amount')
            .gte('date', startOfYear)
            .lt('date', endOfYear)
    ]);

    if (resExpenses.error) console.error('Dashboard Expenses Error:', resExpenses.error);
    if (resBudgets.error) console.error('Dashboard Budgets Error:', resBudgets.error);
    if (resAccounts.error) console.error('Dashboard Accounts Error:', resAccounts.error);

    const expenses = resExpenses.data || [];
    const budgets = resBudgets.data || [];
    const accounts = resAccounts.data || [];
    const yearlyExpenses = resYearlyExpenses.data || [];

    // --- 0. Total Balance & Accounts ---
    const totalBalance = accounts.reduce((sum, item) => sum + parseFloat(item.balance), 0);
    const balanceEl = document.getElementById('total-balance');
    if (balanceEl) setTimeout(() => animateCountUp(balanceEl, totalBalance), 600);

    const accountsGrid = document.getElementById('dashboard-accounts-grid');
    if (accountsGrid) {
        if (accounts.length === 0) {
            accountsGrid.innerHTML = `
                <div class="card" style="grid-column: 1 / -1; text-align: center; color: var(--pk-text-muted);">
                    No accounts found. <a href="accounts.php" style="color: var(--pk-primary);">Add one?</a>
                </div>`;
        } else {
            accountsGrid.innerHTML = accounts.map(acc => `
                <div class="card">
                    <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom: 10px;">
                        <h3 style="margin:0;">${acc.name}</h3>
                        <span class="badge">${acc.type}</span>
                    </div>
                    <div style="font-size: 1.5rem; font-weight:700;">${formatCurrency(acc.balance)}</div>
                </div>
            `).join('');
        }
    }


    // 1. Calculate Total Spent
    const totalSpent = expenses.reduce((sum, item) => sum + parseFloat(item.amount), 0);
    const totalSpentEl = document.getElementById('total-spent');
    if (totalSpentEl) setTimeout(() => animateCountUp(totalSpentEl, totalSpent), 600);

    // 2. Budget Remaining (Simplified - fetch all budgets for this month)
    // Budgets already fetched above

    const totalBudget = budgets.reduce((sum, item) => sum + parseFloat(item.amount_limit), 0);
    const remaining = totalBudget - totalSpent;

    const budgetElement = document.getElementById('budget-remaining');
    if (budgetElement) setTimeout(() => animateCountUp(budgetElement, remaining), 600);

    // Status Text
    const statusEl = document.getElementById('budget-status');
    const budgetCard = document.getElementById('budget-remaining').closest('.card'); // Get parent card

    if (statusEl) {
        if (totalBudget === 0) {
            statusEl.innerText = "No budgets set";
            statusEl.style.color = "var(--pk-text-muted)";
            if (budgetCard) budgetCard.classList.remove('card-danger');
        } else if (remaining < 0) {
            statusEl.innerHTML = "⚠️ Over Budget!";
            statusEl.className = "trend up text-danger";
            if (budgetElement) budgetElement.style.color = "var(--pk-danger)";

            // Highlight Card
            if (budgetCard) budgetCard.classList.add('card-danger');

        } else {
            statusEl.innerText = "On Track";
            statusEl.className = "trend down";
            if (budgetElement) budgetElement.style.color = "var(--pk-primary)";
            if (budgetCard) budgetCard.classList.remove('card-danger');
        }
    }

    // 3. Category Chart
    const categoryTotalsMonth = {};
    expenses.forEach(ex => {
        categoryTotalsMonth[ex.category] = (categoryTotalsMonth[ex.category] || 0) + parseFloat(ex.amount);
    });

    const categoryTotalsYear = {};
    yearlyExpenses.forEach(ex => {
        categoryTotalsYear[ex.category] = (categoryTotalsYear[ex.category] || 0) + parseFloat(ex.amount);
    });

    // Store globally for switching
    window.chartData = {
        month: categoryTotalsMonth,
        year: categoryTotalsYear
    };

    // Initial Render
    updateChartPeriod('month');

    // 4. Recent List
    const listEl = document.getElementById('recent-expenses-list');
    listEl.innerHTML = expenses.slice(0, 5).map(ex => `
        <div style="display:flex; justify-content:space-between; padding: 10px 0; border-bottom: 1px solid var(--pk-border);">
            <div>
                <div style="font-weight: 500;">${ex.merchant || ex.category}</div>
                <div style="font-size: 0.8rem; color: var(--pk-text-muted);">${ex.date}</div>
            </div>
            <div style="font-weight: 600;">${formatCurrency(ex.amount)}</div>
        </div>
    `).join('');

    // 5. Top 5 Merchants
    const merchantTotals = {};
    expenses.forEach(ex => {
        const key = ex.merchant ? ex.merchant.trim() : (ex.notes ? ex.notes.trim() : 'Unknown');
        if (key && key !== 'Unknown') {
            merchantTotals[key] = (merchantTotals[key] || 0) + parseFloat(ex.amount);
        }
    });

    // Convert to array and sort
    const sortedMerchants = Object.entries(merchantTotals)
        .map(([name, total]) => ({ name, total }))
        .sort((a, b) => b.total - a.total)
        .slice(0, 5);

    const merchantsEl = document.getElementById('top-merchants-list');
    if (merchantsEl) {
        if (sortedMerchants.length === 0) {
            merchantsEl.innerHTML = '<p style="color: var(--pk-text-muted);">No data yet.</p>';
        } else {
            merchantsEl.innerHTML = sortedMerchants.map((m, index) => `
                <div style="display:flex; justify-content:space-between; padding: 10px 0; border-bottom: 1px solid var(--pk-border);">
                    <div style="display:flex; align-items:center;">
                        <span style="font-weight:bold; color:var(--pk-primary); margin-right:10px; width:15px;">#${index + 1}</span>
                        <div style="font-weight: 500;">${m.name}</div>
                    </div>
                    <div style="font-weight: 600;">${formatCurrency(m.total)}</div>
                </div>
            `).join('');
        }
    }
}


// --- Chart Update Logic ---
window.updateChartPeriod = (viewType) => {
    // viewType: 'month' or 'year'
    if (!window.chartData) return;

    const data = window.chartData[viewType];
    const labels = Object.keys(data);
    const values = Object.values(data);

    // Update Buttons
    const btnMonth = document.getElementById('chart-tab-month');
    const btnYear = document.getElementById('chart-tab-year');

    const activeStyle = "background: var(--pk-card-bg); border: none; padding: 4px 12px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; font-weight: 600; box-shadow: 0 1px 2px rgba(0,0,0,0.1); color: var(--pk-text-primary); transition: all 0.2s;";
    const inactiveStyle = "background: transparent; border: none; padding: 4px 12px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; font-weight: 600; color: var(--pk-text-muted); transition: all 0.2s;";

    if (viewType === 'month') {
        if (btnMonth) btnMonth.style.cssText = activeStyle;
        if (btnYear) btnYear.style.cssText = inactiveStyle;
    } else {
        if (btnMonth) btnMonth.style.cssText = inactiveStyle;
        if (btnYear) btnYear.style.cssText = activeStyle;
    }

    // Update Chart
    if (window.myCategoryChart) {
        window.myCategoryChart.destroy();
    }

    const ctx = document.getElementById('categoryChart').getContext('2d');
    window.myCategoryChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444', '#8b5cf6', '#ec4899', '#64748b'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right', labels: { color: '#94a3b8' } }
            },
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 1000,
                easing: 'easeOutQuart'
            }
        }
    });

};

// --- Expenses Page Functions ---

async function loadExpenses() {
    const { data: expenses, error } = await supabase
        .from('expenses')
        .select('*')
        .order('date', { ascending: false });

    if (error) {
        console.error(error);
        return;
    }

    const tbody = document.getElementById('expenses-list');
    if (expenses.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 30px;">No expenses found. Add one!</td></tr>';
        return;
    }

    tbody.innerHTML = expenses.map(ex => `
        <tr>
            <td>${ex.date}</td>
            <td><span class="badge">${ex.category}</span></td>
            <td>${ex.merchant || '-'}</td>
            <td style="color:var(--pk-text-muted); font-size: 0.9em;">${ex.notes || '-'}</td>
            <td style="font-weight: 600;">${formatCurrency(ex.amount)}</td>
            <td>
                <div style="display:flex; gap: 10px;">
                    <i data-lucide="edit-2" style="width:16px; cursor:pointer;" onclick='editExpense(${JSON.stringify(ex)})'></i>
                    <i data-lucide="trash" style="width:16px; cursor:pointer; color: var(--pk-danger);" onclick="deleteExpense('${ex.id}')"></i>
                </div>
            </td>
        </tr>
    `).join('');

    lucide.createIcons(); // Re-init icons for dynamic content

    // Check URL params for auto-open (Moved from expenses.php to ensure data is loaded)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'add') {
        // Ensure openExpenseModal is available
        if (typeof openExpenseModal === 'function') {
            openExpenseModal();
            // Clear param to prevent reopening on reload
            window.history.replaceState({}, document.title, window.location.pathname);
        } else {
            // Retry shortly if function not ready yet (rare edge case since this runs in initializePage)
            setTimeout(() => {
                if (typeof openExpenseModal === 'function') openExpenseModal();
            }, 500);
        }
    }
}

window.handleExpenseSubmit = async (e) => {
    e.preventDefault();
    const id = document.getElementById('expense-id').value;
    const amount = document.getElementById('amount').value;
    const category = document.getElementById('category').value;
    const date = document.getElementById('date').value;
    const merchant = document.getElementById('merchant').value;
    const notes = document.getElementById('notes').value;
    const user = (await supabase.auth.getUser()).data.user;

    const payload = {
        user_id: user.id,
        amount,
        category,
        date,
        merchant,
        notes
    };

    let error;
    if (id) {
        const { error: err } = await supabase.from('expenses').update(payload).eq('id', id);
        error = err;
    } else {
        const { error: err } = await supabase.from('expenses').insert([payload]);
        error = err;
    }

    if (error) {
        alert('Error saving expense: ' + error.message);
    } else {
        closeExpenseModal();
        loadExpenses();
    }
};

window.editExpense = (item) => {
    // defined in global scope for HTML onclick access
    // We need to call the specialized UI function from expenses.php. 
    // Since this constitutes a cross-module call, we rely on `openExpenseModal` being global.
    if (typeof openExpenseModal === 'function') openExpenseModal(item);
};

window.deleteExpense = async (id) => {
    if (!confirm('Are you sure you want to delete this expense?')) return;
    const { error } = await supabase.from('expenses').delete().eq('id', id);
    if (error) alert('Error deleting: ' + error.message);
    else loadExpenses();
};

window.exportCSV = async () => {
    const monthInput = document.getElementById('export-month');
    const monthVal = monthInput ? monthInput.value : null;

    let query = supabase.from('expenses').select('*').order('date', { ascending: false });

    if (monthVal) {
        // Calculate First and Last Day
        const firstDay = monthVal + '-01';
        // Calculate next month for exclusive upper bound
        const date = new Date(monthVal + '-01');
        date.setMonth(date.getMonth() + 1);
        const nextMonth = date.toISOString().slice(0, 10);

        query = query.gte('date', firstDay).lt('date', nextMonth);
        console.log(`Exporting for: ${firstDay} to ${nextMonth}`);
    }

    const { data: expenses, error } = await query;

    if (error) {
        alert("Error exporting: " + error.message);
        return;
    }

    if (!expenses || expenses.length === 0) {
        alert("No expenses found for export.");
        return;
    }

    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "Date,Amount,Category,Merchant,Notes\n";

    expenses.forEach(row => {
        csvContent += `${row.date},${row.amount},${row.category},${row.merchant || ''},${row.notes || ''}\n`;
    });

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    // Add month to filename if selected
    const filename = monthVal ? `expenses_${monthVal}.csv` : "expenses_export.csv";
    link.setAttribute("download", filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link); // Clean up
};


// --- Budgets Page Functions ---

async function loadBudgets() {
    const monthStr = new Date().toISOString().slice(0, 7); // Current Month
    document.getElementById('current-month-display').innerText = `Month: ${monthStr}`;

    // Date Logic
    const now = new Date();
    const currentYear = now.getFullYear();
    const firstDay = new Date(now.getFullYear(), now.getMonth(), 1).toLocaleDateString('en-CA');
    const nextMonth = new Date(now.getFullYear(), now.getMonth() + 1, 1).toLocaleDateString('en-CA');

    // Yearly Range
    const startOfYear = `${currentYear}-01-01`;
    const endOfYear = `${currentYear + 1}-01-01`;

    console.log(`Fetching Budgets Data: ${firstDay} to ${nextMonth}`);

    // Parallel Fetch: Expenses (Monthly & Yearly) and Budgets
    const [resExpenses, resBudgets, resYearlyExpenses] = await Promise.all([
        supabase.from('expenses').select('*')
            .gte('date', firstDay)
            .lt('date', nextMonth),
        supabase.from('budgets').select('*').eq('month', monthStr),
        supabase.from('expenses').select('category, amount')
            .gte('date', startOfYear)
            .lt('date', endOfYear)
    ]);

    if (resExpenses.error) console.error("Expenses Error:", resExpenses.error);
    if (resBudgets.error) console.error("Budgets Error:", resBudgets.error);

    const expenses = resExpenses.data || [];
    const budgets = resBudgets.data || [];
    const yearlyExpenses = resYearlyExpenses.data || [];

    // Calculate actuals per category (Monthly)
    const actuals = {};
    if (expenses) {
        expenses.forEach(ex => {
            actuals[ex.category] = (actuals[ex.category] || 0) + parseFloat(ex.amount);
        });
    }

    // Calculate yearly totals per category
    const yearlyTotals = {};
    if (yearlyExpenses) {
        yearlyExpenses.forEach(ex => {
            yearlyTotals[ex.category] = (yearlyTotals[ex.category] || 0) + parseFloat(ex.amount);
        });
    }

    const grid = document.getElementById('budget-grid');
    grid.innerHTML = '';

    if (EXPENSE_CATEGORIES.length === 0) {
        grid.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--pk-text-muted);">
                <i data-lucide="tags" style="width: 48px; height: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                <p>No categories found.</p>
                <a href="categories.php" class="btn btn-primary" style="margin-top: 15px; display: inline-flex;">
                    <i data-lucide="plus"></i> Add Categories
                </a>
            </div>
        `;
        lucide.createIcons();
        return;
    }

    EXPENSE_CATEGORIES.forEach(cat => {
        const budgetItem = budgets ? budgets.find(b => b.category === cat) : null;
        const limit = budgetItem ? parseFloat(budgetItem.amount_limit) : 0;
        const spent = actuals[cat] || 0;
        const percent = limit > 0 ? (spent / limit) * 100 : 0;
        const yearTotal = yearlyTotals[cat] || 0;

        // Color Logic
        let barColor = 'var(--pk-primary)';
        let cardClass = 'card';
        let statusText = ''; // Default status text

        if (percent > 0 && limit > 0) statusText = percent.toFixed(0) + '%';

        if (percent > 80) barColor = 'var(--pk-accent)';
        if (percent > 100) {
            barColor = 'var(--pk-danger)';
            cardClass += ' card-danger';
            const overAmount = spent - limit;
            statusText = `<span style="display:flex; align-items:center; gap:4px; font-weight:600;"><i data-lucide="alert-circle" style="width:14px;"></i> Over by ${formatCurrency(overAmount)}</span>`;
        }

        const card = document.createElement('div');
        card.className = cardClass;
        card.innerHTML = `
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 10px;">
                <h3>${cat}</h3>
                <i data-lucide="settings-2" style="width:16px; cursor:pointer; color:var(--pk-text-muted);" 
                   onclick="openBudgetModal('${cat}', ${limit})"></i>
            </div>
            
            <div style="display:flex; justify-content:space-between; align-items:end; margin-bottom: 5px;">
                <span style="font-size: 1.5rem; font-weight:700;">${formatCurrency(spent)}</span>
                <span style="color: var(--pk-text-muted); font-size: 0.9rem;"> / ${formatCurrency(limit)}</span>
            </div>

            <div style="width: 100%; height: 8px; background: var(--pk-bg); border-radius: 4px; overflow: hidden; margin-bottom: 8px;">
                <div style="width: ${Math.min(percent, 100)}%; height: 100%; background: ${barColor}; transition: width 0.5s;"></div>
            </div>
            
            <div style="display:flex; justify-content:space-between; align-items:center; font-size: 0.8rem;">
                 <div style="color: var(--pk-text-muted);">
                    <i data-lucide="calendar-days" style="width:12px; vertical-align:middle;"></i> Year: ${formatCurrency(yearTotal)} / ${formatCurrency(limit * 12)}
                 </div>
                 <div style="color: ${percent > 100 ? 'var(--pk-danger)' : 'var(--pk-text-muted)'}; text-align: right;">
                    ${statusText}
                 </div>
            </div>
        `;
        grid.appendChild(card);
    });
    lucide.createIcons();
}

window.handleBudgetSubmit = async (e) => {
    e.preventDefault();
    const category = document.getElementById('budget-category-hidden').value;
    const limit = document.getElementById('budget-limit').value;
    const monthStr = new Date().toISOString().slice(0, 7);
    const user = (await supabase.auth.getUser()).data.user;

    // Check if exists
    const { data: existing } = await supabase.from('budgets').select('id').eq('category', category).eq('month', monthStr).single();

    let error;
    if (existing) {
        const { error: err } = await supabase.from('budgets').update({ amount_limit: limit }).eq('id', existing.id);
        error = err;
    } else {
        const { error: err } = await supabase.from('budgets').insert([{
            user_id: user.id,
            category,
            amount_limit: limit,
            month: monthStr
        }]);
        error = err;
    }

    if (error) alert('Error saving budget: ' + error.message);
    else {
        closeBudgetModal();
        loadBudgets(); // Refresh UI
    }
};


// --- Accounts Page Functions ---

async function loadAccountsPage() {
    const { data: accounts, error } = await supabase
        .from('accounts')
        .select('*')
        .order('name');

    if (error) {
        console.error(error);
        return;
    }

    const grid = document.getElementById('accounts-grid');
    if (accounts.length === 0) {
        grid.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--pk-text-muted);">
                <i data-lucide="wallet" style="width: 48px; height: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                <p>No accounts found.</p>
                <button class="btn btn-primary" onclick="openAccountModal()" style="margin-top: 15px; display: inline-flex;">
                    <i data-lucide="plus"></i> Add Account
                </button>
            </div>
        `;
        lucide.createIcons();
        return;
    }

    grid.innerHTML = accounts.map(acc => `
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom: 15px;">
                <div>
                    <h3 style="margin: 0;">${acc.name}</h3>
                    <span class="badge" style="margin-top: 5px; display:inline-block;">${acc.type}</span>
                </div>
                <div style="display:flex; gap: 8px;">
                    <i data-lucide="edit-2" style="width:16px; cursor:pointer;" onclick='openAccountModal(${JSON.stringify(acc)})'></i>
                    <i data-lucide="trash" style="width:16px; cursor:pointer; color: var(--pk-danger);" onclick="deleteAccount('${acc.id}')"></i>
                </div>
            </div>
            
            <div style="font-size: 1.8rem; font-weight:700;">${formatCurrency(acc.balance)}</div>
            <div style="color: var(--pk-text-muted); font-size: 0.8rem; margin-top: 5px;">Current Balance</div>
        </div>
    `).join('');

    lucide.createIcons();
}

window.openAccountModal = (account = null) => {
    const modal = document.getElementById('account-modal');
    modal.style.display = 'flex';

    if (account) {
        document.getElementById('acc-modal-title').innerText = 'Edit Account';
        document.getElementById('acc-id').value = account.id;
        document.getElementById('acc-name').value = account.name;
        document.getElementById('acc-type').value = account.type;
        document.getElementById('acc-balance').value = account.balance;
        // In a real app, maybe prevent editing balance directly if it messes with history, but here we allow it for corrections.
    } else {
        document.getElementById('acc-modal-title').innerText = 'Add Account';
        document.getElementById('account-form').reset();
        document.getElementById('acc-id').value = '';
    }
};

window.closeAccountModal = () => {
    document.getElementById('account-modal').style.display = 'none';
};

window.handleAccountSubmit = async (e) => {
    e.preventDefault();
    const id = document.getElementById('acc-id').value;
    const name = document.getElementById('acc-name').value;
    const type = document.getElementById('acc-type').value;
    const balance = document.getElementById('acc-balance').value;
    const user = (await supabase.auth.getUser()).data.user;

    const payload = {
        user_id: user.id,
        name,
        type,
        balance
    };

    let error;
    if (id) {
        const { error: err } = await supabase.from('accounts').update(payload).eq('id', id);
        error = err;
    } else {
        const { error: err } = await supabase.from('accounts').insert([payload]);
        error = err;
    }

    if (error) alert('Error saving account: ' + error.message);
    else {
        closeAccountModal();
        loadAccountsPage();
    }
};

window.deleteAccount = async (id) => {
    if (!confirm('Are you sure you want to delete this account? Expenses linked to it will remain but be unlinked.')) return;
    const { error } = await supabase.from('accounts').delete().eq('id', id);
    if (error) alert('Error deleting: ' + error.message);
    else loadAccountsPage();
};


// --- Updated Expenses Logic ---

// Fetch Accounts for Dropdown
async function fetchAccounts() {
    const { data, error } = await supabase.from('accounts').select('id, name').order('name');
    if (error) return [];
    return data;
}


// Actually, since openExpenseModal is defined in expenses.php script block, we can't easily override it from here without race conditions or modifying that file. 
// STRATEGY: We will modify `expenses.php` directly to call a helper function here, OR we just handle the data fetching here and expenses.php uses it.
// Let's attach a helper to window that expenses.php calls.

window.populateAccountSelect = async (selectId, selectedId = null) => {
    const accounts = await fetchAccounts();
    const select = document.getElementById(selectId);
    if (!select) return;

    select.innerHTML = accounts.length > 0 ?
        '<option value="">Select Account (Optional)</option>' + accounts.map(a => `<option value="${a.id}">${a.name}</option>`).join('') :
        '<option value="">No accounts found</option>';

    if (selectedId) select.value = selectedId;
};

// Update handleExpenseSubmit to include account_id
window.handleExpenseSubmit = async (e) => {
    e.preventDefault();
    const id = document.getElementById('expense-id').value;
    const amount = document.getElementById('amount').value;
    const category = document.getElementById('category').value;
    const date = document.getElementById('date').value;
    const merchant = document.getElementById('merchant').value;
    const notes = document.getElementById('notes').value;
    // New field
    const account_id = document.getElementById('account-id') ? document.getElementById('account-id').value : null;

    const user = (await supabase.auth.getUser()).data.user;

    const payload = {
        user_id: user.id,
        amount,
        category,
        date,
        merchant,
        notes,
        account_id: account_id || null // Handle empty string
    };

    let error;
    if (id) {
        const { error: err } = await supabase.from('expenses').update(payload).eq('id', id);
        error = err;
    } else {
        const { error: err } = await supabase.from('expenses').insert([payload]);
        error = err;
    }

    if (error) {
        alert('Error saving expense: ' + error.message);
    } else {
        if (typeof closeExpenseModal === 'function') closeExpenseModal();
        loadExpenses();
    }
};


// --- Categories Page Functions ---

async function loadCategoriesPage() {
    const { data: categories, error } = await supabase
        .from('categories')
        .select('*')
        .order('name');

    if (error) {
        console.error(error);
        return;
    }

    const tbody = document.getElementById('categories-list');
    if (!tbody) return;

    if (categories.length === 0) {
        tbody.innerHTML = '<tr><td colspan="2" style="text-align: center; padding: 30px;">No categories found. Add one!</td></tr>';
        return;
    }

    tbody.innerHTML = categories.map(cat => `
        <tr>
            <td>
                <div style="font-weight: 500;">${cat.name}</div>
            </td>
            <td style="text-align: right;">
                 <i data-lucide="trash" style="width:16px; cursor:pointer; color: var(--pk-danger);" onclick="deleteCategory('${cat.id}')"></i>
            </td>
        </tr>
    `).join('');

    lucide.createIcons();
}

window.handleCategorySubmit = async (e) => {
    e.preventDefault();
    const name = document.getElementById('category-name').value;
    const user = (await supabase.auth.getUser()).data.user;

    const { error } = await supabase.from('categories').insert([{
        user_id: user.id,
        name: name
    }]);

    if (error) {
        alert('Error saving category: ' + error.message);
    } else {
        if (typeof closeCategoryModal === 'function') closeCategoryModal();
        loadCategoriesPage();
        fetchCategories(); // Refresh global list
    }
};

window.deleteCategory = async (id) => {
    if (!confirm('Are you sure you want to delete this category? THIS WILL ALSO DELETE ALL LINKED EXPENSES AND BUDGETS!')) return;

    // 1. Get Category Name first
    const { data: catData, error: catFetchError } = await supabase
        .from('categories')
        .select('name')
        .eq('id', id)
        .single();

    if (catFetchError) {
        alert('Error fetching category details: ' + catFetchError.message);
        return;
    }

    const categoryName = catData.name;

    // 2. Delete linked Expenses
    const { error: expError } = await supabase
        .from('expenses')
        .delete()
        .eq('category', categoryName);

    if (expError) {
        alert('Error deleting linked expenses: ' + expError.message);
        return;
    }

    // 3. Delete linked Budgets
    const { error: budError } = await supabase
        .from('budgets')
        .delete()
        .eq('category', categoryName);

    if (budError) {
        alert('Error deleting linked budgets: ' + budError.message);
        return;
    }

    // 4. Finally delete the category
    const { error } = await supabase.from('categories').delete().eq('id', id);

    if (error) {
        alert('Error deleting category: ' + error.message);
    } else {
        loadCategoriesPage();
        fetchCategories(); // Refresh global list
    }
};

window.initializePage = async () => {
    const path = window.location.pathname;

    // Global User Info
    const { data: { user } } = await supabase.auth.getUser();
    const userInfoEl = document.getElementById('user-info');
    if (userInfoEl) userInfoEl.innerText = user.email;

    // Fetch Categories first
    await fetchCategories();

    if (path.includes('dashboard.php')) {
        loadDashboard();
    } else if (path.includes('expenses.php')) {
        loadExpenses();
    } else if (path.includes('budgets.php')) {
        loadBudgets();
    } else if (path.includes('categories.php')) {
        loadCategoriesPage();
    } else if (path.includes('accounts.php')) {
        loadAccountsPage();
    }
};

