<?php
/**
 * Notes Hub — Dashboard
 * Halaman utama untuk menjelajahi catatan kuliah yang diunggah mahasiswa.
 * Palet warna & tipografi mengikuti Rancangan Desain CatatIn.
 */

// ------------------------------------------------------------------
// "Basis data" sederhana (array). Ganti dengan query database sungguhan
// jika sudah tersedia (mis. PDO ke MySQL).
// ------------------------------------------------------------------
$catatan = [
    [
        'id'       => 1,
        'judul'    => 'Rangkuman Struktur Data — Pohon & Graf',
        'penyusun' => 'Nazma F.',
        'jurusan'  => 'Informatika',
        'tipe'     => 'Rangkuman',
        'upvote'   => 128,
        'tanggal'  => '2026-09-20',
    ],
    [
        'id'       => 2,
        'judul'    => 'Kumpulan Soal UAS Basis Data 2025',
        'penyusun' => 'Nesa',
        'jurusan'  => 'Sistem Informasi',
        'tipe'     => 'Soal Ujian',
        'upvote'   => 96,
        'tanggal'  => '2026-09-18',
    ],
    [
        'id'       => 3,
        'judul'    => 'Modul Praktikum Rangkaian Digital',
        'penyusun' => 'Naswa S.',
        'jurusan'  => 'Elektro',
        'tipe'     => 'Modul',
        'upvote'   => 54,
        'tanggal'  => '2026-09-15',
    ],
    [
        'id'       => 4,
        'judul'    => 'Rangkuman Pemrograman Web — Sesi 1-7',
        'penyusun' => 'Afifatul M.',
        'jurusan'  => 'Informatika',
        'tipe'     => 'Rangkuman',
        'upvote'   => 41,
        'tanggal'  => '2026-09-10',
    ],
];

// ------------------------------------------------------------------
// Baca parameter dari query string (filter, pencarian, urutan)
// ------------------------------------------------------------------
$jurusanAktif = isset($_GET['jurusan']) ? trim($_GET['jurusan']) : 'Semua jurusan';
$kataKunci    = isset($_GET['q']) ? trim($_GET['q']) : '';
$urutan       = isset($_GET['urutan']) ? $_GET['urutan'] : 'terbaru';

$daftarJurusan = ['Semua jurusan', 'Informatika', 'Elektro', 'Sistem Informasi', 'Rangkuman', 'Soal Ujian', 'Modul'];

// ------------------------------------------------------------------
// Terapkan filter
// ------------------------------------------------------------------
$hasil = array_filter($catatan, function ($item) use ($jurusanAktif, $kataKunci) {
    $cocokJurusan = true;
    if ($jurusanAktif !== 'Semua jurusan') {
        // Beberapa pilihan mewakili "jurusan", sebagian mewakili "tipe" — cocokkan keduanya.
        $cocokJurusan = ($item['jurusan'] === $jurusanAktif) || ($item['tipe'] === $jurusanAktif);
    }

    $cocokKataKunci = true;
    if ($kataKunci !== '') {
        $gabungan = strtolower($item['judul'] . ' ' . $item['jurusan'] . ' ' . $item['penyusun']);
        $cocokKataKunci = str_contains($gabungan, strtolower($kataKunci));
    }

    return $cocokJurusan && $cocokKataKunci;
});

// ------------------------------------------------------------------
// Terapkan urutan
// ------------------------------------------------------------------
$hasil = array_values($hasil);
usort($hasil, function ($a, $b) use ($urutan) {
    if ($urutan === 'terpopuler') {
        return $b['upvote'] <=> $a['upvote'];
    }
    // default: terbaru
    return strtotime($b['tanggal']) <=> strtotime($a['tanggal']);
});

/**
 * Bangun kembali query string, dengan opsi menimpa satu parameter.
 */
function buatTautan(array $override = []): string
{
    $params = array_merge($_GET, $override);
    return '?' . http_build_query($params);
}

