<?php
// src/auth/logout.php
session_start();
session_unset();
session_destroy();

// Redirige al login
header('Location: ../../public/login.php');
exit;
