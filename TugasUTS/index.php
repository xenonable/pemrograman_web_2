
<?php
$conn = new mysqli("localhost", "root", "", "calon_mhs");
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$pesan = "";
$tipe_pesan = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["simpan"])) {
    $kode = strtoupper(trim($_POST["kode"] ?? ""));
    $nama = trim($_POST["nama"] ?? "");
    $jk = $_POST["jk"] ?? "";
    $asal = trim($_POST["asal_sekolah"] ?? "");
    $ortu = trim($_POST["pekerjaan_ortu"] ?? "");
    $tempat = trim($_POST["tempat_lahir"] ?? "");
    $tgl = $_POST["tgl_lahir"] ?? "";
    $mat = (float)($_POST["mat"] ?? 0);
    $bing = (float)($_POST["bing"] ?? 0);
    $umum = (float)($_POST["umum"] ?? 0);

    if (
        $kode == "" || $nama == "" || $asal == "" ||
        $tempat == "" || $tgl == "" ||
        !in_array($jk, ["Laki-Laki", "Perempuan"]) ||
        min($mat, $bing, $umum) < 0 ||
        max($mat, $bing, $umum) > 100
    ) {
        $pesan = "Periksa kembali data dan nilai. Nilai harus 0–100.";
        $tipe_pesan = "error";
    } else {
        $rata = ($mat + $bing + $umum) / 3;

        if ($rata >= 70) {
            $ket = "Lulus";
        } elseif ($rata >= 60) {
            $ket = "Cadangan";
        } else {
            $ket = "Tidak Lulus";
        }

        $awal = substr($kode, 0, 1);
        if ($awal == "A") {
            $tes = "Gedung A";
        } elseif ($awal == "B") {
            $tes = "Gedung B";
        } elseif ($awal == "V") {
            $tes = "Viktor";
        } else {
            $tes = "Tidak Diketahui";
        }

        $ttl = $tempat . ", " . $tgl;

        $stmt = $conn->prepare(
            "INSERT INTO calon_mhs
            (kode, nama, jenis_kelamin, ttl, asal_sekolah,
            pekerjaan_ortu, matematika, inggris, umum,
            rata_rata, keterangan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssssssdddds",
            $kode, $nama, $jk, $ttl, $asal, $ortu,
            $mat, $bing, $umum, $rata, $ket
        );

        if ($stmt->execute()) {
            $pesan = "Data pendaftar berhasil disimpan!";
            $tipe_pesan = "success";
        } else {
            $pesan = $stmt->errno == 1062
                ? "Kode pendaftaran sudah digunakan!"
                : "Data gagal disimpan. Periksa struktur tabel database.";
            $tipe_pesan = "error";
        }

        $stmt->close();
    }
}

$data = [];
$hasil = $conn->query("SELECT * FROM calon_mhs ORDER BY id DESC");

if ($hasil) {
    while ($row = $hasil->fetch_assoc()) {
        $data[] = $row;
    }
}

$total = count($data);
$lulus = 0;
$cadangan = 0;
$tidak = 0;

foreach ($data as $d) {
    if ($d["keterangan"] == "Lulus") {
        $lulus++;
    } elseif ($d["keterangan"] == "Cadangan") {
        $cadangan++;
    } else {
        $tidak++;
    }
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pendaftaran Mahasiswa Baru</title>

<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: "Segoe UI", Arial, sans-serif;
    background: #f1f5f9;
    color: #1e293b;
    padding: 30px 16px;
}

.container {
    max-width: 1150px;
    margin: auto;
}

.hero {
    background: linear-gradient(135deg, #1d4ed8, #4338ca);
    color: white;
    padding: 32px;
    border-radius: 18px;
    margin-bottom: 24px;
    box-shadow: 0 10px 25px #1e40af25;
}

.hero .tag {
    display: inline-block;
    background: #ffffff25;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    margin-bottom: 12px;
}

.hero h1 {
    font-size: 28px;
    margin-bottom: 9px;
}

.hero p {
    color: #dbeafe;
    line-height: 1.6;
}

.card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 26px;
    margin-bottom: 24px;
    box-shadow: 0 5px 18px #0f172a08;
}

.section-title {
    font-size: 20px;
    margin-bottom: 6px;
}

.subtitle {
    color: #64748b;
    font-size: 13px;
    margin-bottom: 24px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 19px;
}

.field {
    min-width: 0;
}

.field.full {
    grid-column: 1 / -1;
}

label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #334155;
}

