let selectedProduct = null;
let invoiceProducts = JSON.parse(localStorage.getItem("customerInvoiceProducts")) || [];

// حفظ المنتجات في LocalStorage
function saveProducts() {
    localStorage.setItem("customerInvoiceProducts", JSON.stringify(invoiceProducts));
}

// البحث وإظهار الاقتراحات أثناء الكتابة
document.getElementById("productSearch").addEventListener("input", function () {
    const query = this.value.toLowerCase();
    const box = document.getElementById("suggestionsBox");

    if (!query) {
        box.classList.add("hidden");
        return;
    }

    const filtered = window.products.filter((p) =>
        p.code.toLowerCase().includes(query) ||
        p.name.toLowerCase().includes(query)
    );

    box.innerHTML = "";

    filtered.forEach((product) => {
        const price = getPriceByCustomerType(product);
        const div = document.createElement("div");
        div.className =
            "p-3 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-200 dark:border-gray-600";

        div.innerHTML = `
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">الكود: ${product.code}</div>
                    <div class="font-semibold text-gray-800 dark:text-white">${product.name}</div>
                    <div class="text-lg font-bold text-green-600 dark:text-green-400">${price} ج.م</div>
                    <div class="text-sm text-gray-500 dark:text-gray-300">المتوفر: ${product.stock}</div>
                </div>
            </div>
        `;

        div.onclick = () => selectProduct(product);
        box.appendChild(div);
    });

    box.classList.remove("hidden");
});

// عند اختيار منتج
function selectProduct(product) {
    selectedProduct = product;
    document.getElementById("productSearch").value = product.name;
    document.getElementById("productPrice").value = getPriceByCustomerType(product);
    document.getElementById("suggestionsBox").classList.add("hidden");
}

// إضافة المنتج الحالي إلى الفاتورة (الليستة)
function addProductToInvoice() {
    const qty = parseInt(document.getElementById("productQty").value, 10);
    const price = parseFloat(document.getElementById("productPrice").value);

    if (!selectedProduct) {
        showNotification("يرجى اختيار منتج أولاً", "error");
        return;
    }

    if (!qty || qty <= 0) {
        showNotification("يرجى إدخال كمية صحيحة", "error");
        return;
    }
    const invoiceTypeElement = document.querySelector('select[name="type"]');
    const invoiceType = invoiceTypeElement ? invoiceTypeElement.value : 'payment';
    if(invoiceType === 'payment' && qty > selectedProduct.stock) {
        showNotification(`الكمية المطلوبة (${qty}) تتجاوز المتوفر (${selectedProduct.stock}) `, "error");
        return;
    }

    const existingProduct = invoiceProducts.find((p) => p.id === selectedProduct.id);

    if (existingProduct) {
        const newQuantity = existingProduct.quantity + qty;
        if (newQuantity > selectedProduct.stock) {
            showNotification(`إجمالي الكمية (${newQuantity}) يتجاوز المتوفر (${selectedProduct.stock})`, "error");
            return;
        }
        existingProduct.quantity = newQuantity;
        existingProduct.total = existingProduct.price * existingProduct.quantity;
    } else {
        invoiceProducts.push({
            id: selectedProduct.id,
            name: selectedProduct.name,
            price: price,
            quantity: qty,
            total: price * qty
        });
    }

    saveProducts();
    renderProducts();
    updateTotal();

    // إعادة تعيين الحقول بعد الإضافة بنجاح لتجهيزها للمنتج التالي
    document.getElementById("productSearch").value = "";
    document.getElementById("productQty").value = "1";
    document.getElementById("productPrice").value = "";
    selectedProduct = null;

    showNotification("تم إضافة المنتج بنجاح", "success");
    
    // إعادة مؤشر الكتابة تلقائياً لخانة البحث للمنتج القادم
    document.getElementById("productSearch").focus();
}

