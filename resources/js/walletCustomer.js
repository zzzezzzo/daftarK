
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
const openBtn2 = document.getElementById('openPaymentModalBtn');
const closePaymentBtns = [document.getElementById('closePaymentModalBtn'), document.getElementById('closePaymentModalBtn2')];
openBtn2.addEventListener('click', () => {
    document.getElementById('paymentModal').classList.remove('hidden');
});

closePaymentBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('paymentModal').classList.add('hidden');
    });
});