input[type="text"],
input[type="date"],
input[type="number"] {
    width: 100%;
    padding: 12px 13px;
    border: 1px solid #cbd5e1;
    border-radius: 9px;
    font-size: 14px;
    outline: none;
    background: white;
    transition: .2s;
}

input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px #2563eb18;
}

.radio-group {
    display: flex;
    gap: 20px;
    align-items: center;
    min-height: 42px;
}

.radio-group label {
    display: flex;
    gap: 7px;
    align-items: center;
    margin: 0;
    font-weight: 400;
}

.btn {
    display: inline-block;
    border: none;
    border-radius: 9px;
    padding: 13px 23px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: .2s;
}

.btn-primary {
    color: white;
    background: #2563eb;
}

.btn-primary:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
}

.alert {
    padding: 14px 17px;
    border-radius: 10px;
    margin-bottom: 22px;
    font-size: 14px;
}

.success {
    color: #166534;
    background: #dcfce7;
    border: 1px solid #bbf7d0;
}

.error {
    color: #991b1b;
    background: #fee2e2;
    border: 1px solid #fecaca;
}

.stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 15px;
    margin-bottom: 24px;
}

.stat {
    background: white;
    padding: 20px;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
}

.stat p {
    font-size: 13px;
    color: #64748b;
    margin-bottom: 10px;
}

.stat h2 {
    font-size: 28px;
}

