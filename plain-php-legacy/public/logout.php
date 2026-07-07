<?php
require_once __DIR__ . '/../inc/functions.php';
$_SESSION = array();
session_destroy();
header('Location: login.php');
exit;
