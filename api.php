<?php
header('Content-Type: application/json; charset=utf-8');

$db_file = 'podcast.db';

try {
    $conn = new PDO("sqlite:" . $db_file);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT name, duration, description, file_path, playable FROM episodes ORDER BY episode_id ASC";
    $stmt = $conn->query($sql);
    
    // Verileri diziye aktar
    $bolumler = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $bolumler
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed: ' . $e->getMessage()
    ]);
}
?>