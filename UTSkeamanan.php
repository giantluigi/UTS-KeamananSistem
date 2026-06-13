<?php

function encrypt_triphase($plaintext, $key) {
    if ($plaintext === "") return "";
    
    // 1. Fase Substitusi (Caesar Shift pada Printable ASCII 32-126)
    $shifted_text = "";
    $len = strlen($plaintext);
    for ($i = 0; $i < $len; $i++) {
        $char = $plaintext[$i];
        $ascii = ord($char);
        if ($ascii >= 32 && $ascii <= 126) {
            $shifted_ascii = (($ascii - 32 + $key) % 95 + 95) % 95 + 32;
            $shifted_text .= chr($shifted_ascii);
        } else {
            $shifted_text .= $char;
        }
    }

    // 2. Fase Reverse Text
    $reversed_text = strrev($shifted_text);

    // 3. Fase Transposisi (Genap & Ganjil)
    $evens = ""; $odds = "";
    $rev_len = strlen($reversed_text);
    for ($i = 0; $i < $rev_len; $i++) {
        if ($i % 2 === 0) {
            $evens .= $reversed_text[$i];
        } else {
            $odds .= $reversed_text[$i];
        }
    }
    return $evens . $odds;
}

function decrypt_triphase($ciphertext, $key) {
    $len = strlen($ciphertext);
    if ($len === 0) return "";

    // 1. Fase Reverse Transposisi
    $mid_point = (int)ceil($len / 2);
    $evens = substr($ciphertext, 0, $mid_point);
    $odds = substr($ciphertext, $mid_point);

    $untransposed_text = "";
    $p_even = 0; $p_odd = 0;
    for ($i = 0; $i < $len; $i++) {
        if ($i % 2 === 0) {
            $untransposed_text .= $evens[$p_even++];
        } else {
            $untransposed_text .= $odds[$p_odd++];
        }
    }

    // 2. Fase Un-Reverse Text
    $unreversed_text = strrev($untransposed_text);

    // 3. Fase Dekripsi Substitusi
    $plaintext = "";
    $unrev_len = strlen($unreversed_text);
    for ($i = 0; $i < $unrev_len; $i++) {
        $char = $unreversed_text[$i];
        $ascii = ord($char);
        if ($ascii >= 32 && $ascii <= 126) {
            $unshifted_ascii = (($ascii - 32 - $key) % 95 + 95) % 95 + 32;
            $plaintext .= chr($unshifted_ascii);
        } else {
            $plaintext .= $char;
        }
    }
    return $plaintext;
}

// Inisialisasi variabel default atau menangkap input POST
$input_text = isset($_POST['text']) ? $_POST['text'] : "Apa yang Allah ambil darimu, akan diganti dengan sesuatu yang lebih baik jika kamu bersabar";
$kunci = isset($_POST['key']) ? (int)$_POST['key'] : 17;

// Eksekusi Algoritma
$ciphertext = encrypt_triphase($input_text, $kunci);
$decrypted_text = decrypt_triphase($ciphertext, $kunci);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tri-Phase Cipher Tool | Sistem Informasi</title>
    <style>
        :root {
            --bg-color: #f3f4f6;
            --card-bg: #ffffff;
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --success: #10b981;
            --border: #e5e7eb;
        }

        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .container {
            width: 100%;
            max-width: 680px;
            background: var(--card-bg);
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }

        header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 20px;
        }

        header h1 {
            font-size: 24px;
            margin: 0 0 8px 0;
            color: var(--primary);
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        header p {
            margin: 0;
            font-size: 14px;
            color: var(--text-muted);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-main);
        }

        textarea, input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            background-color: #f9fafb;
            box-sizing: border-box;
            transition: all 0.2s ease;
        }

        textarea:focus, input[type="number"]:focus {
            outline: none;
            border-color: var(--primary);
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn-submit {
            width: 100%;
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 14px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .btn-submit:hover {
            background-color: var(--primary-hover);
        }

        .results-section {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px dashed var(--border);
        }

        .result-box {
            margin-bottom: 18px;
            padding: 15px;
            border-radius: 8px;
            background: #f8fafc;
            border-left: 4px solid var(--primary);
        }

        .result-box.success {
            border-left-color: var(--success);
            background-color: #f0fdf4;
        }

        .result-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            font-weight: 700;
            margin-bottom: 6px;
        }

        .result-content {
            font-family: 'Courier New', Courier, monospace;
            font-size: 15px;
            word-break: break-all;
            color: var(--text-main);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #065f46;
            background-color: #d1fae5;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <header>
        <h1>🛡️ Tri-Phase Cipher Tool</h1>
        <p>Tugas Kriptografi Primitif Sederhana • Mahasiswa Sistem Informasi</p>
    </header>

    <form method="POST" action="">
        <div class="form-group">
            <label Gym for="text">Teks / Pesan (Plaintext)</label>
            <textarea id="text" name="text" required><?= htmlspecialchars($input_text) ?></textarea>
        </div>

        <div class="form-group">
            <label for="key">Kunci Rahasia (Integer Key)</label>
            <input type="number" id="key" name="key" value="<?= $kunci ?>" required min="1">
        </div>

        <button type="submit" class="btn-submit">Proses Enkripsi & Dekripsi</button>
    </form>

    <div class="results-section">
        <div class="result-box">
            <div class="result-title">🔒 Hasil Enkripsi (Ciphertext)</div>
            <div class="result-content"><?= htmlspecialchars($ciphertext) ?></div>
        </div>

        <div class="result-box">
            <div class="result-title">🔓 Hasil Dekripsi (Plaintext Kembali)</div>
            <div class="result-content"><?= htmlspecialchars($decrypted_text) ?></div>
        </div>

        <?php if ($input_text === $decrypted_text): ?>
            <div class="status-badge">
                <span>✅ INTEGRITY OK: Teks kembali 100% sempurna!</span>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>