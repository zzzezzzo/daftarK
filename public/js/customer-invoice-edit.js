let selectedProduct = null;
let invoiceProducts = [];

// خريطة تحفظ الكمية الأصلية لكل منتج زي ما كانت مسجلة في الفاتورة قبل التعديل
// دي بنستخدمها عشان نضيفها للمخزون الحالي، لأن الكمية دي أصلاً متخصومة من المخزون
let originalQuantities = {};

/* ===========================
    تحميل المنتجات القديمة
=========================== */
document.addEventListener("DOMContentLoaded", function () {

    if (window.invoiceItems) {
        window.invoiceItems.forEach(item => {
            const product = window.products.find(p => p.id == item.product_id);
            const qty = parseInt(item.quantity, 10);

            invoiceProducts.push({
                id: item.product_id,
                name: product ? product.name : "منتج",
                price: parseFloat(item.unit_price),
                quantity: qty,
                total: parseFloat(item.unit_price) * qty
            });

            // تجميع الكمية الأصلية (لو نفس المنتج اتكرر لأي سبب)
            originalQuantities[item.product_id] =
                (originalQuantities[item.product_id] || 0) + qty;
        });

        renderProducts();
        updateTotal();
    }

    // البحث + سكانر الباركود + تنقل Enter بين الحقول
    initSearchAndBarcode();

    // منع حفظ الفورم لو مفيش منتجات
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
});

/* ===========================
   حساب المخزون الفعلي المتاح لمنتج معين
   = المخزون الحالي في الداتابيز + الكمية الأصلية المحجوزة لنفس المنتج في هذه الفاتورة
=========================== */
function getEffectiveStock(product) {
    const reserved = originalQuantities[product.id] || 0;
    return product.stock + reserved;
}

/* ===========================
   البحث عن المنتج (نفس شكل صفحة الإضافة)
=========================== */
function initSearchAndBarcode() {
    const searchInput = document.getElementById("productSearch");
    const qtyInput = document.getElementById("productQty");
    const priceInput = document.getElementById("productPrice");

    if (!searchInput) return;

    searchInput.addEventListener("input", function () {
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
            const price = getPrice(product);
            const effectiveStock = getEffectiveStock(product);

            // كام قطعة لسه ممكن تتضاف فعليًا (بعد خصم اللي في الفاتورة دلوقتي)
            const alreadyInInvoice = invoiceProducts
                .filter(p => p.id == product.id)
                .reduce((sum, p) => sum + p.quantity, 0);
            const availableToAdd = effectiveStock - alreadyInInvoice;

            const div = document.createElement("div");
            div.className =
                "p-3 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-200 dark:border-gray-600";

            div.innerHTML = `
                <div class="flex justify-between items-center">
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">الكود: ${product.code}</div>
                        <div class="font-semibold text-gray-800 dark:text-white">${product.name}</div>
                        <div class="text-lg font-bold text-green-600 dark:text-green-400">${price} ج.م</div>
                        <div class="text-sm text-gray-500 dark:text-gray-300">المتوفر: ${availableToAdd}</div>
                    </div>
                </div>
            `;

            div.onclick = () => selectProductFn(product);
            box.appendChild(div);
        });

        box.classList.remove("hidden");
    });

    // إخفاء صندوق الاقتراحات عند الضغط خارج الحقول
    document.addEventListener("click", function (e) {
        const box = document.getElementById("suggestionsBox");
        if (box && !e.target.closest("#productSearch") && !e.target.closest("#suggestionsBox")) {
            box.classList.add("hidden");
        }
    });

    // Enter في خانة البحث = سكانر الباركود (تطابق تام مع الكود)
    searchInput.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();

            const query = this.value.trim().toLowerCase();
            if (!query) return;

            const exactMatch = window.products.find(p => p.code.toLowerCase() === query);

            if (exactMatch) {
                selectProductFn(exactMatch);
                if (qtyInput) {
                    qtyInput.focus();
                    qtyInput.select();
                }
            } else {
                showNotification("كود المنتج غير صحيح أو غير مسجل", "error");
            }
        }
    });

    // Enter في خانة الكمية يضيف المنتج للفاتورة بدل ما يبعت الفورم
    if (qtyInput) {
        qtyInput.addEventListener("keydown", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                addProductToInvoice();
            }
        });
    }

    // Enter في خانة السعر يضيف المنتج للفاتورة بدل ما يبعت الفورم
    if (priceInput) {
        priceInput.addEventListener("keydown", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                addProductToInvoice();
            }
        });
    }
}

function selectProductFn(product) {
    selectedProduct = product;
    document.getElementById("productSearch").value = product.name;
    document.getElementById("productPrice").value = getPrice(product);
    document.getElementById("suggestionsBox").classList.add("hidden");
}

/* ===========================
   إضافة المنتج (مع فحص المخزون الفعلي)
=========================== */
function addProductToInvoice() {

    if (!selectedProduct) {
        showNotification("يرجى اختيار منتج أولاً", "error");
        return;
    }

    const qty = parseInt(document.getElementById("productQty").value, 10);
    const price = parseFloat(document.getElementById("productPrice").value);

    if (!qty || qty <= 0) {
        showNotification("يرجى إدخال كمية صحيحة", "error");
        return;
    }

    // المخزون الفعلي = مخزون الداتابيز + الكمية الأصلية المحجوزة لنفس المنتج في هذه الفاتورة
    const effectiveStock = getEffectiveStock(selectedProduct);

    const existingProduct = invoiceProducts.find((p) => p.id == selectedProduct.id);
    const currentQtyInInvoice = existingProduct ? existingProduct.quantity : 0;
    const newTotalQty = currentQtyInInvoice + qty;

    if (newTotalQty > effectiveStock) {
        showNotification(
            `إجمالي الكمية (${newTotalQty}) يتجاوز المتوفر (${effectiveStock})`,
            "error"
        );
        return;
    }

    if (existingProduct) {
        existingProduct.quantity = newTotalQty;
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

    renderProducts();
    updateTotal();

    document.getElementById("productSearch").value = "";
    document.getElementById("productQty").value = "1";
    document.getElementById("productPrice").value = "";
    selectedProduct = null;

    showNotification("تم إضافة المنتج بنجاح", "success");
    document.getElementById("productSearch").focus();
}

/* ===========================
   عرض المنتجات
=========================== */
function renderProducts() {
    const table = document.getElementById("productsTable");
    const inputs = document.getElementById("productsInputs");

    table.innerHTML = "";
    inputs.innerHTML = "";

    invoiceProducts.forEach((item, index) => {
        table.innerHTML += `
            <tr>
                <td class="p-2">${item.name}</td>
                <td class="p-2">${item.price}</td>
                <td class="p-2">${item.quantity}</td>
                <td class="p-2">${item.total.toFixed(2)}</td>
                <td class="p-2">
                    <button type="button" onclick="removeProduct(${index})"
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
   حذف منتج
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

    const totalQtyElement = document.getElementById("totalQuantity");
    if (totalQtyElement) {
        const totalQty = invoiceProducts.reduce((sum, item) => sum + item.quantity, 0);
        totalQtyElement.innerText = `${totalQty} وحدة`;
    }
}

/* ===========================
   تحديد السعر حسب نوع العميل
=========================== */
function getPrice(product) {
    if (window.customerType === "trade")
        return product.price_trade;

    if (window.customerType === "technical")
        return product.price_technician;

    if (window.customerType === "client")
        return product.price_customer;

    return product.price_base;
}

/* ===========================
   إشعارات
=========================== */
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