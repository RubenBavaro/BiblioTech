<?php

$current = $_COOKIE['tema'] ?? 'light';
$new = ($current === 'dark') ? 'light' : 'dark';

setcookie("tema", $new, [
    'expires' => time() + (86400 * 30),
    'path' => '/',
    'samesite' => 'Lax'
]);

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
