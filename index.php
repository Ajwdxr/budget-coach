<?php include 'includes/header.php'; ?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div style="display: flex; justify-content: center; margin-bottom: 20px;">
            <i data-lucide="wallet" size="48" style="color: var(--pk-primary);"></i>
        </div>
        <h1 class="auth-title">Budget Coach</h1>
        <p class="auth-subtitle">Track every RM. Master your wealth.</p>

        <button class="btn btn-outline" onclick="signInWithProvider('google')" style="margin-bottom: 12px;">
            <i data-lucide="chrome"></i> Continue with Google
        </button>

        <button class="btn btn-outline" onclick="signInWithProvider('github')">
            <i data-lucide="github"></i> Continue with GitHub
        </button>
    </div>
</div>

<!-- App Logic -->
<script src="js/app.js" type="module"></script>
<script>
    lucide.createIcons();
</script>
</body>
</html>
