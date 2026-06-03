<?php
// Bilgi güncelleme (Update) işlemleri için session başlatılıyor
session_start();
ob_start();

require_once __DIR__ . '/classes/Database.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

$db = Database::getInstance();

$errors = [];

$plant_id = intval($_GET['id'] ?? 0);

if ($plant_id <= 0) {
    header('Location: index.php');
    exit;
}

// Güncellenecek bitkinin mevcut bilgilerini veritabanından çek (Read işlemi)
$plant = $db->fetchOne(
    'SELECT * FROM plants WHERE id = ?',
    [$plant_id]
);

if (!$plant) {
    header('Location: index.php');
    exit;
}

// Sahiplik kontrolü
if ($plant['added_by'] != $_SESSION['user_id']) {
    header('Location: index.php');
    exit;
}

$form_data = [
    'name'            => $plant['name'],
    'species'         => $plant['species'],
    'location'        => $plant['location'],
    'water_frequency' => $plant['water_frequency'],
    'last_watered'    => $plant['last_watered'],
];

// Bilgi güncelleme (Update) formu gönderildiğinde çalışacak kontroller
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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

        // Veritabanındaki bitki bilgilerini güncelle (Bilgi güncelleme - Update işlemi)
        $db->execute(
            'UPDATE plants SET name = ?, species = ?, location = ?, water_frequency = ?, last_watered = ? WHERE id = ? AND added_by = ?',
            [$name, $species, $location, intval($water_frequency), $last_watered, $plant_id, $_SESSION['user_id']]
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


        <div class="card shadow border-0 rounded-4" id="edit-plant-card" style="background-color: #ffffff;">


            <div class="card-header text-center border-0 rounded-top-4 py-4" style="background-color: var(--bg-primary);">
                <h2 class="mb-0" style="color: var(--text-light); font-family: var(--font-heading);">
                    <i class="bi bi-pencil-square me-2"></i>Bitkiyi Düzenle
                </h2>
                <p class="mb-0 mt-2" style="color: var(--bg-accent); font-size: 0.9rem;">
                    "<?php echo htmlspecialchars($plant['name']); ?>" bilgilerini güncelleyin
                </p>
            </div>

            <div class="card-body p-4">


                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert" id="edit-plant-errors">
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


                <form method="POST" action="edit_plant.php?id=<?php echo $plant_id; ?>" id="edit-plant-form" novalidate>


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

                        <button type="submit" class="btn btn-lg rounded-3 fw-bold flex-fill" id="btn-update-plant"
                                style="background-color: var(--bg-primary); color: var(--text-light); border: none; transition: all 0.3s ease;"
                                onmouseover="this.style.backgroundColor='#1b4332'"
                                onmouseout="this.style.backgroundColor='var(--bg-primary)'">
                            <i class="bi bi-check-circle me-2"></i>Güncelle
                        </button>


                        <a href="index.php" class="btn btn-lg btn-outline-secondary rounded-3 fw-bold flex-fill" id="btn-cancel-edit">
                            <i class="bi bi-x-circle me-2"></i>İptal
                        </a>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
