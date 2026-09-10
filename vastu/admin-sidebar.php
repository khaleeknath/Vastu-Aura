<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside class="admin-sidebar">

    <a class="logo-mark" href="index.php">
        VastuAura
    </a>

    <nav>

        <a href="admin-orders.php"
           class="<?= $currentPage === 'admin-orders.php' ? 'active' : '' ?>">
            Orders
        </a>

        <a href="admin-appointments.php"
           class="<?= $currentPage === 'admin-appointments.php' ? 'active' : '' ?>">
            Appointments
        </a>

        <a href="admin-products.php"
           class="<?= $currentPage === 'admin-products.php' ? 'active' : '' ?>">
            Inventory
        </a>

        

        <a href="#" id="sidebarLogout">
            Logout
        </a>

    </nav>

</aside>