.blue { color: #2563eb; }
.green { color: #16a34a; }
.orange { color: #d97706; }
.red { color: #dc2626; }

.table-wrap {
    width: 100%;
    overflow-x: auto;
}

table {
    border-collapse: collapse;
    width: 100%;
    font-size: 13px;
    white-space: nowrap;
}

th {
    background: #f1f5f9;
    color: #475569;
    text-align: left;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .3px;
}

th, td {
    padding: 13px 14px;
    border-bottom: 1px solid #e2e8f0;
}

tbody tr:hover {
    background: #f8fafc;
}

.badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
}

.badge-lulus {
    color: #166534;
    background: #dcfce7;
}

.badge-cadangan {
    color: #92400e;
    background: #fef3c7;
}

.badge-tidak {
    color: #991b1b;
    background: #fee2e2;
}

.empty {
    text-align: center;
    padding: 35px;
    color: #64748b;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
}

.info-box {
    padding: 17px;
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    line-height: 1.8;
    font-size: 13px;
}

.info-box h3 {
    font-size: 14px;
    margin-bottom: 7px;
}

footer {
    text-align: center;
    color: #94a3b8;
    font-size: 12px;
    padding: 10px 0 20px;
}

@media (max-width: 700px) {
    body { padding: 16px 10px; }
    .hero { padding: 24px 20px; }
    .hero h1 { font-size: 23px; }
    .card { padding: 19px; }
    .form-grid { grid-template-columns: 1fr; }
    .field.full { grid-column: auto; }
    .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .stat { padding: 15px; }
    .stat h2 { font-size: 24px; }
    .info-grid { grid-template-columns: 1fr; }
}
</style>
</head>

<body>
<div class="container">

    <header class="hero">
        <span class="tag">SISTEM INFORMASI PENDAFTARAN</span>
        <h1>Pendaftaran Mahasiswa Baru</h1>
        <p>Formulir pendaftaran dan seleksi calon mahasiswa baru.</p>
    </header>

    <?php if ($pesan != ""): ?>
        <div class="alert <?= e($tipe_pesan) ?>">
            <?= e($pesan) ?>
        </div>
    <?php endif; ?>

    <section class="card">
        <h2 class="section-title">Formulir Pendaftaran</h2>
        <p class="subtitle">Lengkapi informasi calon mahasiswa di bawah ini.</p>

        <form method="POST" class="form-grid">
            <div class="field">
                <label>Kode Pendaftaran</label>
                <input type="text" name="kode" placeholder="Contoh: A001"
                       maxlength="30" required>
            </div>

            <div class="field">
                <label>Nama Pendaftar</label>
                <input type="text" name="nama" placeholder="Nama lengkap"
                       maxlength="150" required>
            </div>

            <div class="field">
                <label>Jenis Kelamin</label>
                <div class="radio-group">
                    <label><input type="radio" name="jk"
                           value="Laki-Laki" required> Laki-Laki</label>
                    <label><input type="radio" name="jk"
                           value="Perempuan"> Perempuan</label>
                </div>
            </div>

            <div class="field">
                <label>Asal Sekolah</label>
                <input type="text" name="asal_sekolah"
                       placeholder="Nama sekolah asal" required>
            </div>

            <div class="field">
                <label>Pekerjaan Orang Tua</label>
                <input type="text" name="pekerjaan_ortu"
                       placeholder="Pekerjaan orang tua">
            </div>

            <div class="field">
                <label>Tempat Lahir</label>
                <input type="text" name="tempat_lahir"
                       placeholder="Kota kelahiran" required>
            </div>

            <div class="field">
                <label>Tanggal Lahir</label>
                <input type="date" name="tgl_lahir" required>
            </div>

            <div class="field">
                <label>Nilai Matematika</label>
                <input type="number" name="mat" min="0" max="100"
                       step="0.01" placeholder="0 - 100" required>
            </div>

            <div class="field">
                <label>Nilai Bahasa Inggris</label>
                <input type="number" name="bing" min="0" max="100"
                       step="0.01" placeholder="0 - 100" required>
            </div>

            <div class="field">
                <label>Nilai Umum</label>
                <input type="number" name="umum" min="0" max="100"
                       step="0.01" placeholder="0 - 100" required>
            </div>

            <div class="field full">
                <button type="submit" name="simpan" class="btn btn-primary">
                    SIMPAN PENDAFTARAN
                </button>
            </div>
        </form>
    </section>

    <section class="stats">
        <div class="stat">
            <p>Total Pendaftar</p>
            <h2 class="blue"><?= $total ?></h2>
        </div>
        <div class="stat">
            <p>Peserta Lulus</p>
            <h2 class="green"><?= $lulus ?></h2>
        </div>
        <div class="stat">
            <p>Peserta Cadangan</p>
            <h2 class="orange"><?= $cadangan ?></h2>
        </div>
        <div class="stat">
            <p>Tidak Lulus</p>
            <h2 class="red"><?= $tidak ?></h2>
        </div>
    </section>

    <section class="card">
        <h2 class="section-title">Data Pendaftar</h2>
        <p class="subtitle">
            Data berikut diambil langsung dari database Laragon.
        </p>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>TTL</th>
                        <th>Asal Sekolah</th>
                        <th>Pekerjaan Ortu</th>
                        <th>Tempat Tes</th>
                        <th>Matematika</th>
                        <th>Inggris</th>
                        <th>Umum</th>
                        <th>Rata-rata</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="13" class="empty">
                            Belum ada data pendaftar.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data as $i => $d): ?>
                    <?php
                        $awal = strtoupper(substr($d["kode"], 0, 1));
                        $tes = $awal == "A" ? "Gedung A" :
                               ($awal == "B" ? "Gedung B" :
                               ($awal == "V" ? "Viktor" : "Tidak Diketahui"));

                        $status = $d["keterangan"];
                        $class = $status == "Lulus" ? "badge-lulus" :
                                 ($status == "Cadangan" ? "badge-cadangan" : "badge-tidak");
                    ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= e($d["kode"]) ?></td>
                        <td><?= e($d["nama"]) ?></td>
                        <td><?= e($d["jenis_kelamin"]) ?></td>
                        <td><?= e($d["ttl"]) ?></td>
                        <td><?= e($d["asal_sekolah"]) ?></td>
                        <td><?= e($d["pekerjaan_ortu"] ?? "-") ?></td>
                        <td><?= e($tes) ?></td>
                        <td><?= e($d["matematika"]) ?></td>
                        <td><?= e($d["inggris"]) ?></td>
                        <td><?= e($d["umum"]) ?></td>
                        <td><strong><?= number_format((float)$d["rata_rata"], 2) ?></strong></td>
                        <td><span class="badge <?= $class ?>"><?= e($status) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="card">
        <h2 class="section-title">Informasi Seleksi</h2>
        <p class="subtitle">Ketentuan kode tempat tes dan hasil seleksi.</p>

        <div class="info-grid">
            <div class="info-box">
                <h3>Kode Tempat Tes</h3>
                A = Gedung A<br>
                B = Gedung B<br>
                V = Viktor
            </div>

            <div class="info-box">
                <h3>Kriteria Kelulusan</h3>
                Rata-rata ≥ 70 = Lulus<br>
                Rata-rata ≥ 60 = Cadangan<br>
                Rata-rata &lt; 60 = Tidak Lulus
            </div>

            <div class="info-box">
                <h3>Perhitungan Nilai</h3>
                Rata-rata = (Matematika + Inggris + Umum) / 3
            </div>
        </div>
    </section>

    <footer>
        Sistem Pendaftaran Mahasiswa Baru &copy; <?= date("Y") ?>
    </footer>

</div>
</body>
</html>
<?php $conn->close(); ?>
