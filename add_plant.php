<?php
// Kullanıcı tarafından bilgi girişi (Create) işlemleri için session başlatılıyor
session_start();
ob_start();

require_once __DIR__ . '/classes/Database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$errors = [];

$form_data = [
    'name'            => '',
    'species'         => '',
    'location'        => '',
    'water_frequency' => '',
    'last_watered'    => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Kullanıcı tarafından bilgi girişi (Create) form verileri alınıyor
    $name            = trim($_POST['name'] ?? '');
    $species         = trim($_POST['species'] ?? '');
    $location        = trim($_POST['location'] ?? '');
    $water_frequency = trim($_POST['water_frequency'] ?? '');
    $last_watered    = trim($_POST['last_watered'] ?? '');

    $form_data = compact('name', 'species', 'location', 'water_frequency', 'last_watered');

    if (empty($name)) {
        $errors[] = 'Bitki adı boş bırakılamaz.';
    }

    if (empty($species)) {
        $errors[] = 'Tür bilgisi boş bırakılamaz.';
    }

    if (empty($location)) {
        $errors[] = 'Konum boş bırakılamaz.';
    }

    if (empty($water_frequency)) {
        $errors[] = 'Sulama sıklığı boş bırakılamaz.';
    }

    if (empty($last_watered)) {
        $errors[] = 'Son sulama tarihi boş bırakılamaz.';
    }

    if (!empty($water_frequency) && (!ctype_digit($water_frequency) || intval($water_frequency) <= 0)) {
        $errors[] = 'Sulama sıklığı pozitif bir tam sayı olmalıdır.';
    }

    if (!empty($last_watered)) {
        $date_parts = explode('-', $last_watered);
        if (count($date_parts) !== 3 || !checkdate(intval($date_parts[1]), intval($date_parts[2]), intval($date_parts[0]))) {
            $errors[] = 'Geçerli bir tarih girin.';
        }
    }

    if (empty($errors)) {

        $db = Database::getInstance();

        // Veritabanına yeni bitki verilerini ekleme sorgusu (Create)
        $db->execute(
            'INSERT INTO plants (name, species, location, water_frequency, last_watered, added_by, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())',
            [$name, $species, $location, intval($water_frequency), $last_watered, $_SESSION['user_id']]
        );

        header('Location: index.php');
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>


<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">


        <a href="index.php" class="btn btn-sm rounded-pill mb-3 px-3" id="btn-back-to-list"
           style="color: var(--bg-primary); border: 1px solid var(--bg-primary); transition: all 0.3s ease;"
           onmouseover="this.style.backgroundColor='var(--bg-primary)'; this.style.color='#fff'"
           onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--bg-primary)'">
            <i class="bi bi-arrow-left me-1"></i>Koleksiyonuma Dön
        </a>


        <div class="card shadow border-0 rounded-4" id="add-plant-card" style="background-color: #ffffff;">


            <div class="card-header text-center border-0 rounded-top-4 py-4" style="background-color: var(--bg-primary);">
                <h2 class="mb-0" style="color: var(--text-light); font-family: var(--font-heading);">
                    <i class="bi bi-plus-circle me-2"></i>Yeni Bitki Ekle
                </h2>
                <p class="mb-0 mt-2" style="color: var(--bg-accent); font-size: 0.9rem;">
                    Koleksiyonunuza yeni bir bitki ekleyin
                </p>
            </div>

            <div class="card-body p-4">


                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert" id="add-plant-errors">
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


                <form method="POST" action="add_plant.php" id="add-plant-form" novalidate>


                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold" style="color: var(--text-dark);">
                            <i class="bi bi-flower2 me-1" style="color: var(--bg-primary);"></i>Bitki Adı
                        </label>
                        <input type="text" class="form-control rounded-3" id="name" name="name"
                               placeholder="Örn: Monstera Deliciosa"
                               value="<?php echo htmlspecialchars($form_data['name']); ?>"
                               required>
                    </div>


                    <div class="mb-3">
                        <label for="species" class="form-label fw-bold" style="color: var(--text-dark);">
                            <i class="bi bi-tag me-1" style="color: var(--bg-primary);"></i>Tür
                        </label>
                        <input type="text" class="form-control rounded-3" id="species" name="species"
                               placeholder="Örn: Araceae"
                               value="<?php echo htmlspecialchars($form_data['species']); ?>"
                               required>
                    </div>


                    <div class="mb-3">
                        <label for="location" class="form-label fw-bold" style="color: var(--text-dark);">
                            <i class="bi bi-geo-alt me-1" style="color: var(--bg-primary);"></i>Konum
                        </label>
                        <input type="text" class="form-control rounded-3" id="location" name="location"
                               placeholder="Örn: Salon, pencere kenarı"
                               value="<?php echo htmlspecialchars($form_data['location']); ?>"
                               required>
                    </div>


                    <div class="row g-3 mb-4">

                        <div class="col-md-6">
                            <label for="water_frequency" class="form-label fw-bold" style="color: var(--text-dark);">
                                <i class="bi bi-droplet me-1" style="color: #0077b6;"></i>Sulama Sıklığı (gün)
                            </label>
                            <input type="number" class="form-control rounded-3" id="water_frequency" name="water_frequency"
                                   placeholder="Örn: 3"
                                   min="1"
                                   value="<?php echo htmlspecialchars($form_data['water_frequency']); ?>"
                                   required>
                        </div>


                        <div class="col-md-6">
                            <label for="last_watered" class="form-label fw-bold" style="color: var(--text-dark);">
                                <i class="bi bi-calendar3 me-1" style="color: #e76f51;"></i>Son Sulama Tarihi
                            </label>
                            <input type="date" class="form-control rounded-3" id="last_watered" name="last_watered"
                                   value="<?php echo htmlspecialchars($form_data['last_watered']); ?>"
                                   required>
                        </div>
                    </div>


                    <div class="d-flex gap-3">

                        <button type="submit" class="btn btn-lg rounded-3 fw-bold flex-fill" id="btn-save-plant"
                                style="background-color: var(--bg-primary); color: var(--text-light); border: none; transition: all 0.3s ease;"
                                onmouseover="this.style.backgroundColor='#1b4332'"
                                onmouseout="this.style.backgroundColor='var(--bg-primary)'">
                            <i class="bi bi-check-circle me-2"></i>Kaydet
                        </button>


                        <a href="index.php" class="btn btn-lg btn-outline-secondary rounded-3 fw-bold flex-fill" id="btn-cancel-add">
                            <i class="bi bi-x-circle me-2"></i>İptal
                        </a>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
