// // Customer Invoice Creation JavaScript
// let itemIndex = 1;

// // Initialize on page load
// document.addEventListener('DOMContentLoaded', function() {
//     console.log('Page loaded');
//     console.log('Customer type:', window.customerType);
//     console.log('Products:', window.products);
//     calculateTotals();
// });

// function updateProductPrice(select) {
//     const itemDiv = select.closest('.grid');
//     const index = select.name.match(/\d+/)[0];
//     const selectedOption = select.options[select.selectedIndex];
//     const stock = parseInt(selectedOption.dataset.stock);
    
//     // Update stock display
//     updateStockDisplay(index, stock);
    
//     // Set price based on customer type
//     let price = 0;
//     switch(window.customerType) {
//         case 'trade':
//             price = parseFloat(selectedOption.dataset.tradePrice);
//             break;
//         case 'technical':
//             price = parseFloat(selectedOption.dataset.technicalPrice);
//             break;
//         case 'client':
//             price = parseFloat(selectedOption.dataset.clientPrice);
//             break;
//         default:
//             price = parseFloat(selectedOption.dataset.basePrice);
//     }
    
//     // Find and update the unit price input (if it exists)
//     const unitPriceInput = itemDiv.querySelector('input[name*="unit_price"]');
//     if (unitPriceInput) {
//         unitPriceInput.value = price;
//     }
    
//     // Validate current quantity
//     const quantityInput = itemDiv.querySelector('input[name*="quantity"]');
//     if (quantityInput) {
//         validateStock(quantityInput);
//     }
    
//     // Calculate totals
//     calculateTotals();
// }

// function updateStockDisplay(index, stock) {
//     const warningDiv = document.getElementById(`stockWarning-${index}`);
//     if (warningDiv) {
//         const warningText = warningDiv.querySelector('.stock-warning-text');
//         warningText.textContent = `المتوفر في المخزون: ${stock} قطعة`;
//     }
// }

// function validateStock(input) {
//     const itemDiv = input.closest('.grid');
//     const index = input.name.match(/\d+/)[0];
//     const productSelect = itemDiv.querySelector('select[name*="product_id"]');
//     const warningDiv = document.getElementById(`stockWarning-${index}`);
    
//     if (!productSelect.value) return;
    
//     const selectedOption = productSelect.options[productSelect.selectedIndex];
//     const availableStock = parseInt(selectedOption.dataset.stock);
//     const requestedQuantity = parseInt(input.value);
    
//     if (requestedQuantity > availableStock) {
//         // Show warning and limit quantity
//         warningDiv.classList.remove('hidden');
//         warningDiv.querySelector('.stock-warning-text').textContent = 
//             `الكمية المطلوبة (${requestedQuantity}) تتجاوز المتوفر (${availableStock})`;
//         input.value = availableStock; // Auto-correct to available stock
//         input.classList.add('border-red-500');
//     } else {
//         // Hide warning
//         warningDiv.classList.add('hidden');
//         input.classList.remove('border-red-500');
//     }
// }

// function removeItem(button) {
//     button.closest('.grid').remove();
//     calculateTotals();
// }

