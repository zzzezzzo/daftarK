 document.addEventListener('DOMContentLoaded', function(){

    
    document.getElementById('addCategoryBtn').addEventListener('click', async () => {

            const {url, csrf} = window.categoryConfig
            const { value: name } = await Swal.fire({
            title: 'إضافة فئة جديدة',
            input: 'text',
            inputPlaceholder: 'اسم الفئة',
            showCancelButton: true,
            confirmButtonText: 'حفظ',
            cancelButtonText: 'إلغاء'
        });

        if (!name) return;

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name })
        });
        const data = await response.json();

        if (data.id) {
            const select = document.getElementById('category_id');
            const option = document.createElement('option');
            option.value = data.id;
            option.textContent = data.name;
            option.selected = true;
            select.appendChild(option);
            Swal.fire('تم', 'تمت إضافة الفئة بنجاح', 'success');
        }else{
            swal.fire({
                icon:"error",
                title: "Ooops..",
                text: "موجودة بالفعل"
            })
        }
    })
});