import './bootstrap';
import './theme';
import Swal from 'sweetalert2';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Sidebar Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    if (sidebarToggle && sidebar && sidebarOverlay) {
        // Toggle sidebar
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        });
        
        // Close sidebar when clicking overlay
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.add('translate-x-full');
            sidebarOverlay.classList.add('hidden');
        });
        
        // Close sidebar when pressing Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                sidebar.classList.add('translate-x-full');
                sidebarOverlay.classList.add('hidden');
            }
        });
    }
});

// Swal.fire("SweetAlert2 is working!");
document.querySelectorAll('.delete-product').forEach(form => {
    form.querySelector('button').addEventListener('click', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تتمكن من التراجع عن هذا الحذف!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذفه!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
document.querySelectorAll('.delete-supplier').forEach(form =>{
    form.querySelector('button').addEventListener('click', function(e){
        e.preventDefault();
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تتمكن من التراجع عن هذا الحذف!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذفه!',
            cancelButtonText: 'إلغاء'
        }).then((result)=>{
            if(result.isConfirmed){
                form.submit();
            }
        })
    })
})
document.querySelectorAll('.delete-customer').forEach(form=>{
    form.querySelector('button').addEventListener('click', (e)=>{
        e.preventDefault();
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تتمكن من التراجع عن هذا الحذف!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذفه!',
            cancelButtonText: 'إلغاء'
        }).then((result)=>{
            if(result.isConfirmed){
                form.submit();
            }
        })
    })
})
document.querySelectorAll('.delete-CustomerInvoice').forEach(form=>{
    form.querySelector('button').addEventListener('click', (e)=>{
        e.preventDefault();
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تتمكن من التراجع عن هذا الحذف!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذفه!',
            cancelButtonText: 'إلغاء'
        }).then((result)=>{
            if(result.isConfirmed){
                form.submit();
            }
        })
    })
})
function createProductRow(index) {
    const wrapper = document.getElementById('itemsWrapper');
    const newItem = document.createElement('div');
    newItem.classList.add('flex','gap-3','items-end','mt-2');

    let optionsHTML = `<option value="" disabled selected>اختر المنتج</option>`;
    productsList.forEach(product => {
        optionsHTML += `<option value="${product.id}">${product.name}</option>`;
    });

    newItem.innerHTML = `
        <div class="w-1/3 relative">
            <label class="block text-gray-700 dark:text-gray-300 mb-1">المنتج</label>
            <select class="productSelect w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    name="products[${index}][product_id]">
                ${optionsHTML}
            </select>
        </div>
        <div class="w-1/3">
            <label class="block text-gray-700 dark:text-gray-300 mb-1">الكمية</label>
            <input type="number"
                   name="products[${index}][quantity]"
                   class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                   value="1" min="1" required>
        </div>

        <div class="w-1/3">
            <label class="block text-gray-700 dark:text-gray-300 mb-1">سعر الوحدة</label>
            <input type="number"
                   name="products[${index}][unit_price]"
                   step="0.01"
                   class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                   required>
        </div>

        <button type="button"
                class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 removeItemBtn">
            حذف
        </button>
    `;

    wrapper.appendChild(newItem);
}
// زر إضافة صف جديد
document.getElementById('addItemBtn').addEventListener('click', function(){
    createProductRow(itemIndex);
    itemIndex++;
});

// زر حذف الصف
document.addEventListener('click', function(e){
    if(e.target && e.target.classList.contains('removeItemBtn')){
        e.target.parentElement.remove();
    }
});

function getNextIndex() {
    const inputs = document.querySelectorAll('[name^="products["]');
    let maxIndex = -1;
    inputs.forEach(input => {
        const match = input.name.match(/products\[(\d+)\]/);
        if (match) {
            const index = parseInt(match[1]);
            if (index > maxIndex) {
                maxIndex = index;
            }
        }
    });
    return maxIndex + 1;
}

document.getElementById('addItemBtn').addEventListener('click', function() {
    const itemIndex = getNextIndex(); // 👈 نجيب رقم جديد ديناميك
    const newItem = document.createElement('div');
    newItem.className = 'grid grid-cols-2 md:grid-cols-5 gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl';

    let options = `<option value="" disabled selected>اختر المنتج</option>`;
    products.forEach(product => {
        options += `<option value="${product.id}">${product.name}</option>`;
    });

    newItem.innerHTML = `
        <div class="w-full relative">
            <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">
                المنتج
            </label>
            <div class="relative">
                <select name="products[${itemIndex}][product_id]" 
                        class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl" required>
                    ${options}
                </select>
            </div>
        </div>

        <div class="w-full relative">
            <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">
                الكمية
            </label>
            <input type="number" 
                name="products[${itemIndex}][quantity]" 
                value="1" 
                min="1"
                class="w-full p-4 border border-gray-300 dark:border-gray-600 rounded-xl" 
                required>
        </div>

        <input type="hidden" 
            name="products[${itemIndex}][unit_price]" 
            value="0">

        <div class="w-full flex items-end">
            <button type="button" 
                class="delete-btn p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all">
                <i class="bi bi-trash3"></i>
            </button>
        </div>
    `;

    newItem.querySelector('.delete-btn').addEventListener('click', function () {
        newItem.remove();
    });

    document.getElementById('itemsWrapper').appendChild(newItem);
});


