<div class="flex flex-col min-h-screen bg-slate-50">

    <!-- Header Banner -->
    <section class="text-white py-12 lg:py-16 bg-slate-900 relative overflow-hidden" style="background-color: #0E3A59;">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/Rosset Welfare team members sitting at a conference table reviewing documents during an official organization meeting..jpg') }}" 
                 alt="ROSSET-SWA Dependants" 
                 class="w-full h-full object-cover filter brightness-40">
            <div class="absolute inset-0 bg-[#0E3A59]/90"></div>
        </div>

        <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
            <span class="text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full border border-sky-400/30 inline-block" style="background-color: rgba(46, 163, 242, 0.2); color: #2EA3F2;">
                Family & Next of Kin
            </span>
            <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
                Manage Your <span style="color: #2EA3F2;">Dependants</span>
            </h1>
            <p class="text-sm sm:text-base text-slate-200 max-w-xl mx-auto">
                Add or update your spouse, children, parents, and siblings for your welfare records.
            </p>
        </div>
    </section>

    <!-- Main Form Section -->
    <section class="py-12 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 w-full flex-grow">
        <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-6 sm:p-8 space-y-6">

            @if (session()->has('message'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-lg font-medium flex items-center space-x-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                    <span>{{ session('message') }}</span>
                </div>
            @endif

            <form wire:submit="saveDependants" class="space-y-8">
                
                <!-- Spouse Section -->
                <div class="space-y-4 pb-6 border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Spouse</h3>
                        <button type="button" wire:click="addSpouse" class="text-xs text-[#2EA3F2] font-semibold hover:underline flex items-center space-x-1 cursor-pointer">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i><span>Add Spouse</span>
                        </button>
                    </div>

                    @foreach($spouse as $index => $item)
                        <div class="flex items-center space-x-3 bg-slate-50 p-3 rounded-lg border border-slate-200">
                            <input type="text" wire:model="spouse.{{ $index }}.name" placeholder="Full Name" class="w-full text-xs border border-slate-200 rounded-lg p-2 bg-white text-slate-700">
                            <button type="button" wire:click="removeSpouse({{ $index }})" class="text-red-500 hover:text-red-700 p-1 flex-shrink-0">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    @endforeach
                    @if(empty($spouse))
                        <p class="text-xs text-slate-400 italic">No spouse added yet.</p>
                    @endif
                </div>

                <!-- Children Section -->
                <div class="space-y-4 pb-6 border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Children</h3>
                        <button type="button" wire:click="addChild" class="text-xs text-[#2EA3F2] font-semibold hover:underline flex items-center space-x-1 cursor-pointer">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i><span>Add Child</span>
                        </button>
                    </div>

                    @foreach($children as $index => $item)
                        <div class="flex items-center space-x-3 bg-slate-50 p-3 rounded-lg border border-slate-200">
                            <input type="text" wire:model="children.{{ $index }}.name" placeholder="Child's Full Name" class="w-full text-xs border border-slate-200 rounded-lg p-2 bg-white text-slate-700">
                            <button type="button" wire:click="removeChild({{ $index }})" class="text-red-500 hover:text-red-700 p-1 flex-shrink-0">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    @endforeach
                    @if(empty($children))
                        <p class="text-xs text-slate-400 italic">No children added yet.</p>
                    @endif
                </div>

                <!-- Parents Section -->
                <div class="space-y-4 pb-6 border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Parents</h3>
                        <button type="button" wire:click="addParent" class="text-xs text-[#2EA3F2] font-semibold hover:underline flex items-center space-x-1 cursor-pointer">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i><span>Add Parent</span>
                        </button>
                    </div>

                    @foreach($parents as $index => $item)
                        <div class="flex items-center space-x-3 bg-slate-50 p-3 rounded-lg border border-slate-200">
                            <input type="text" wire:model="parents.{{ $index }}.name" placeholder="Parent's Full Name" class="w-full text-xs border border-slate-200 rounded-lg p-2 bg-white text-slate-700">
                            <select wire:model="parents.{{ $index }}.relation" class="text-xs border border-slate-200 rounded-lg p-2 bg-white text-slate-700 flex-shrink-0">
                                <option value="Father">Father</option>
                                <option value="Mother">Mother</option>
                            </select>
                            <button type="button" wire:click="removeParent({{ $index }})" class="text-red-500 hover:text-red-700 p-1 flex-shrink-0">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    @endforeach
                    @if(empty($parents))
                        <p class="text-xs text-slate-400 italic">No parents added yet.</p>
                    @endif
                </div>

                <!-- Siblings Section -->
                <div class="space-y-4 pb-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">Siblings</h3>
                        <button type="button" wire:click="addSibling" class="text-xs text-[#2EA3F2] font-semibold hover:underline flex items-center space-x-1 cursor-pointer">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i><span>Add Sibling</span>
                        </button>
                    </div>

                    @foreach($siblings as $index => $item)
                        <div class="flex items-center space-x-3 bg-slate-50 p-3 rounded-lg border border-slate-200">
                            <input type="text" wire:model="siblings.{{ $index }}.name" placeholder="Sibling's Full Name" class="w-full text-xs border border-slate-200 rounded-lg p-2 bg-white text-slate-700">
                            <button type="button" wire:click="removeSibling({{ $index }})" class="text-red-500 hover:text-red-700 p-1 flex-shrink-0">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    @endforeach
                    @if(empty($siblings))
                        <p class="text-xs text-slate-400 italic">No siblings added yet.</p>
                    @endif
                </div>

                <!-- Action Button -->
                <div class="pt-4">
                    <button type="submit" wire:loading.attr="disabled" class="w-full py-3.5 px-6 rounded-xl text-white font-bold text-xs uppercase tracking-wider transition shadow-sm bg-slate-900 hover:bg-slate-800 cursor-pointer flex items-center justify-center space-x-2">
                        <span wire:loading.remove>Save Dependants</span>
                        <span wire:loading>Saving Changes...</span>
                    </button>
                </div>

            </form>
        </div>
    </section>

</div>

<script>
    document.addEventListener('livewire:navigated', () => {
        lucide.createIcons();
    });
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>