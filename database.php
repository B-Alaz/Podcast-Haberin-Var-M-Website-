<?php
// Hataları ekrana bas ki bir sorun olursa hemen görelim
error_reporting(E_ALL);
ini_set('display_errors', 1);

// SQLite veritabanı dosya yolu (Aynı klasörde oluşur)
$db_file = 'podcast.db';

try {
    // 1. SQLite Bağlantısı
    $conn = new PDO("sqlite:" . $db_file);
    // Hata modunu aktifleştir
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<span style='color: #4CAF50; font-weight: bold;'>✔ SQLite Bağlantısı Başarılı.</span><br>";

    // 2. Eski Tabloyu Temizle (Varsa siler ki sıfırdan temiz kurulum olsun)
    $conn->exec("DROP TABLE IF EXISTS episodes");
    echo "<span style='color: orange;'>- Eski tablo temizlendi.</span><br>";

    // 3. Tabloyu Oluştur (SQLite formatında)
    // NOT: AUTOINCREMENT ve veri tipleri SQLite'a özeldir.
    $create_table_sql = "
    CREATE TABLE episodes (
      episode_id INTEGER PRIMARY KEY AUTOINCREMENT,
      name TEXT NOT NULL,
      duration TEXT NOT NULL,
      description TEXT NOT NULL,
      file_path TEXT DEFAULT NULL,
      playable INTEGER NOT NULL DEFAULT 1,
      date DATETIME DEFAULT CURRENT_TIMESTAMP
    );";

    $conn->exec($create_table_sql);
    echo "<span style='color: green; font-weight: bold;'>✔ 'episodes' tablosu başarıyla oluşturuldu.</span><br>";

    // 4. Verileri Ekle
    // MySQL'den farklı olarak tek tırnak içindeki tek tırnakları (\') kaçış karakteriyle düzelttik.
    $insert_data_sql = "
    INSERT INTO episodes (name, duration, description, file_path, playable) VALUES
    ('Episode 1: Noel Baba''nın Kara Listesi', '33:41', 'Yılın son oyun haberleri ve en saçma vakalar...', 'episodes/ep1.wav', 1),
    ('Episode 2: Yılbaşı Temizliği Bitti', 'Haber bekleniyor...', 'Ocak ayı oyun haberleri ve kalan absürtler...', 'episodes/ep2.wav', 0),
    ('Episode 3: Aşk Budur!', 'Haber bekleniyor...', 'Şubat ayı oyun haberleri ve trajikomik vakalar...', 'episodes/ep3.wav', 0),
    ('Episode 4: Yerden Biten Saçmalıklar','Haber bekleniyor...','Mart ayı oyun haberleri ve yeni sezon başlangıçları...','episodes/ep4.wav', 0);";

    $conn->exec($insert_data_sql);
    echo "<span style='color: green; font-weight: bold;'>✔ Bölümler başarıyla içeri aktarıldı.</span><br>";

    echo "<hr><b>Şimdi ana sayfanı (index) açabilirsin, bölümler orada görünecektir!</b>";

} catch (PDOException $e) {
    // Bir hata olursa burada kırmızı renkte yazacak
    die("<span style='color: red; font-weight: bold;'>HATA OLUŞTU: </span>" . $e->getMessage());
}
?>