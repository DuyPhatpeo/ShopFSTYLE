<?php
session_start();
session_unset();
session_destroy();
header("Location: login.php?msg=" . urlencode("Bạn đã đăng xuất!") . "&type=success");
exit;