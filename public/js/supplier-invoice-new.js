let selectedProduct = null;
let invoiceProducts = [];


/* ===========================
   البحث عن المنتج
=========================== */
document.getElementById("productSearch").addEventListener("input", function () {

    const query = this.value.toLowerCase();
    const box = document.getElementById("suggestionsBox");

    if (!query) {
        box.classList.add("hidden");
        return;
    }

    const filtered = window.products.filter(p =>
        p.code.toLowerCase().includes(query) ||
        p.name.toLowerCase().includes(query)
    );

    box.innerHTML = "";

    filtered.forEach(product => {
        const div = document.createElement("div");
        div.className = "p-3 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-200 dark:border-gray-600";
        div.innerHTML = `
            <div class="flex justify-between items-center">
                <div>
                    <div class="font-semibold text-gray-800 dark:text-white">${product.name}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">المتوفر: ${product.stock} | سعر الشراء: ${product.price_base}</div>
                </div>
                <div class="text-lg font-bold text-green-600 dark:text-green-400">${product.price_base} ج.م</div>
            </div>
        `;
        div.onclick = () => selectProduct(product);
        box.appendChild(div);
    });

    box.classList.remove("hidden");
});

/* ===========================
   اختيار المنتج
=========================== */
function selectProduct(product) {
    selectedProduct = product;
    document.getElementById("productSearch").value = product.name;
    document.getElementById("productPrice").value = product.price_base;
    document.getElementById("suggestionsBox").classList.add("hidden");
}

/* ===========================
   إضافة منتج للفاتورة
=========================== */
function addProductToInvoice() {
    const qty = parseInt(document.getElementById("productQty").value);
    console.log(qty);
    const price = parseFloat(document.getElementById("productPrice").value);

    if (!selectedProduct) {
        showNotification("يرجى اختيار منتج أولاً", "error");
        return;
    }

    if (!qty || qty <= 0) {
        showNotification("يرجى إدخال كمية صحيحة", "error");
        return;
    }
    const existingProduct = invoiceProducts.find(p => p.id === selectedProduct.id);
    if (existingProduct) {
        existingProduct.quantity += qty;
        existingProduct.total = price * existingProduct.quantity;
    } else {
        invoiceProducts.push({
            id: selectedProduct.id,
            name: selectedProduct.name,
            price: price,
            quantity: qty,
            total: price * qty
        });
    }

    renderProducts();
    updateTotal();

    // Clear inputs
    document.getElementById("productSearch").value = "";
    document.getElementById("productQty").value = "1";
    document.getElementById("productPrice").value = "";
    selectedProduct = null;

    showNotification("تم إضافة المنتج بنجاح", "success");
}

/* ===========================
   عرض المنتجات
=========================== */
function renderProducts() {
    const table = document.getElementById("productsTable");
    const inputs = document.getElementById("productsInputs");
    const countBadge = document.getElementById("productsCount");
    const emptyState = document.getElementById("emptyState");

    table.innerHTML = "";
    inputs.innerHTML = "";
    countBadge.textContent = invoiceProducts.length;

    // Show/hide empty state
    if (invoiceProducts.length === 0) {
        emptyState.style.display = "block";
        table.parentElement.style.display = "none";
    } else {
        emptyState.style.display = "none";
        table.parentElement.style.display = "table";
    }

    invoiceProducts.forEach((item, index) => {

        table.innerHTML += `
            <tr>
                <td class="p-2 text-gray-50">${item.name}</td>
                <td class="p-2 text-gray-50">${item.price}</td>
                <td class="p-2">${item.quantity}</td>
                <td class="p-2">${item.total.toFixed(2)}</td>
                <td class="p-2">
                    <button onclick="removeProduct(${index})"
                        class="bg-red-500 text-white px-2 rounded">
                        حذف
                    </button>
                </td>
            </tr>
        `;

        inputs.innerHTML += `
            <input type="hidden" name="products[${index}][product_id]" value="${item.id}">
            <input type="hidden" name="products[${index}][quantity]" value="${item.quantity}">
            <input type="hidden" name="products[${index}][unit_price]" value="${item.price}">
        `;
    });
}

/* ===========================
   حذف
=========================== */
function removeProduct(index) {
    invoiceProducts.splice(index, 1);
    renderProducts();
    updateTotal();
}

/* ===========================
   تحديث الإجمالي
=========================== */
function updateTotal() {
    let total = 0;
    invoiceProducts.forEach(item => {
        total += item.total;
    });

    document.getElementById("totalAmount").innerText = total.toFixed(2);
}

/* ===========================
   إشعارات
=========================== */
function showNotification(message, type) {
    const notification = document.createElement("div");
    notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white z-50 ${
        type === "success" ? "bg-green-500" : "bg-red-500"
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

/* ===========================
   التحقق من صحة النموذج
=========================== */
document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");
    if (form) {
        form.addEventListener("submit", function(e) {
            if (invoiceProducts.length === 0) {
                e.preventDefault();
                showNotification("يجب إضافة منتج واحد على الأقل", "error");
                return false;
            }
            return true;
        });
    }
});
