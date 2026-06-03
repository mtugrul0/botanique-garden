<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Botanique Garden - Botanik Bahçesi Yönetim Sistemi. Bitkilerinizi kolayca yönetin ve takip edin.">

    <title>Botanique Garden - Botanik Bahçesi Yönetim Sistemi</title>


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* ===== CSS Değişkenleri (Botanik Teması) ===== */
        :root {
            --bg-primary: #2d6a4f;       /* Koyu yeşil - ana renk */
            --bg-accent: #95d5b2;         /* Açık yeşil - vurgu renk */
            --bg-cream: #f8f4e3;          /* Krem - arka plan */
            --bg-dark-green: #1b4332;     /* Çok koyu yeşil - footer/koyu alanlar */
            --bg-light-green: #d8f3dc;    /* Çok açık yeşil - hover/aktif durumlar */
            --text-dark: #1b4332;         /* Koyu metin rengi */
            --text-light: #f8f4e3;        /* Açık metin rengi */
            --font-heading: 'Playfair Display', serif;
            --font-body: 'Lato', sans-serif;
            --border-decorative: linear-gradient(90deg, #2d6a4f, #95d5b2, #52b788, #95d5b2, #2d6a4f);
        }

        /* ===== Genel Stiller ===== */
        body {
            font-family: var(--font-body);
            background-color: var(--bg-cream);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
        }

        /* ===== Navbar Stilleri ===== */
        .navbar-botanique {
            background-color: var(--bg-primary);
            padding: 0.75rem 0;
            position: relative;
        }

        /* Navbar altındaki dekoratif border */
        .navbar-botanique::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--border-decorative);
        }

        /* Logo stili */
        .navbar-brand-botanique {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-light) !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: opacity 0.3s ease;
        }

        .navbar-brand-botanique:hover {
            opacity: 0.85;
        }

        .navbar-brand-botanique .brand-icon {
            font-size: 1.75rem;
        }

        /* Navbar link stilleri */
        .navbar-botanique .nav-link {
            color: var(--bg-light-green) !important;
            font-weight: 400;
            transition: color 0.3s ease;
        }

        .navbar-botanique .nav-link:hover {
            color: #ffffff !important;
        }

        /* Kullanıcı karşılama metni */
        .navbar-welcome {
            color: var(--bg-accent);
            font-weight: 300;
            margin-right: 1rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .navbar-welcome .username {
            font-weight: 700;
            color: #ffffff;
        }

        /* Navbar butonları */
        .btn-navbar-logout {
            background-color: transparent;
            border: 1px solid var(--bg-accent);
            color: var(--bg-accent);
            font-size: 0.875rem;
            padding: 0.35rem 1rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-navbar-logout:hover {
            background-color: var(--bg-accent);
            color: var(--bg-dark-green);
        }

        .btn-navbar-register {
            background-color: var(--bg-accent);
            color: var(--bg-dark-green);
            font-weight: 700;
            font-size: 0.875rem;
            padding: 0.35rem 1rem;
            border-radius: 50px;
            border: 1px solid var(--bg-accent);
            transition: all 0.3s ease;
        }

        .btn-navbar-register:hover {
            background-color: #b7e4c7;
            border-color: #b7e4c7;
        }

        /* ===== İçerik Alanı ===== */
        main {
            flex: 1;
        }
    </style>
</head>
<body>


<nav class="navbar navbar-expand-lg navbar-botanique" id="main-navbar">
    <div class="container">


        <a class="navbar-brand navbar-brand-botanique" href="index.php" id="navbar-brand">
            <span class="brand-icon">🌿</span>
            Botanique
        </a>


        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                aria-controls="navbarContent" aria-expanded="false" aria-label="Menüyü aç/kapa"
                id="navbar-toggler">
            <span class="navbar-toggler-icon"></span>
        </button>


        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto align-items-center gap-2">

                <?php if (isset($_SESSION['user_id']) && isset($_SESSION['username'])): ?>



                    <li class="nav-item" id="nav-welcome">
                        <span class="navbar-welcome">
                            <i class="bi bi-person-circle"></i>
                            Hoş geldin, <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        </span>
                    </li>


                    <li class="nav-item" id="nav-logout">
                        <a class="btn btn-navbar-logout" href="logout.php" id="btn-logout">
                            <i class="bi bi-box-arrow-right me-1"></i>Çıkış
                        </a>
                    </li>

                <?php else: ?>



                    <li class="nav-item" id="nav-login">
                        <a class="nav-link" href="login.php" id="link-login">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Giriş
                        </a>
                    </li>


                    <li class="nav-item" id="nav-register">
                        <a class="btn btn-navbar-register" href="register.php" id="btn-register">
                            <i class="bi bi-person-plus me-1"></i>Kayıt Ol
                        </a>
                    </li>

                <?php endif; ?>

            </ul>
        </div>

    </div>
</nav>


<main class="container py-4">
