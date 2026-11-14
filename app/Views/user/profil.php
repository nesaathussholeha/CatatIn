
<?php
$escape = fn($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$nama = $old['nama_lengkap'] ?? $user['nama_lengkap'] ?? '';
$jurusan = $old['jurusan'] ?? $user['jurusan'] ?? '';
$nim = $old['nim'] ?? $user['nim'] ?? '';
$email = $old['email'] ?? $user['email'] ?? '';
$hp = $old['no_hp'] ?? $user['no_hp'] ?? '';
$foto = $user['foto_profil'] ?? '';

$fotoUrl = $foto
    ? BASE_URL . '/' . ltrim($foto, '/')
    : '';

$profilErrors = $errors['profil'] ?? [];
$passwordErrors = $errors['password'] ?? [];

function profileFieldError($errors, $key)
{
    if (empty($errors[$key])) {
        return '';
    }

    return '<p class="err">' .
        htmlspecialchars($errors[$key], ENT_QUOTES, 'UTF-8') .
        '</p>';
}
?>

<div class="profil-page">

    <?php if (!empty($flash)): ?>
        <div class="toast <?= ($flash[0] ?? '') === 'ok' ? 'ok' : 'bad' ?>" id="toast">
            <span><?= $escape($flash[1] ?? '') ?></span>
        </div>
    <?php endif; ?>

    <!-- Statistik profil -->
    <section class="stats">
        <article class="card stat">
            <div class="stat-ico">♡</div>
            <div>
                <b class="num" data-count="<?= (int) $stats['upvote'] ?>">
                    <?= (int) $stats['upvote'] ?>
                </b>
                <p>Total upvote diterima</p>
            </div>
        </article>

        <article class="card stat">
            <div class="stat-ico">▤</div>
            <div>
                <b class="num" data-count="<?= (int) $stats['catatan'] ?>">
                    <?= (int) $stats['catatan'] ?>
                </b>
                <p>Catatan diunggah</p>
            </div>
        </article>

        <article class="card stat">
            <div class="medal">🏅</div>
            <div>
                <b class="badge-name"><?= $escape($stats['badge']) ?></b>
                <p>Badge reputasi</p>
            </div>
        </article>
    </section>

    <div class="profile-grid">

        <!-- Informasi akun -->
        <section class="card panel">
            <h2>Informasi Akun</h2>
            <p class="hint">Perbarui informasi pribadi akunmu.</p>

            <form
                method="POST"
                action="<?= BASE_URL ?>/index.php?page=profile"
                enctype="multipart/form-data"
                id="formProfil"
                novalidate
            >
                <input type="hidden" name="csrf" value="<?= $escape($csrf) ?>">
                <input type="hidden" name="aksi" value="profil">

                <div class="identity">
                    <div class="photo">
                        <?php if ($fotoUrl): ?>
                            <img src="<?= $escape($fotoUrl) ?>" alt="Foto profil" id="photoImg">
                            <span id="photoDefault" hidden>👤</span>
                        <?php else: ?>
                            <span id="photoDefault">👤</span>
                            <img src="" alt="Foto profil" id="photoImg" hidden>
                        <?php endif; ?>
                    </div>

                    <div>
                        <strong class="id-name" id="liveName">
                            <?= $escape($nama) ?>
                        </strong>
                        <small id="liveJurusan"><?= $escape($jurusan) ?></small>
                    </div>
                </div>

                <input
                    type="file"
                    name="foto"
                    id="foto"
                    accept="image/jpeg,image/png,image/webp"
                    hidden
                >

                <button type="button" class="btn btn-outline" id="btnFoto">
                    📷 Ganti Foto
                </button>

                <?= profileFieldError($profilErrors, 'foto') ?>

                <div class="field">
                    <label for="nama">Nama Lengkap</label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        value="<?= $escape($nama) ?>"
                        required
                    >
                    <?= profileFieldError($profilErrors, 'nama') ?>
                </div>

                <div class="field">
                    <label for="jurusan">Jurusan</label>
                    <input
                        type="text"
                        id="jurusan"
                        name="jurusan"
                        value="<?= $escape($jurusan) ?>"
                        required
                    >
                    <?= profileFieldError($profilErrors, 'jurusan') ?>
                </div>

                <div class="field">
                    <label for="nim">NIM / Nomor Induk</label>
                    <input
                        type="text"
                        id="nim"
                        name="nim"
                        value="<?= $escape($nim) ?>"
                        inputmode="numeric"
                        maxlength="20"
                        required
                    >
                    <?= profileFieldError($profilErrors, 'nim') ?>
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= $escape($email) ?>"
                        required
                    >
                    <?= profileFieldError($profilErrors, 'email') ?>
                </div>

                <div class="field">
                    <label for="hp">Nomor HP</label>
                    <input
                        type="tel"
                        id="hp"
                        name="hp"
                        value="<?= $escape($hp) ?>"
                        required
                    >
                    <?= profileFieldError($profilErrors, 'hp') ?>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary" id="btnSimpan" disabled>
                        Simpan Perubahan
                    </button>

                    <span class="dirty-note" id="dirtyNote" hidden>
                        Ada perubahan yang belum disimpan
                    </span>
                </div>
            </form>
        </section>

        <!-- Keamanan akun -->
        <section class="card panel panel-security">
            <h2>Keamanan Akun</h2>
            <p class="hint">Ubah password untuk menjaga keamanan akunmu.</p>

            <form
                method="POST"
                action="<?= BASE_URL ?>/index.php?page=profile"
                id="formPass"
                novalidate
            >
                <input type="hidden" name="csrf" value="<?= $escape($csrf) ?>">
                <input type="hidden" name="aksi" value="password">

                <div class="field">
                    <label for="pl">Password Saat Ini</label>
                    <div class="pw">
                        <input type="password" id="pl" name="password_lama" required>
                        <button type="button" class="eye" data-target="pl" aria-label="Tampilkan password">◉</button>
                    </div>
                    <?= profileFieldError($passwordErrors, 'lama') ?>
                </div>

                <div class="field">
                    <label for="pb">Password Baru</label>
                    <div class="pw">
                        <input type="password" id="pb" name="password_baru" required>
                        <button type="button" class="eye" data-target="pb" aria-label="Tampilkan password">◉</button>
                    </div>
                    <?= profileFieldError($passwordErrors, 'baru') ?>

                    <div class="meter" id="meter" data-level="0">
                        <i></i><i></i><i></i><i></i>
                    </div>
                    <small class="meter-label" id="meterLabel"></small>
                </div>

                <div class="field">
                    <label for="pk">Konfirmasi Password Baru</label>
                    <div class="pw">
                        <input type="password" id="pk" name="password_konfirmasi" required>
                        <button type="button" class="eye" data-target="pk" aria-label="Tampilkan password">◉</button>
                    </div>
                    <?= profileFieldError($passwordErrors, 'konf') ?>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 20px;" id="btnPassword" disabled>
                    Ubah Password
                </button>
            </form>
        </section>

    </div>
</div>