// function addSingleItem() {
//     const wrapper = document.getElementById('itemsWrapper');
//     const newItem = document.createElement('div');
//     newItem.className = 'grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl';
//     newItem.innerHTML = `
//         <div class="w-full relative">
//             <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 items-center gap-2">
//                 <i class="bi bi-tag text-blue-600"></i>
//                 المنتج
//             </label>
//             <div class="relative">
//                 <select 
//                     name="products[${itemIndex}][product_id]" 
//                     class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all appearance-none"
//                     required
//                     onchange="updateProductPrice(this)">
//                     <option value="" disabled selected>اختر المنتج</option>
//                     ${window.products.map(product => `
//                         <option value="${product.id}" 
//                                 data-category="${product.category_id}"
//                                 data-base-price="${product.price_base}"
//                                 data-trade-price="${product.getPriceForCustomerType ? product.getPriceForCustomerType('trade') : product.price_base}"
//                                 data-technical-price="${product.getPriceForCustomerType ? product.getPriceForCustomerType('technical') : product.price_base}"
//                                 data-client-price="${product.getPriceForCustomerType ? product.getPriceForCustomerType('client') : product.price_base}"
//                                 data-stock="${product.stock}">
//                             ${product.name} (المتوفر: ${product.stock})
//                         </option>
//                     `).join('')}
//                 </select>
//                 <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none">
//                     <i class="bi bi-chevron-down"></i>
//                 </div>
//             </div>
//         </div>
//         <div class="w-full relative">
//             <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2 items-center gap-2">
//                 <i class="bi bi-123 text-blue-600"></i>
//                 الكمية
//             </label>
//             <div class="relative">
//                 <input 
//                     type="number" 
//                     name="products[${itemIndex}][quantity]" 
//                     value="1" 
//                     min="1" 
//                     class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all"
//                     required
//                     onchange="calculateItemTotal(this)"
//                     oninput="validateStock(this)">
//                 <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
//                     <i class="bi bi-123"></i>
//                 </div>
//             </div>
//             <div id="stockWarning-${itemIndex}" class="mt-1 text-sm text-red-600 dark:text-red-400 hidden">
//                 <i class="bi bi-exclamation-triangle"></i>
//                 <span class="stock-warning-text"></span>
//             </div>
//         </div>
//         <div class="w-full flex items-end gap-2">
//             <button type="button" onclick="this.closest('.grid').remove()" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all">
//                 <i class="bi bi-trash"></i>
//                 حذف
//             </button>
//         </div>
//     `;
//     wrapper.appendChild(newItem);
//     itemIndex++;
//     calculateTotals(); // Calculate total after adding new item
// }

// function calculateItemTotal(input) {
//     calculateTotals();
// }

// function calculateTotals() {
//     let total = 0;
//     const productSelects = document.querySelectorAll('select[name*="product_id"]');
    
//     productSelects.forEach((select, index) => {
//         if (select.value) {
//             const itemDiv = select.closest('.grid');
//             const quantityInput = itemDiv.querySelector('input[name*="quantity"]');
//             const selectedOption = select.options[select.selectedIndex];
            
//             // Get price based on customer type
//             let price = 0;
//             switch(window.customerType) {
//                 case 'trade':
//                     price = parseFloat(selectedOption.dataset.tradePrice) || 0;
//                     break;
//                 case 'technical':
//                     price = parseFloat(selectedOption.dataset.technicalPrice) || 0;
//                     break;
//                 case 'client':
//                     price = parseFloat(selectedOption.dataset.clientPrice) || 0;
//                     break;
//                 default:
//                     price = parseFloat(selectedOption.dataset.basePrice) || 0;
//             }
            
//             const quantity = parseInt(quantityInput.value) || 0;
//             total += price * quantity;
//         }
//     });
    
//     // Update total display
//     document.getElementById('totalAmount').textContent = total.toFixed(2) + ' ج.م';
// }

// // Form validation before submission
// document.addEventListener('submit', function(e) {
//     const form = e.target;
//     if (form.tagName === 'FORM' && form.querySelector('select[name*="product_id"]')) {
//         const quantityInputs = document.querySelectorAll('input[name*="quantity"]');
//         let hasStockError = false;
        
//         quantityInputs.forEach(input => {
//             const itemDiv = input.closest('.grid');
//             const productSelect = itemDiv.querySelector('select[name*="product_id"]');
            
//             if (productSelect.value) {
//                 const selectedOption = productSelect.options[productSelect.selectedIndex];
//                 const availableStock = parseInt(selectedOption.dataset.stock);
//                 const requestedQuantity = parseInt(input.value);
                
//                 if (requestedQuantity > availableStock) {
//                     hasStockError = true;
//                     input.classList.add('border-red-500');
//                 }
//             }
//         });
        
//         if (hasStockError) {
//             e.preventDefault();
//             alert('يرجى تصحيح كميات المنتجات لتتناسب مع المتوفر في المخزون');
//             return false;
//         }
//     }
// });
