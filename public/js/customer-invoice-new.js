// Customer Invoice Creation JavaScript
let selectedProduct = null;
let invoiceProducts = [];

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {    
    // Check if elements exist
    const productSearch = document.getElementById('productSearch');
    const suggestionsBox = document.getElementById('suggestionsBox');
    if (!productSearch) {
        console.error('productSearch element not found');
        return;
    }
    if (!suggestionsBox) {
        console.error('suggestionsBox element not found');
        return;
    }
});

// البحث عن المنتجات
document.getElementById('productSearch').addEventListener('input', function () {
    const query = this.value.toLowerCase();
    const box = document.getElementById('suggestionsBox');
    
    if (query.length < 1) {
        box.classList.add('hidden');
        return;
    }
    
    const filtered = window.products.filter(p =>
        p.name.toLowerCase().includes(query)
    );
    
    box.innerHTML = '';
    
    if (filtered.length === 0) {
        const div = document.createElement('div');
        div.className = "p-3 text-gray-500 text-center border-b";
        div.innerHTML = '<div>لا توجد نتائج</div>';
        box.appendChild(div);
        box.classList.remove('hidden');
        return;
    }
    
    filtered.forEach(product => {
        let price = getPriceByCustomerType(product);
        const stockClass = product.stock > 0 ? 'text-green-600' : 'text-red-600';
        const stockText = product.stock > 0 ? `المتوفر: ${product.stock}` : `غير متوفر`;
        
        const div = document.createElement('div');
        div.className = "p-3 cursor-pointer border-b transition-colors";
        div.innerHTML = `
            <div class="font-semibold text-gray-100">${product.name}</div>
            <div class="flex justify-between items-center mt-1">
                <div class="text-green-600 text-sm font-medium">
                    السعر: ${price} ج.م
                </div>
                <div class="${stockClass} text-sm font-medium">
                    ${stockText}
                </div>
            </div>
        `;
        
        div.onclick = function () {
            selectedProduct = product;
            document.getElementById('productSearch').value = product.name;
            const priceInput = document.getElementById('productPrice');
            priceInput.value = price;
            box.classList.add('hidden');
        };
        
        box.appendChild(div);
    });
    
    box.classList.remove('hidden');
});

// إضافة المنتج للقائمة
function addProductToInvoice() {
    const qty = parseInt(document.getElementById('productQty').value);
    const priceInputUpdate = document.getElementById('productPrice').value;   
    
    if (!selectedProduct) {
        showNotification("اختر منتج أولاً", "error");
        return;
    }
    
    // Check stock availability
    if (qty > selectedProduct.stock) {
        showNotification(`الكمية المطلوبة (${qty}) تتجاوز المتوفر (${selectedProduct.stock})`, "error");
        return;
    }
    
    const price = priceInputUpdate ? parseFloat(priceInputUpdate) : getPriceByCustomerType(selectedProduct);
    const total = price * qty;
    
    invoiceProducts.push({
        id: selectedProduct.id,
        name: selectedProduct.name,
        price: price,
        quantity: qty,
        total: total,
        stock: selectedProduct.stock
    });
    
    renderProducts();
    
    // Reset
    document.getElementById('productSearch').value = '';
    document.getElementById('productQty').value = 1;
    document.getElementById('productPrice').value = '';
    selectedProduct = null;
    
    showNotification("تم إضافة المنتج بنجاح", "success");
}

// عرض المنتجات
function renderProducts() {
    const table = document.getElementById('productsTable');
    const productsInputs = document.getElementById('productsInputs');
    const productsCount = document.getElementById('productsCount');
    table.innerHTML = '';
    productsInputs.innerHTML = '';
    let totalInvoice = 0;
    
    // Update products count
    productsCount.textContent = invoiceProducts.length;
    
    if (invoiceProducts.length === 0) {
        table.innerHTML = `
            <tr>
                <td colspan="5" class="p-8 text-center text-gray-500">
                    <i class="bi bi-inbox text-4xl mb-2"></i>
                    <div>لا توجد منتجات مضافة</div>
                </td>
            </tr>
        `;
        document.getElementById('totalAmount').innerText = '0.00 ج.م';
        return;
    }
    
    invoiceProducts.forEach((item, index) => {
        totalInvoice += item.total;
        
        // Check if quantity exceeds stock
        const isOverStock = item.quantity > item.stock;
        const stockClass = isOverStock ? 'text-red-600' : 'text-green-600';
        const stockText = isOverStock ? `تجاوز المخزون (${item.stock})` : `متوفر (${item.stock})`;
        
        table.innerHTML += `
            <tr class="border-t hover:bg-gray-50 transition-colors ${isOverStock ? 'bg-red-50' : ''}">
                <td class="p-3">
                    <div class="font-medium text-gray-800">${item.name}</div>
                    ${isOverStock ? '<div class="text-xs text-red-600 mt-1"><i class="bi bi-exclamation-triangle"></i> تجاوز المخزون</div>' : ''}
                </td>
                <td class="p-3">
                    <span class="font-semibold text-blue-700">${item.price}</span> ج.م
                </td>
                <td class="p-3">
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm font-medium">${item.quantity}</span>
                    <div class="text-xs ${stockClass} mt-1">${stockText}</div>
                </td>
                <td class="p-3">
                    <span class="font-bold ${isOverStock ? 'text-red-600' : 'text-green-700'}">${item.total}</span> ج.م
                </td>
                <td class="p-3">
                    <button onclick="removeProduct(${index})"
                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg transition-all transform hover:scale-105">
                        <i class="bi bi-trash3"></i>
                    </button>
                </td>
            </tr>
        `;
        
        // Create hidden inputs for form submission
        productsInputs.innerHTML += `
            <input type="hidden" name="products[${index}][product_id]" value="${item.id}">
            <input type="hidden" name="products[${index}][quantity]" value="${item.quantity}">
            <input type="hidden" name="products[${index}][price]" value="${item.price}">
        `;
    });
    
    document.getElementById('totalAmount').innerText = totalInvoice.toFixed(2) + ' ج.م';
}

// حذف المنتج
function removeProduct(index) {
    invoiceProducts.splice(index, 1);
    renderProducts();
    showNotification("تم حذف المنتج", "info");
}

// تحديد السعر حسب نوع العميل
function getPriceByCustomerType(product) {
    if (window.customerType === 'trade') {
        return product.price_trade;
    }
    if (window.customerType === 'technical') {
        return product.price_technician;
    }
    if (window.customerType === 'client') {
        return product.price_customer;
    }
    return product.price_base;
}

// إخفاء القائمة عند النقر خارجها
document.addEventListener('click', function(e) {
    const box = document.getElementById('suggestionsBox');
    if (box && !e.target.closest('#productSearch') && !e.target.closest('#suggestionsBox')) {
        box.classList.add('hidden');
    }
});

// Show notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
    
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        info: 'bg-blue-500 text-white'
    };
    
    const icons = {
        success: 'bi-check-circle',
        error: 'bi-exclamation-triangle',
        info: 'bi-info-circle'
    };
    
    notification.className += ` ${colors[type]}`;
    notification.innerHTML = `
        <div class="flex items-center gap-2">
            <i class="bi ${icons[type]}"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}
