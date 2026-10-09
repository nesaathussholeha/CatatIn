document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form[action='register.php']");
  if (!form) return;

  const password = form.querySelector("input[name='password']");
  const konfirmasi = form.querySelector("input[name='konfirmasi_password']");
  const hint = document.getElementById("konfirmasi-hint");
  const submitBtn = form.querySelector("button[type='submit']");

  function cekKecocokanPassword() {
    const cocok = password.value === konfirmasi.value;
    const kosong = konfirmasi.value === "";

    if (!cocok && !kosong) {
      konfirmasi.classList.add("input-error");
      hint.classList.add("show");
    } else {
      konfirmasi.classList.remove("input-error");
      hint.classList.remove("show");
    }
    return cocok;
  }

  password.addEventListener("input", cekKecocokanPassword);
  konfirmasi.addEventListener("input", cekKecocokanPassword);

  form.addEventListener("submit", function (e) {
    if (!cekKecocokanPassword()) {
      e.preventDefault();
      konfirmasi.focus();
      return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = "Memproses...";
  });
});