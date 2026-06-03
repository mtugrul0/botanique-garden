<?php
session_start();
ob_start();

require_once __DIR__ . '/classes/Database.php';

$errors = [];
$success = '';

$form_data = [
    'username' => '',
    'email'    => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username         = trim($_POST['username'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    $form_data['username'] = $username;
    $form_data['email']    = $email;

    if (empty($username)) {
        $errors[] = 'Kullanıcı adı boş bırakılamaz.';
    }

    if (empty($email)) {
        $errors[] = 'E-posta adresi boş bırakılamaz.';
    }

    if (empty($password)) {
        $errors[] = 'Şifre boş bırakılamaz.';
    }

    if (empty($password_confirm)) {
        $errors[] = 'Şifre tekrar alanı boş bırakılamaz.';
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Geçerli bir e-posta adresi girin.';
    }

    if (!empty($password) && !empty($password_confirm) && $password !== $password_confirm) {
        $errors[] = 'Şifreler eşleşmiyor.';
    }

    if (!empty($password) && strlen($password) < 6) {
        $errors[] = 'Şifre en az 6 karakter olmalıdır.';
    }

    if (empty($errors)) {

        $db = Database::getInstance();

        $existing_user = $db->fetchOne(
            'SELECT id FROM users WHERE username = ?',
            [$username]
        );
        if ($existing_user) {
            $errors[] = 'Bu kullanıcı adı zaten kullanılıyor.';
        }

        $existing_email = $db->fetchOne(
            'SELECT id FROM users WHERE email = ?',
            [$email]
        );
        if ($existing_email) {
            $errors[] = 'Bu e-posta adresi zaten kayıtlı.';
        }
    }

    if (empty($errors)) {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $db->execute(
            'INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())',
            [$username, $email, $hashed_password]
        );

        $_SESSION['register_success'] = 'Kayıt başarılı! Şimdi giriş yapabilirsiniz.';
        header('Location: login.php');
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>


<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">


        <div class="card shadow border-0 rounded-4" id="register-card" style="background-color: #ffffff;">


            <div class="card-header text-center border-0 rounded-top-4 py-4" style="background-color: var(--bg-primary);">
                <h2 class="mb-0" style="color: var(--text-light); font-family: var(--font-heading);">
                    <i class="bi bi-person-plus me-2"></i>Kayıt Ol
                </h2>
                <p class="mb-0 mt-2" style="color: var(--bg-accent); font-size: 0.9rem;">
                    Botanique Garden'a katılın
                </p>
            </div>

            <div class="card-body p-4">


                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert" id="register-errors">
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


                <form method="POST" action="register.php" id="register-form" novalidate>


                    <div class="mb-3">
                        <label for="username" class="form-label fw-bold" style="color: var(--text-dark);">
                            <i class="bi bi-person me-1"></i>Kullanıcı Adı
                        </label>
                        <input type="text" class="form-control rounded-3" id="username" name="username"
                               placeholder="Kullanıcı adınızı girin"
                               value="<?php echo htmlspecialchars($form_data['username']); ?>"
                               required>
                    </div>


                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold" style="color: var(--text-dark);">
                            <i class="bi bi-envelope me-1"></i>E-posta
                        </label>
                        <input type="email" class="form-control rounded-3" id="email" name="email"
                               placeholder="ornek@email.com"
                               value="<?php echo htmlspecialchars($form_data['email']); ?>"
                               required>
                    </div>


                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold" style="color: var(--text-dark);">
                            <i class="bi bi-lock me-1"></i>Şifre
                        </label>
                        <input type="password" class="form-control rounded-3" id="password" name="password"
                               placeholder="En az 6 karakter"
                               required>
                    </div>


                    <div class="mb-4">
                        <label for="password_confirm" class="form-label fw-bold" style="color: var(--text-dark);">
                            <i class="bi bi-lock-fill me-1"></i>Şifre Tekrar
                        </label>
                        <input type="password" class="form-control rounded-3" id="password_confirm" name="password_confirm"
                               placeholder="Şifrenizi tekrar girin"
                               required>
                    </div>


                    <div class="d-grid">
                        <button type="submit" class="btn btn-lg rounded-3 fw-bold" id="btn-register-submit"
                                style="background-color: var(--bg-primary); color: var(--text-light); border: none; transition: all 0.3s ease;"
                                onmouseover="this.style.backgroundColor='#1b4332'"
                                onmouseout="this.style.backgroundColor='var(--bg-primary)'">
                            <i class="bi bi-person-plus me-2"></i>Kayıt Ol
                        </button>
                    </div>

                </form>


                <div class="text-center mt-4 pt-3" style="border-top: 1px solid #e9ecef;">
                    <p class="mb-0" style="color: #6c757d;">
                        Zaten hesabın var mı?
                        <a href="login.php" class="fw-bold text-decoration-none" id="link-to-login"
                           style="color: var(--bg-primary);">
                            Giriş Yap
                        </a>
                    </p>
                </div>

            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
