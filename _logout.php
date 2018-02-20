<?php
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['admin'])) {
    $userType = $_SESSION['admin'] == 0 ? "User" : "Administrator";
    echo "Hello " .   htmlentities($_SESSION['username']) . "!  ($userType)";
    ?>
    <div id="logout-button">
        <a href="logout.php">
            <button type="button" name="Logout" id="Logout">Logout</button>
        </a>
    </div>

    <?php
}
?>