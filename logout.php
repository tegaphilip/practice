<?php
session_start();
unset($_SESSION['is_logged_in']);
unset($_SESSION['username']);
unset($_SESSION['user_id']);
unset($_SESSION['admin']);
header("Location:index.php?logged_out=true");