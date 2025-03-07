<?php 
    include ('../includes/header.php'); 
    include ('../views/user/login-register.php'); 
    include ('../views/search.php'); 
    include_once __DIR__ . "/../includes/config.php";

?>


<!-- Nút "Back to Top" -->
<button id="back-to-top" aria-label="Back to Top"
    class="fixed bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full shadow-lg hidden transition-transform transform hover:scale-110 focus:outline-none">
    <img src="<?= USER_URL ?>/assets/icons/chevron-up.svg" alt="Back to Top" class="h-6 w-6" />
</button>

<?php 
    include ('user/banner.php'); 
?>

<?php 
    include ('product/product-list.php'); 
?>

<?php 
    include ('product/product-list.php'); 
?>

<?php include ('../includes/footer.php'); ?>