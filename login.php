<?php
// Oturum açma işlemleri için session başlatılıyor
session_start();
ob_start();

require_once __DIR__ . '/classes/Database.php';

$errors = [];

$success = '';
if (isset($_SESSION['register_success'])) {
    $success = $_SESSION['register_success'];
    unset($_SESSION['register_success']);
}

$form_username = '';

// Form gönderildiğinde çalışacak oturum açma (Login) kontrolleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $form_username = $username;

    if (empty($username)) {
        $errors[] = 'Kullanıcı adı boş bırakılamaz.';
    }

    if (empty($password)) {
        $errors[] = 'Şifre boş bırakılamaz.';
    }

    if (empty($errors)) {

        $db = Database::getInstance();

        // Veritabanından kullanıcı bilgilerini çekme (Read işlemi)
        $user = $db->fetchOne(
            'SELECT id, username, password FROM users WHERE username = ?',
            [$username]
        );

        if ($user && password_verify($password, $user['password'])) {

            // Oturum açma başarılı, kullanıcı bilgilerini session'a kaydet
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];

            header('Location: index.php');
            exit;

        } else {
            $errors[] = 'Kullanıcı adı veya şifre hatalı.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>


<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">


        <div class="card shadow border-0 rounded-4" id="login-card" style="background-color: #ffffff;">


            <div class="card-header text-center border-0 rounded-top-4 py-4" style="background-color: var(--bg-primary);">
                <h2 class="mb-0" style="color: var(--text-light); font-family: var(--font-heading);">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Giriş Yap
                </h2>
                <p class="mb-0 mt-2" style="color: var(--bg-accent); font-size: 0.9rem;">
                    Botanique Garden'a hoş geldiniz
                </p>
            </div>

            <div class="card-body p-4">


                <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert" id="login-success">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                    </div>
                <?php endif; ?>


                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert" id="login-errors">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Hata!</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                    </div>
                <?php endif; ?>


                <form method="POST" action="login.php" id="login-form" novalidate>


                    <div class="mb-3">
                        <label for="username" class="form-label fw-bold" style="color: var(--text-dark);">
                            <i class="bi bi-person me-1"></i>Kullanıcı Adı
                        </label>
                        <input type="text" class="form-control rounded-3" id="username" name="username"
                               placeholder="Kullanıcı adınızı girin"
                               value="<?php echo htmlspecialchars($form_username); ?>"
                               required>
                    </div>


                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold" style="color: var(--text-dark);">
                            <i class="bi bi-lock me-1"></i>Şifre
                        </label>
                        <input type="password" class="form-control rounded-3" id="password" name="password"
                               placeholder="Şifrenizi girin"
                               required>
                    </div>


                    <div class="d-grid">
                        <button type="submit" class="btn btn-lg rounded-3 fw-bold" id="btn-login-submit"
                                style="background-color: var(--bg-primary); color: var(--text-light); border: none; transition: all 0.3s ease;"
                                onmouseover="this.style.backgroundColor='#1b4332'"
                                onmouseout="this.style.backgroundColor='var(--bg-primary)'">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Giriş Yap
                        </button>
                    </div>

                </form>


                <div class="text-center mt-4 pt-3" style="border-top: 1px solid #e9ecef;">
                    <p class="mb-0" style="color: #6c757d;">
                        Hesabın yok mu?
                        <a href="register.php" class="fw-bold text-decoration-none" id="link-to-register"
                           style="color: var(--bg-primary);">
                            Kayıt Ol
                        </a>
                    </p>
                </div>

            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
