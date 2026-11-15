
// Modal Laporan
function toggleModal(show) {
    const modal = document.getElementById('reportModal');

    if (!modal) return;

    modal.classList.toggle('active', show);
}

function submitReport() {
    const reason = document.getElementById('reportReason');

    if (!reason.value.trim()) {
        alert('Silakan tuliskan alasan laporan terlebih dahulu.');
        return;
    }

    toggleModal(false);
    reason.value = '';

    alert('Terima kasih, laporan Anda telah kami terima.');
}

// Upvote Counter
function toggleUpvote(button) {
    const countSpan = button.querySelector('.upvote-count');
    const totalUpvote = document.getElementById('totalUpvote');

    let count = parseInt(countSpan.textContent);
    let total = totalUpvote
        ? parseInt(totalUpvote.textContent.replace(/\./g, ''))
        : 0;

    if (button.classList.contains('active')) {
        button.classList.remove('active');
        count--;
        total--;
    } else {
        button.classList.add('active');
        count++;
        total++;
    }

    countSpan.textContent = count;

    if (totalUpvote) {
        totalUpvote.textContent = total.toLocaleString('id-ID');
    }

    const card = button.closest('.note-card');

    if (card) {
        card.dataset.upvote = count;
    }
}

// Pencarian, Filter, dan Pengurutan
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const categorySelect = document.getElementById('categorySelect');
    const sortSelect = document.getElementById('sortSelect');
    const noteGrid = document.getElementById('noteGrid');
    const emptyMessage = document.getElementById('emptyMessage');
    const displayCount = document.getElementById('displayCount');

    if (!noteGrid) return;

    const cards = Array.from(noteGrid.querySelectorAll('.note-card'));
    const totalCards = cards.length;

    function applyFilter() {
        const query = searchInput
            ? searchInput.value.toLowerCase().trim()
            : '';

        const category = categorySelect
            ? categorySelect.value
            : 'all';

        const sort = sortSelect
            ? sortSelect.value
            : 'terbaru';

        let filteredCards = cards.filter(card => {
            const title = card.dataset.title || '';
            const cardCategory = card.dataset.category || '';

            const matchesSearch = title.includes(query);
            const matchesCategory =
                category === 'all' || cardCategory === category;

            return matchesSearch && matchesCategory;
        });

        // Pengurutan
        filteredCards.sort((a, b) => {
            if (sort === 'terbaru') {
                return Number(b.dataset.date) - Number(a.dataset.date);
            }

            if (sort === 'terlama') {
                return Number(a.dataset.date) - Number(b.dataset.date);
            }

            if (sort === 'upvote') {
                return Number(b.dataset.upvote) - Number(a.dataset.upvote);
            }

            return 0;
        });

        // Tampilkan kartu sesuai hasil filter
        cards.forEach(card => {
            card.style.display = 'none';
        });

        filteredCards.forEach(card => {
            card.style.display = '';
            noteGrid.appendChild(card);
        });

        // Pesan jika kosong
        if (emptyMessage) {
            emptyMessage.hidden = filteredCards.length > 0;
        }

        // Jumlah catatan
        if (displayCount) {
            displayCount.textContent =
                `Menampilkan ${filteredCards.length} dari ${totalCards} catatan`;
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', applyFilter);
    }

    if (categorySelect) {
        categorySelect.addEventListener('change', applyFilter);
    }

    if (sortSelect) {
        sortSelect.addEventListener('change', applyFilter);
    }

    // Tutup modal ketika klik area luar
    const modal = document.getElementById('reportModal');

    if (modal) {
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                toggleModal(false);
            }
        });
    }

    // Tutup modal dengan tombol Escape
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            toggleModal(false);
        }
    });

    applyFilter();
});
