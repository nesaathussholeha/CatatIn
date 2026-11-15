<div class="profil-admin">

    <div class="profil-card">

        <!-- BAGIAN KIRI -->
        <div class="profil-left">

            <div class="profil-avatar-wrapper">
                <div class="profil-avatar">
                    <img id="previewFoto" src="<?= BASE_URL ?>/images/default-avatar.png" alt="Foto Profil">
                    <span id="avatarInitial">A</span>
                </div>

                <label for="fotoProfil" class="btn-foto">
                    <i class="fa-solid fa-camera"></i>
                    Ganti Foto
                </label>

                <input type="file" id="fotoProfil" accept="image/png,image/jpeg,image/webp" hidden>
            </div>

            <div class="profil-user-info">
                <h3 id="displayUsername">admin</h3>
                <span class="profil-role">Administrator</span>
            </div>

            <div class="profil-divider"></div>

            <div class="profil-biodata">
                <div class="profil-info-item">
                    <span>Nama Lengkap</span>
                    <strong>Administrator CatatIn</strong>
                </div>

                <div class="profil-info-item">
                    <span>Email</span>
                    <strong>admin@catatin.com</strong>
                </div>

                <div class="profil-info-item">
                    <span>Status Akun</span>
                    <strong class="status-aktif">Aktif</strong>
                </div>
            </div>

        </div>

        <!-- BAGIAN KANAN -->
        <div class="profil-right">

            <section class="profil-section">
                <div class="profil-section-heading">
                    <h3>Informasi Akun</h3>
                    <p>Ubah username akun administrator.</p>
                </div>

                <form id="formProfil">

                    <div class="profil-form-group">
                        <label for="username">Username</label>
                        <input
                            type="text"
                            id="username"
                            value="admin"
                            placeholder="Masukkan username"
                            required>
                    </div>

                    <button type="submit" class="profil-btn">
                        Simpan Perubahan
                    </button>

                </form>
            </section>

            <div class="profil-divider"></div>

            <section class="profil-section">
                <div class="profil-section-heading">
                    <h3>Keamanan Akun</h3>
                    <p>Ubah password untuk menjaga keamanan akun.</p>
                </div>

                <form id="formPassword">

                    <div class="profil-form-group">
                        <label for="passwordLama">Password Lama</label>
                        <div class="password-wrapper">
                            <input
                                type="password"
                                id="passwordLama"
                                placeholder="Masukkan password lama"
                                required>
                            <button type="button" class="toggle-password" data-target="passwordLama">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="profil-form-group">
                        <label for="passwordBaru">Password Baru</label>
                        <div class="password-wrapper">
                            <input
                                type="password"
                                id="passwordBaru"
                                placeholder="Masukkan password baru"
                                required>
                            <button type="button" class="toggle-password" data-target="passwordBaru">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="profil-form-group">
                        <label for="konfirmasiPassword">Konfirmasi Password Baru</label>
                        <div class="password-wrapper">
                            <input
                                type="password"
                                id="konfirmasiPassword"
                                placeholder="Ulangi password baru"
                                required>
                            <button type="button" class="toggle-password" data-target="konfirmasiPassword">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="profil-btn">
                        Ganti Password
                    </button>

                </form>
            </section>

        </div>

    </div>

</div>