function inisial(string $nama): string
{
    $kata = preg_split('/\s+/', trim($nama));
    $inisial = '';
    foreach (array_slice($kata, 0, 2) as $k) {
        if ($k !== '') {
            $inisial .= mb_strtoupper(mb_substr($k, 0, 1));
        }
    }
    return $inisial ?: '?';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notes Hub — Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
<style>
  :root{
    /* ---- Palet CatatIn ---- */
    --navy:#171A3D;         /* teks utama & sidebar */
    --indigo:#4C5FE0;       /* aksi utama */
    --indigo-dark:#3B4BC7;  /* hover/pressed */
    --lavender:#EEF1FB;     /* latar halaman & elemen lembut */
    --slate:#64748B;        /* teks sekunder */
    --line:#E1E4F2;         /* border/divider */
    --green:#16A34A;        /* sukses / upvote */
    --amber:#F59E0B;        /* aksen sekunder */

    --card:#ffffff;
    --radius-lg:18px;
    --radius-md:12px;
    --shadow: 0 10px 30px -12px rgba(23,26,61,0.16);
  }
  *{box-sizing:border-box;}
  html, body{
    height:100%;
    margin:0;
    font-family:"Inter", -apple-system, BlinkMacSystemFont, sans-serif;
    font-size:15px;
    line-height:1.6;
    background:var(--lavender);
    color:var(--navy);
  }
  a{color:inherit; text-decoration:none;}
  h1,h2,h3,h4,h5,h6{
    font-family:"Poppins", "Inter", sans-serif;
    color:var(--navy);
    margin:0;
  }

  .app{
    display:flex;
    min-height:100vh;
    width:100%;
    background:var(--card);
  }

  /* ---------- Sidebar ---------- */
  .sidebar{
    width:230px;
    flex-shrink:0;
    background:var(--navy);
    color:#c7cae0;
    padding:28px 18px;
    display:flex;
    flex-direction:column;
    gap:28px;
    position:sticky;
    top:0;
    height:100vh;
    overflow-y:auto;
  }
  .brand{
    display:flex;
    align-items:center;
    font-family:"Poppins", sans-serif;
    font-weight:600;
    font-size:1.05rem;
    color:#fff;
    padding:0 6px;
  }
  nav.menu{
    display:flex;
    flex-direction:column;
    gap:4px;
  }
  nav.menu a{
    padding:11px 14px;
    border-radius:10px;
    font-family:"Poppins", sans-serif;
    font-weight:500;
    font-size:0.92rem;
    color:#c7cae0;
    transition:background .15s ease, color .15s ease;
  }
  nav.menu a:hover{ background:rgba(255,255,255,0.08); color:#fff; }
  nav.menu a.active{
    background:var(--indigo);
    color:#fff;
  }
  .sidebar .keluar{ margin-top:auto; }

  /* ---------- Main ---------- */
  .main{
    flex:1;
    padding:clamp(16px,4vw,26px) clamp(16px,4vw,34px) 40px;
    min-width:0;
  }
  .crumb{
    font-size:0.8rem;
    color:var(--slate);
    margin-bottom:18px;
  }
  .topbar{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    margin-bottom:22px;
  }
  .topbar h1{
    flex:1;
    font-weight:600;
    font-size:clamp(1.4rem, 3vw, 1.85rem);
    line-height:1.25;
    letter-spacing:-0.01em;
  }
  .avatar{
    width:36px; height:36px;
    border-radius:50%;
    background:var(--indigo);
    color:#fff;
    display:flex; align-items:center; justify-content:center;
    font-family:"Poppins", sans-serif;
    font-size:0.78rem;
    font-weight:600;
    flex-shrink:0;
  }

  /* Search row */
  .search-row{
    display:flex;
    gap:12px;
    margin-bottom:16px;
  }
  .search-box{
    flex:1;
    display:flex;
    align-items:center;
    gap:10px;
    background:var(--lavender);
    border:1px solid var(--line);
    border-radius:12px;
    padding:11px 16px;
  }
  .search-box input{
    border:none;
    background:transparent;
    outline:none;
    width:100%;
    font-family:"Inter", sans-serif;
    font-size:0.92rem;
    color:var(--navy);
  }
  .search-box svg{ flex-shrink:0; color:var(--slate); }

  .sort-btn{
    display:flex;
    align-items:center;
    gap:6px;
    padding:8px 4px;
    background:none;
    border:none;
    font-family:"Inter", sans-serif;
    font-size:0.85rem;
    color:var(--slate);
    cursor:pointer;
    white-space:nowrap;
  }
  .sort-btn:hover{ color:var(--navy); }
  .sort-btn.active{ color:var(--navy); font-weight:600; }

  /* Filter jurusan: pills di desktop, dropdown di tablet/HP */
  .pills{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
    margin-bottom:26px;
  }
  .pill{
    padding:8px 16px;
    border-radius:999px;
    font-family:"Inter", sans-serif;
    font-size:0.83rem;
    border:1px solid var(--line);
    background:var(--card);
    color:var(--slate);
  }
  .pill:hover{ border-color:var(--indigo); color:var(--navy); }
  .pill.active{
    background:var(--indigo);
    border-color:var(--indigo);
    color:#fff;
    font-weight:500;
  }

  .select-wrap{
    display:none;
    position:relative;
    margin-bottom:26px;
  }
  .select-wrap select{
    appearance:none;
    -webkit-appearance:none;
    width:100%;
    padding:11px 38px 11px 16px;
    border-radius:12px;
    border:1px solid var(--line);
    background:var(--card);
    font-family:"Inter", sans-serif;
    font-size:0.87rem;
    color:var(--navy);
    cursor:pointer;
  }
  .select-wrap select:hover, .select-wrap select:focus{ border-color:var(--indigo); outline:none; }
  .select-wrap::after{
    content:"";
    position:absolute;
    right:16px; top:50%;
    width:8px; height:8px;
    border-right:2px solid var(--slate);
    border-bottom:2px solid var(--slate);
    transform:translateY(-65%) rotate(45deg);
    pointer-events:none;
  }

  @media (max-width:900px){
    .pills{ display:none; }
    .select-wrap{ display:block; }
  }

  /* ---------- Cards ---------- */
  .grid{
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));
    gap:16px;
  }
  .card{
    border:1px solid var(--line);
    border-radius:var(--radius-lg);
    padding:20px;
    background:var(--card);
    display:flex;
    flex-direction:column;
    gap:10px;
  }

  .tag{
    align-self:flex-start;
    font-family:"Poppins", sans-serif;
    font-size:0.72rem;
    font-weight:500;
    line-height:1.4;
    padding:4px 10px;
    border-radius:999px;
  }
  .tag.Informatika{ background:#EEF1FB; color: #171A3D ; }
  .tag.Elektro{ background:#EEF1FB; color: #171A3D ; }
  .tag.Sistem-Informasi{ background:#EEF1FB; color:#171A3D ; }

  .card h3{
    font-weight:600;
    font-size:1.05rem;
    line-height:1.4;
  }
  .card .oleh{
    font-family:"Inter", sans-serif;
    font-size:0.85rem;
    color:var(--slate);
    margin:0;
  }
  .card-foot{
    margin-top:6px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    font-size:0.82rem;
  }
  .upvote{
    color:var(--green);
    font-weight:600;
    display:flex;
    align-items:center;
    gap:4px;
  }
  .detail-link{
    color:var(--slate);
    font-family:"Inter", sans-serif;
    font-size:0.82rem;
  }
  .detail-link:hover{ color:var(--indigo); }

  .empty{
    grid-column:1 / -1;
    text-align:center;
    padding:48px 20px;
    color:var(--slate);
    border:1px dashed var(--line);
    border-radius:var(--radius-lg);
  }

  @media (max-width:900px){
    .search-row{ flex-wrap:wrap; }
    .sort-btn{ flex:1; justify-content:center; }
  }

  /* ---------- Menu toggle (tablet & mobile) ---------- */
  .menu-toggle{
    display:none;
    align-items:center;
    gap:8px;
    border:1px solid rgba(76,95,224,0.3);
    background:var(--lavender);
    padding:9px 12px;
    border-radius:10px;
    font-family:"Poppins", sans-serif;
    font-weight:500;
    font-size:0.85rem;
    color:var(--indigo);
    cursor:pointer;
    flex-shrink:0;
    transition:background .15s ease;
  }
  .menu-toggle:hover{ background:#E4E8FB; }
  .overlay{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(23,26,61,0.45);
    z-index:40;
  }

  @media (max-width:900px){
    .menu-toggle{ display:flex; }

    .sidebar{
      position:fixed;
      top:0; left:0;
      height:100vh;
      z-index:50;
      transform:translateX(-100%);
      transition:transform .22s ease;
      box-shadow:0 0 0 rgba(0,0,0,0);
    }
    body.sidebar-open .sidebar{
      transform:translateX(0);
      box-shadow:20px 0 40px -20px rgba(0,0,0,0.35);
    }
    body.sidebar-open .overlay{ display:block; }

    .sidebar-close{
      display:flex;
      align-items:center;
      justify-content:center;
      width:28px; height:28px;
      border-radius:8px;
      background:rgba(255,255,255,0.1);
      color:#fff;
      margin-left:auto;
      cursor:pointer;
      border:none;
    }
  }

  .sidebar-close{ display:none; }

  @media (max-width:480px){
    .card{ padding:16px; }
    .avatar{ width:32px; height:32px; }
  }
</style>
</head>
<body>
<div class="overlay" onclick="tutupSidebar()"></div>
<div class="app">

  <aside class="sidebar" id="sidebar">
    <div class="brand">
      Notes Hub
      <button type="button" class="sidebar-close" onclick="tutupSidebar()" aria-label="Tutup menu">✕</button>
    </div>
    <nav class="menu">
      <a href="?" class="active">Dashboard</a>
      <a href="#">Unggah Catatan</a>
      <a href="playlist.php">Catatan Saya</a>
      <a href="#">Profil</a>
    </nav>
    <nav class="menu keluar">
      <a href="#">Keluar</a>
    </nav>
  </aside>

  <main class="main">
    <div class="crumb">DASHBOARD</div>

    <div class="topbar">
      <button type="button" class="menu-toggle" onclick="bukaSidebar()" aria-label="Buka menu">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <line x1="3" y1="12" x2="21" y2="12"></line>
          <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
        Menu
      </button>
      <h1>Jelajahi catatan</h1>
      <div class="avatar">SF</div>
    </div>

    <form method="get" class="search-row">
      <input type="hidden" name="jurusan" value="<?= htmlspecialchars($jurusanAktif) ?>">
      <label class="search-box">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="7"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text" name="q" placeholder="Cari judul, matkul, atau penyusun..." value="<?= htmlspecialchars($kataKunci) ?>">
      </label>
      <button type="submit" class="sort-btn <?= $urutan === 'terbaru' ? 'active' : '' ?>" name="urutan" value="terbaru">
        ↓ Terbaru
      </button>
      <button type="submit" class="sort-btn <?= $urutan === 'terpopuler' ? 'active' : '' ?>" name="urutan" value="terpopuler">
        ↑ Terpopuler
      </button>
    </form>

    <div class="pills">
      <?php foreach ($daftarJurusan as $j): ?>
        <a class="pill <?= $j === $jurusanAktif ? 'active' : '' ?>"
           href="<?= buatTautan(['jurusan' => $j]) ?>">
          <?= htmlspecialchars($j) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <form method="get" class="select-wrap">
      <input type="hidden" name="q" value="<?= htmlspecialchars($kataKunci) ?>">
      <input type="hidden" name="urutan" value="<?= htmlspecialchars($urutan) ?>">
      <select name="jurusan" onchange="this.form.submit()">
        <?php foreach ($daftarJurusan as $j): ?>
          <option value="<?= htmlspecialchars($j) ?>" <?= $j === $jurusanAktif ? 'selected' : '' ?>>
            <?= htmlspecialchars($j) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>

    <div class="grid">
      <?php if (empty($hasil)): ?>
        <div class="empty">Tidak ada catatan yang cocok. Coba ubah kata kunci atau filter jurusan.</div>
      <?php else: ?>
        <?php foreach ($hasil as $item): ?>
          <article class="card">
            <span class="tag <?= str_replace(' ', '-', $item['jurusan']) ?>"><?= htmlspecialchars($item['jurusan']) ?></span>
            <h3><?= htmlspecialchars($item['judul']) ?></h3>
            <p class="oleh">oleh <?= htmlspecialchars($item['penyusun']) ?> · <?= htmlspecialchars($item['tipe']) ?></p>
            <div class="card-foot">
              <span class="upvote">▲ <?= (int)$item['upvote'] ?> upvote</span>
              <a class="detail-link" href="detail.php?id=<?= (int)$item['id'] ?>">Lihat detail</a>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>

</div>
<script>
  function bukaSidebar(){
    document.body.classList.add('sidebar-open');
  }
  function tutupSidebar(){
    document.body.classList.remove('sidebar-open');
  }
  // Tutup sidebar otomatis kalau layar dilebarkan ke ukuran desktop
  window.addEventListener('resize', function () {
    if (window.innerWidth > 900) {
      tutupSidebar();
    }
  });
</script>
</body>
</html>