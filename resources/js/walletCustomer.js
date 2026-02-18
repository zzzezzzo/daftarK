
const openBtn = document.getElementById('openModalBtn');
const modal = document.getElementById('myModal');
const closeBtns = [document.getElementById('closeModalBtn'), document.getElementById('closeModalBtn2')];

openBtn.addEventListener('click', () => {
    modal.classList.remove('hidden');
});

closeBtns.forEach(btn => {
    btn.addEventListener('click', () => {
    modal.classList.add('hidden');
    });
});

// Close modal on clicking outside
modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.add('hidden');
});