// عرض المنتجات في الجدول والـ Inputs المخفية للفورم
function renderProducts() {
    const table = document.getElementById("productsTable");
    const inputs = document.getElementById("productsInputs");
    const countBadge = document.getElementById("productsCount");
    const emptyState = document.getElementById("emptyState");

    table.innerHTML = "";
    inputs.innerHTML = "";
    countBadge.textContent = invoiceProducts.length;

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
                <td class="p-2 text-white">${item.name}</td>
                <td class="p-2 text-white">${item.price}</td>
                <td class="p-2 text-white">${item.quantity}</td>
                <td class="p-2 text-white">${item.total.toFixed(2)}</td>
                <td class="p-2">
                    <button type="button" onclick="removeProduct(${index})" class="bg-red-500 text-white px-2 rounded">
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

// حذف منتج من الفاتورة
function removeProduct(index) {
    invoiceProducts.splice(index, 1);
    saveProducts();
    renderProducts();
    updateTotal();
}

// تحديث إجمالي الفاتورة والكميات
function updateTotal() {
    let total = 0;
    invoiceProducts.forEach((item) => {
        total += item.total;
    });

    document.getElementById("totalAmount").innerText = total.toFixed(2);
    const totalQtyElement = document.getElementById("totalQuantity");
    if (totalQtyElement) {
        const totalQty = invoiceProducts.reduce((sum, item) => sum + item.quantity, 0);
        totalQtyElement.innerText = `${totalQty} وحدة`;
    }
}

// جلب السعر بناءً على فئة العميل
function getPriceByCustomerType(product) {
    if (window.customerType === "trade") {
        return product.price_trade;
    }
    if (window.customerType === "technical") {
        return product.price_technician;
    }
    if (window.customerType === "client") {
        return product.price_customer;
    }
    return product.price_base;
}

// إظهار الإشعارات الملونة
function showNotification(message, type) {
    const notification = document.createElement("div");
    notification.className = `
        fixed top-4 right-4 p-4 rounded-lg text-white z-50
        ${type === "success" ? "bg-green-500" : "bg-red-500"}
    `;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// إخفاء صندوق الاقتراحات عند الضغط في أي مكان خارج الحقول
document.addEventListener("click", function (e) {
    const box = document.getElementById("suggestionsBox");
    if (box && !e.target.closest("#productSearch") && !e.target.closest("#suggestionsBox")) {
        box.classList.add("hidden");
    }
});

// الأحداث عند تحميل الصفحة بالكامل
document.addEventListener("DOMContentLoaded", function () {
    renderProducts();
    updateTotal();

    // التحكم في إرسال الحفظ النهائي للفورم (زر الحفظ الأساسي للفاتورة)
    const form = document.querySelector("form");
    if (form) {
        form.addEventListener("submit", function (e) {
            if (invoiceProducts.length === 0) {
                e.preventDefault();
                showNotification("يجب إضافة منتج واحد على الأقل", "error");
                return false;
            }
            return true;
        });
    }

    // --- حـل مشـكلة سـكانر البـاركود والتحويل للكمية والسعر ---
    const searchInput = document.getElementById("productSearch");
    const qtyInput = document.getElementById("productQty");
    const priceInput = document.getElementById("productPrice");

    // 1. عند الضغط على Enter في خانة البحث (الاسكانر)
    if (searchInput) {
        searchInput.addEventListener("keydown", function (e) {
            if (e.key === "Enter") {
                e.preventDefault(); // منع حفظ الفاتورة

                const query = this.value.trim().toLowerCase();
                if (!query) return;

                // البحث عن تطابق تام لكود الباركود
                const exactMatch = window.products.find(p => p.code.toLowerCase() === query);

                if (exactMatch) {
                    selectProduct(exactMatch); // تعبئة البيانات والسعر بناء على نوع العميل
                    
                    // تحويل مؤشر الماوس تلقائياً لخانة الكمية وعمل تظليل (Select) للرقم الحالي لتعديله فوراً
                    if (qtyInput) {
                        qtyInput.focus();
                        qtyInput.select(); 
                    }
                } else {
                    showNotification("كود المنتج غير صحيح أو غير مسجل", "error");
                }
            }
        });
    }

    // 2. عند الضغط على Enter في خانة الكمية -> يضيف المنتج لليستة بدلاً من إرسال الفورم
    if (qtyInput) {
        qtyInput.addEventListener("keydown", function (e) {
            if (e.key === "Enter") {
                e.preventDefault(); // منع حفظ الفاتورة
                addProductToInvoice(); // إضافة لليستة
            }
        });
    }

    // 3. عند الضغط على Enter في خانة السعر (لو حبيت تعدله يدوي) -> يضيف المنتج لليستة
    if (priceInput) {
        priceInput.addEventListener("keydown", function (e) {
            if (e.key === "Enter") {
                e.preventDefault(); // منع حفظ الفاتورة
                addProductToInvoice(); // إضافة لليستة
            }
        });
    }
});