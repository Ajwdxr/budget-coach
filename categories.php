<?php include 'includes/header.php'; ?>

<main>
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2><i data-lucide="tags" style="vertical-align: middle; margin-right: 8px;"></i> Categories</h2>
        <button class="btn btn-primary btn-sm" onclick="openCategoryModal()">
            <i data-lucide="plus"></i> Add Category
        </button>
    </div>

    <div class="card">
        <table class="expenses-table">
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th style="width: 50px;"></th>
                </tr>
            </thead>
            <tbody id="categories-list">
                <!-- JS will populate -->
                <tr>
                    <td colspan="2" style="text-align: center; color: var(--pk-text-muted);">Loading categories...</td>
                </tr>
            </tbody>
        </table>
    </div>
</main>

<!-- Add Category Modal -->
<div class="modal-overlay" id="category-modal">
    <div class="modal">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3>Add Category</h3>
            <i data-lucide="x" style="cursor: pointer;" onclick="closeCategoryModal()"></i>
        </div>

        <form id="category-form" onsubmit="handleCategorySubmit(event)">
            <div class="form-group">
                <label>Category Name</label>
                <input type="text" id="category-name" class="form-control" placeholder="e.g. Gym, Rent, Hobbies" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Category</button>
        </form>
    </div>
</div>

<script>
    const categoryModal = document.getElementById('category-modal');

    function openCategoryModal() {
        document.getElementById('category-form').reset();
        categoryModal.style.display = 'flex';
        document.getElementById('category-name').focus();
    }

    function closeCategoryModal() {
        categoryModal.style.display = 'none';
    }

    categoryModal.addEventListener('click', (e) => {
        if (e.target === categoryModal) closeCategoryModal();
    });
</script>

<?php include 'includes/footer.php'; ?>