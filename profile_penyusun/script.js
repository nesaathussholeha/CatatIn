// Modal Laporan
function toggleModal(show) {
    const modal = document.getElementById('reportModal');
    if (show) {
        modal.classList.add('active');
    } else {
        modal.classList.remove('active');
    }
}

function submitReport() {
    toggleModal(false);
    alert('Terima kasih, laporan Anda telah kami terima.');
}

// Interactive Upvote Counter
function toggleUpvote(button) {
    const countSpan = button.querySelector('.upvote-count');
    const icon = button.querySelector('i');
    let count = parseInt(countSpan.textContent);

    if (button.classList.contains('active')) {
        button.classList.remove('active');
        icon.classList.remove('fa-solid');
        icon.classList.add('fa-regular');
        countSpan.textContent = count - 1;
    } else {
        button.classList.add('active');
        icon.classList.remove('fa-regular');
        icon.classList.add('fa-solid');
        countSpan.textContent = count + 1;
    }
}

// Live Filter & Search Functionality
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const categorySelect = document.getElementById('categorySelect');
    const cards = document.querySelectorAll('.note-card');

    function applyFilter() {
        const query = searchInput ? searchInput.value.toLowerCase() : '';
        const category = categorySelect ? categorySelect.value : 'all';

        cards.forEach(card => {
            const title = card.getAttribute('data-title');
            const cardCat = card.getAttribute('data-category');

            const matchesSearch = title.includes(query);
            const matchesCategory = (category === 'all' || cardCat === category);

            if (matchesSearch && matchesCategory) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    if (searchInput) searchInput.addEventListener('input', applyFilter);
    if (categorySelect) categorySelect.addEventListener('change', applyFilter);
});