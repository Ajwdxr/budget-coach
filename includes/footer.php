<?php if (basename($_SERVER['PHP_SELF']) !== 'index.php'): ?>
    </div> <!-- End .main-content -->
    </div> <!-- End .app-layout -->
<?php endif; ?>


<!-- Toast Container -->
<div id="toast-container" class="toast-container"></div>

<!-- App Logic -->
<script src="js/app.js" type="module"></script>
<script>
    // Initialize Icons
    lucide.createIcons();
</script>
</body>

</html>