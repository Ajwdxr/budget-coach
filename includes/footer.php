<?php if (basename($_SERVER['PHP_SELF']) !== 'index.php'): ?>
    </div> <!-- End .main-content -->
    </div> <!-- End .app-layout -->
<?php endif; ?>


<!-- Toast Container -->
<div id="toast-container" class="toast-container"></div>

<!-- Footer -->
<footer class="app-footer">
    <div class="footer-content">
        <a href="https://github.com/Ajwdxr/budget-coach" target="_blank" rel="noopener noreferrer" class="github-link" title="GitHub">
            <i data-lucide="github"></i>
        </a>
        <span class="footer-credit">Created by <a href="https://ajwdxr.free.nf/" target="_blank" rel="noopener noreferrer">Ajwdxr</a></span>
    </div>
</footer>

<!-- App Logic -->
<script src="js/app.js" type="module"></script>
<script>
    // Initialize Icons
    lucide.createIcons();
</script>
</body>

</html>