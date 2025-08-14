<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLogin()
{
    if (!isset($_SESSION['login'])) {
        header('Location: ../index.php');
        exit();
    }
}
