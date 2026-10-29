<?php
require __DIR__ . '/inc/config.php';
$_SESSION = [];
session_destroy();
header('Location: index.php');
