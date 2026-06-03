<?php
require_once __DIR__ . '/../config/db.php';

// Veritabanı bağlantısı ve CRUD (Create, Read, Update, Delete) işlemleri için Singleton sınıfı
class Database
{
    private static ?Database $instance = null;
    private ?PDO $connection = null;

    // Singleton: dışarıdan new ile oluşturulamaz
    private function __construct()
    {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);

        } catch (PDOException $e) {
            die('Veritabanı bağlantı hatası: ' . $e->getMessage());
        }
    }

    private function __clone() {}

    public function __wakeup()
    {
        throw new \Exception('Singleton sınıfı deserialize edilemez.');
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function query(string $sql, array $params = []): PDOStatement
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            die('Sorgu hatası: ' . $e->getMessage());
        }
    }

    // Veritabanından birden fazla satır okuma (Read) işlemi yapar
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    // Veritabanından tek bir satır okuma (Read) işlemi yapar
    public function fetchOne(string $sql, array $params = []): array|false
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    // Veritabanına veri ekleme (Create), güncelleme (Update) veya silme (Delete) işlemi yapar
    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
}
