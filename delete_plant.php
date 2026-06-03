<?php
session_start();
ob_start();

require_once __DIR__ . '/classes/Database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$plant_id = intval($_GET['id'] ?? 0);

if ($plant_id <= 0) {
    header('Location: index.php');
    exit;
}

$db = Database::getInstance();

$plant = $db->fetchOne(
    'SELECT id, added_by FROM plants WHERE id = ?',
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

$db->execute(
    'DELETE FROM plants WHERE id = ? AND added_by = ?',
    [$plant_id, $_SESSION['user_id']]
);

header('Location: index.php');
exit;
