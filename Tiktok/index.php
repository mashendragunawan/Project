<?php
// Koneksi ke database
$host = 'localhost';
$db = 'article_db';
$user = 'root'; // Ganti dengan username database Anda
$pass = ''; // Ganti dengan password database Anda
$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Simpan artikel jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $tags = $_POST['tags'];
    $content = $_POST['content'];

    if (!empty($title) && !empty($tags) && !empty($content)) {
        $stmt = $conn->prepare('INSERT INTO articles (title, tags, content) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $title, $tags, $content);
        $stmt->execute();
        $stmt->close();
    }
}

// Ambil artikel dari database
$articles = [];
$result = $conn->query('SELECT * FROM articles');
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $articles[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Artikel dengan Card</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* Styling untuk form */
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .form-container input,
        .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-container button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #0056b3;
        }

        /* Styling untuk card */
        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            overflow: hidden;
            margin-bottom: 20px;
            padding: 15px;
        }

        .card-title {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }

        .tags-container {
            margin-bottom: 10px;
        }

        .tag {
            display: inline-block;
            background-color: #007bff;
            color: white;
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 15px;
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .read-more-button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }

        .read-more-button:hover {
            background-color: #0056b3;
        }

        /* Styling untuk modal artikel */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
            max-height: 80%;
            overflow-y: auto;
        }

        .modal-content h2 {
            margin-top: 0;
        }

        .close-button {
            background-color: #ff4d4d;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            float: right;
        }

        .close-button:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Form Input -->
        <div class="form-container">
            <h2>Buat Artikel Baru</h2>
            <form method="POST" action="">
                <input type="text" name="title" placeholder="Judul Artikel" required>
                <input type="text" name="tags" placeholder="Tag (pisahkan dengan koma, contoh: Teknologi, Pendidikan)" required>
                <textarea name="content" placeholder="Isi artikel..." rows="10" required></textarea>
                <button type="submit">Simpan</button>
            </form>
        </div>

        <!-- Tempat Menampilkan Card -->
        <div id="cardContainer">
            <?php foreach ($articles as $article): ?>
                <div class="card">
                    <div class="card-title"><?= htmlspecialchars($article['title']) ?></div>
                    <div class="tags-container">
                        <?php foreach (explode(',', $article['tags']) as $tag): ?>
                            <div class="tag"><?= htmlspecialchars(trim($tag)) ?></div>
                        <?php endforeach; ?>
                    </div>
                    <button class="read-more-button" onclick="openModal('<?= addslashes($article['title']) ?>', '<?= addslashes($article['tags']) ?>', '<?= addslashes($article['content']) ?>')">Read More</button>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Modal untuk menampilkan artikel lengkap -->
        <div id="articleModal" class="modal">
            <div class="modal-content">
                <button class="close-button" onclick="closeModal()">×</button>
                <h2 id="modalTitle"></h2>
                <div id="modalTags" class="tags-container"></div>
                <p id="modalContent"></p>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk membuka modal artikel
        function openModal(title, tags, content) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalTags').innerHTML = tags.split(',').map(tag => `<div class="tag">${tag.trim()}</div>`).join('');
            document.getElementById('modalContent').textContent = content;
            document.getElementById('articleModal').style.display = 'flex';
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            document.getElementById('articleModal').style.display = 'none';
        }
    </script>
</body>
</html>