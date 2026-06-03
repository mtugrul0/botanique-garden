<?php
// Oturum açma/kapama kontrolleri için session başlatılıyor
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

// Girilen bilgilerin listelenmesi (Read) - Sadece giriş yapan kullanıcının bitkileri çekiliyor
$plants = $db->fetchAll(
    'SELECT * FROM plants WHERE added_by = ? ORDER BY created_at DESC',
    [$_SESSION['user_id']]
);

require_once __DIR__ . '/includes/header.php';
?>


<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3" id="page-header">
    <div>
        <h1 class="mb-1" style="color: var(--text-dark); font-family: var(--font-heading); font-size: 2rem;">
            <i class="bi bi-flower1 me-2" style="color: var(--bg-primary);"></i>Bitki Koleksiyonum
        </h1>
        <p class="mb-0" style="color: #6c757d; font-size: 0.95rem;">
            Toplam <strong><?php echo count($plants); ?></strong> bitki kayıtlı
        </p>
    </div>
    <a href="add_plant.php" class="btn btn-success btn-lg rounded-3 fw-bold shadow-sm" id="btn-add-plant"
       style="background-color: var(--bg-primary); border-color: var(--bg-primary); transition: all 0.3s ease;"
       onmouseover="this.style.backgroundColor='#1b4332'; this.style.borderColor='#1b4332'"
       onmouseout="this.style.backgroundColor='var(--bg-primary)'; this.style.borderColor='var(--bg-primary)'">
        <i class="bi bi-plus-circle me-2"></i>Yeni Bitki Ekle
    </a>
</div>

<?php if (empty($plants)): ?>

    <div class="text-center py-5" id="empty-state">
        <div class="card shadow-sm border-0 rounded-4 mx-auto" style="max-width: 500px; background-color: #ffffff;">
            <div class="card-body p-5">
                <div class="mb-4" style="font-size: 4rem;">🌱</div>
                <h3 class="mb-3" style="font-family: var(--font-heading); color: var(--text-dark);">
                    Henüz bitki eklemediniz
                </h3>
                <p class="mb-4" style="color: #6c757d; font-size: 1rem;">
                    Bahçenizi dijital olarak yönetmeye başlayın!<br>
                    İlk bitkinizi ekleyerek koleksiyonunuzu oluşturun.
                </p>
                <a href="add_plant.php" class="btn rounded-pill px-4 py-2 fw-bold" id="btn-add-first-plant"
                   style="background-color: var(--bg-primary); color: var(--text-light); border: none; transition: all 0.3s ease;"
                   onmouseover="this.style.backgroundColor='#1b4332'"
                   onmouseout="this.style.backgroundColor='var(--bg-primary)'">
                    <i class="bi bi-plus-circle me-2"></i>İlk Bitkini Ekle
                </a>
            </div>
        </div>
    </div>

