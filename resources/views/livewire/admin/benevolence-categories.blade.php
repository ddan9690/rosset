<div class="space-y-6">

    <!-- Header Section with Add Button -->
    <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl px-6 py-4 shadow-xs">
        <div>
            <h3 class="font-bold text-sm uppercase tracking-wider" style="color: #0E3A59;">Benevolence Categories</h3>
            <p class="text-xs text-slate-500 mt-0.5">Manage standard benevolence amounts for members.</p>
        </div>
        <button wire:click="openCreateModal" class="px-4 py-2.5 rounded-lg text-white font-bold text-xs uppercase tracking-wider transition shadow-xs bg-[#2EA3F2] hover:bg-sky-500 cursor-pointer flex items-center space-x-1">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Add Category</span>
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-lg font-medium flex items-center space-x-2">
            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Categories Table Section -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3 px-6">Category</th>
                        <th class="py-3 px-6">Amount</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-6 font-semibold text-slate-900">{{ $category->name }}</td>
                            <td class="py-3.5 px-6 font-mono font-bold text-emerald-600">KES {{ number_format($category->amount) }}</td>
                            <td class="py-3.5 px-6 text-right space-x-3">
                                <button wire:click="editCategory({{ $category->id }})" class="font-bold text-[#2EA3F2] hover:underline cursor-pointer">Edit</button>
                                <button type="button" onclick="confirmDelete({{ $category->id }})" class="font-bold text-red-600 hover:underline cursor-pointer">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-slate-400">No benevolence categories found. Click 'Add Category' to create one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200">
            {{ $categories->links() }}
        </div>
    </div>

    <!-- ================= CREATE / EDIT MODAL ================= -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl space-y-6 animate-in fade-in zoom-in duration-200">
                
                <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                    <h3 class="font-bold text-sm uppercase tracking-wider text-slate-900">
                        {{ $isEditing ? 'Edit Benevolence Category' : 'Create Benevolence Category' }}
                    </h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 text-sm font-bold cursor-pointer">✕</button>
                </div>

                <form wire:submit="saveCategory" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Category Name</label>
                        <input type="text" wire:model="name" placeholder="e.g. Self, Spouse, Child, Parent" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none bg-white">
                        @error('name') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Amount</label>
                        <input type="number" step="1" min="0" wire:model="amount" placeholder="e.g. 5000" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none bg-white">
                        @error('amount') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2.5 rounded-lg bg-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-300 transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="px-5 py-2.5 rounded-lg text-white font-bold text-xs uppercase tracking-wider transition shadow-xs bg-[#2EA3F2] hover:bg-sky-500 cursor-pointer flex items-center justify-center space-x-1.5">
                            <span wire:loading.remove wire:target="saveCategory">{{ $isEditing ? 'Update Category' : 'Save Category' }}</span>
                            <span wire:loading wire:target="saveCategory">Saving...</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

</div>

<script>
    document.addEventListener('livewire:navigated', () => { 
        lucide.createIcons(); 
    });
    document.addEventListener('DOMContentLoaded', () => { 
        lucide.createIcons(); 
    });

    function confirmDelete(categoryId) {
        Swal.fire({
            title: 'Are you absolutely sure?',
            text: 'Deleting this category will permanently delete all related contributions associated with it too! This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete everything!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('deleteCategory', categoryId);
            }
        });
    }
</script>