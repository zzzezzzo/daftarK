<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                                <i class="bi bi-pencil-square text-2xl"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold">تعديل الفئة</h1>
                                <p class="text-purple-100 text-sm">تعديل بيانات الفئة: {{ $category->name }}</p>
                            </div>
                        </div>
                        <a href="{{ route('categories.index') }}" 
                           class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-xl transition-all flex items-center gap-2">
                            <i class="bi bi-arrow-left"></i>
                            <span>رجوع</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-6 py-4 rounded-xl mb-6 flex items-start gap-3">
                    <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="bi bi-exclamation-triangle text-white text-sm"></i>
                    </div>
                    <div>
                        <p class="font-semibold mb-2">يرجى تصحيح الأخطاء التالية:</p>
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="flex items-start gap-2">
                                    <i class="bi bi-dot text-red-500"></i>
                                    <span>{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Main Form Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                <form id="categoryForm" action="{{ route('categories.update', $category->id) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')
                    
                    <!-- Category Info Card -->
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 p-6 rounded-xl border border-blue-200 dark:border-gray-500 mb-6">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                                <i class="bi bi-tag text-2xl text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $category->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $category->products_count ?? $category->products->count() }} منتج
                                    @if($category->priceRate)
                                        • لديه أسعار محددة
                                    @else
                                        • لا توجد أسعار محددة
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Basic Information -->
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 p-6 rounded-xl border border-blue-200 dark:border-gray-500">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                            <i class="bi bi-box text-blue-600"></i>
                            المعلومات الأساسية
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-gray-700 dark:text-gray-300 font-semibold items-center gap-2">
                                    <i class="bi bi-tag text-blue-600"></i>
                                    اسم الفئة
                                </label>
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        name="name" 
                                        id="name" 
                                        value="{{ old('name', $category->name) }}"
                                        class="w-full p-4 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white transition-all"
                                        required>
                                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                        <i class="bi bi-tag"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Section -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <i class="bi bi-info-circle"></i>
                            سيتم حفظ التغييرات تلقائياً
                        </div>
                        <div class="flex gap-3">
                            <button 
                                type="button" 
                                onclick="window.history.back()"
                                class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all flex items-center gap-2">
                                <i class="bi bi-x-lg"></i>
                                إلغاء
                            </button>
                            <button 
                                type="submit" 
                                class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-105 shadow-lg flex items-center gap-2">
                                <i class="bi bi-check-lg"></i>
                                حفظ التغييرات
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Handle form submission with AJAX
        document.getElementById('categoryForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const url = "{{ route('categories.update', $category->id) }}";
            
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'نجاح',
                        text: 'تم تعديل الفئة بنجاح',
                        confirmButtonText: 'موافق',
                        willClose: false
                    }).then(() => {
                        // Redirect to index page
                        window.location.href = "{{ route('categories.index') }}";
                    });
                } else {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: result.message || 'حدث خطأ ما',
                        confirmButtonText: 'موافق'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حدث خطأ في الاتصال',
                    confirmButtonText: 'موافق'
                });
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</x-app-layout>
