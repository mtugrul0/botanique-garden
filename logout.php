<?php
// Oturum kapatma işlemleri başlatılıyor
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tüm session (oturum) değişkenlerini temizle (Oturum kapama)
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Oturumu tamamen sonlandır (Oturum kapatma işlemi)
session_destroy();

header('Location: login.php');
exit;
