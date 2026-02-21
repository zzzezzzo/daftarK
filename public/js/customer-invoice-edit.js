let selectedProduct = null;
let invoiceProducts = [];

/* ===========================
    تحميل المنتجات القديمة
=========================== */
document.addEventListener("DOMContentLoaded", function () {

    if (window.invoiceItems) {
        window.invoiceItems.forEach(item => {
            const product = window.products.find(p => p.id == item.product_id);

            invoiceProducts.push({
                id: item.product_id,
                name: product ? product.name : "منتج",
                price: parseFloat(item.unit_price),
                quantity: parseInt(item.quantity),
                total: parseFloat(item.unit_price) * parseInt(item.quantity)
            });
        });
        

        renderProducts();
        updateTotal();
        
        
    }
});

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
        p.name.toLowerCase().includes(query)
    );

    box.innerHTML = "";

    filtered.forEach(product => {
        const div = document.createElement("div");
        div.className = "p-2 hover:bg-gray-100 cursor-pointer";

        div.innerHTML = product.name;

        div.onclick = function () {
            selectedProduct = product;
            document.getElementById("productSearch").value = product.name;
            document.getElementById("productPrice").value = getPrice(product);
            box.classList.add("hidden");
        };

        box.appendChild(div);
    });

    box.classList.remove("hidden");
});

/* ===========================
   إضافة المنتج
=========================== */
function addProductToInvoice() {

    if (!selectedProduct) {
        alert("اختر منتج");
        return;
    }

    const qty = parseInt(document.getElementById("productQty").value);
    const price = getPrice(selectedProduct);

    invoiceProducts.push({
        id: selectedProduct.id,
        name: selectedProduct.name,
        price: price,
        quantity: qty,
        total: price * qty
    });

    selectedProduct = null;
    document.getElementById("productSearch").value = "";
    document.getElementById("productPrice").value = "";
    document.getElementById("productQty").value = 1;

    renderProducts();
    updateTotal();
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
   تحديد السعر
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