<?php else: ?>

    <div class="row g-4" id="plants-grid">

        <!-- Girilen bilgilerin listelenmesi (Read) için döngü başlatılıyor -->
        <?php foreach ($plants as $plant): ?>
            <div class="col-12 col-md-6 col-lg-4">

                <div class="card h-100 shadow-sm border-0 rounded-4 plant-card" id="plant-card-<?php echo $plant['id']; ?>"
                     style="background-color: #ffffff; transition: transform 0.3s ease, box-shadow 0.3s ease; overflow: hidden;">


                    <div style="height: 4px; background: linear-gradient(90deg, var(--bg-primary), var(--bg-accent));"></div>

                    <div class="card-body p-4 d-flex flex-column">


                        <h5 class="card-title mb-1" style="font-family: var(--font-heading); color: var(--text-dark); font-size: 1.25rem;">
                            <i class="bi bi-flower2 me-2" style="color: var(--bg-primary);"></i><?php echo htmlspecialchars($plant['name']); ?>
                        </h5>


                        <p class="card-subtitle mb-3" style="color: var(--bg-primary); font-style: italic; font-size: 0.9rem;">
                            <?php echo htmlspecialchars($plant['species']); ?>
                        </p>

                        <hr class="my-2" style="border-color: var(--bg-light-green); opacity: 0.5;">


                        <div class="d-flex flex-column gap-2 mt-2 mb-3 flex-grow-1">


                            <div class="d-flex align-items-center" style="font-size: 0.9rem; color: #495057;">
                                <i class="bi bi-geo-alt-fill me-2" style="color: var(--bg-primary); font-size: 1rem;"></i>
                                <span><?php echo htmlspecialchars($plant['location']); ?></span>
                            </div>


                            <div class="d-flex align-items-center" style="font-size: 0.9rem; color: #495057;">
                                <i class="bi bi-droplet-fill me-2" style="color: #0077b6; font-size: 1rem;"></i>
                                <span>Her <strong><?php echo htmlspecialchars($plant['water_frequency']); ?></strong> günde bir sulanır</span>
                            </div>


                            <div class="d-flex align-items-center" style="font-size: 0.9rem; color: #495057;">
                                <i class="bi bi-calendar3 me-2" style="color: #e76f51; font-size: 1rem;"></i>
                                <span>Son sulama:
                                    <strong>
                                        <?php
                                        if (!empty($plant['last_watered'])) {
                                            echo date('d.m.Y', strtotime($plant['last_watered']));
                                        } else {
                                            echo 'Henüz sulanmadı';
                                        }
                                        ?>
                                    </strong>
                                </span>
                            </div>

                        </div>


                        <div class="d-flex gap-2 mt-auto pt-3" style="border-top: 1px solid #e9ecef;">

                            <a href="edit_plant.php?id=<?php echo $plant['id']; ?>"
                               class="btn btn-outline-warning rounded-3 flex-fill fw-bold"
                               id="btn-edit-<?php echo $plant['id']; ?>"
                               style="transition: all 0.3s ease;">
                                <i class="bi bi-pencil-square me-1"></i>Düzenle
                            </a>


                            <button type="button"
                                    class="btn btn-outline-danger rounded-3 flex-fill fw-bold"
                                    id="btn-delete-<?php echo $plant['id']; ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModal-<?php echo $plant['id']; ?>"
                                    style="transition: all 0.3s ease;">
                                <!-- Bilgi silme (Delete) işlemini tetikleyecek buton -->
                                <i class="bi bi-trash3 me-1"></i>Sil
                            </button>
                        </div>

                    </div>
                </div>


                <div class="modal fade" id="deleteModal-<?php echo $plant['id']; ?>" tabindex="-1"
                     aria-labelledby="deleteModalLabel-<?php echo $plant['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow">


                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title" id="deleteModalLabel-<?php echo $plant['id']; ?>"
                                    style="font-family: var(--font-heading); color: var(--text-dark);">
                                    <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>Bitkiyi Sil
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                            </div>


                            <div class="modal-body py-4">
                                <p class="mb-0" style="color: #495057; font-size: 1rem;">
                                    <strong>"<?php echo htmlspecialchars($plant['name']); ?>"</strong>
                                    bitkisini silmek istediğinizden emin misiniz?
                                </p>
                                <p class="mb-0 mt-2" style="color: #6c757d; font-size: 0.875rem;">
                                    Bu işlem geri alınamaz.
                                </p>
                            </div>


                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">
                                    <i class="bi bi-x-lg me-1"></i>Vazgeç
                                </button>
                                <a href="delete_plant.php?id=<?php echo $plant['id']; ?>"
                                   class="btn btn-danger rounded-3 fw-bold"
                                   id="btn-confirm-delete-<?php echo $plant['id']; ?>">
                                    <i class="bi bi-trash3 me-1"></i>Evet, Sil
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>

    </div>
<?php endif; ?>


<style>
    .plant-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.75rem 1.5rem rgba(45, 106, 79, 0.15) !important;
    }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
