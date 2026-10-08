<div class="min-h-screen bg-slate-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Top Navigation / Back Bar -->
        <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl px-6 py-3 shadow-2xs">
            <a href="{{ route('portal') }}" wire:navigate
                class="text-xs font-bold text-slate-600 hover:text-slate-900 hover:underline flex items-center space-x-1">
                <span>&larr; Back to Portal</span>
            </a>

            <div class="text-xs font-mono font-semibold text-slate-500 uppercase tracking-wider">
                ROSSET-SWA Portal
            </div>
        </div>

        <!-- Document Header Card -->
        <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-xs text-center space-y-4">
            <div class="flex justify-center">
                <img src="{{ asset('images/logo.png') }}" alt="ROSSET-SWA Logo" class="h-16 w-auto object-contain">
            </div>

            <div class="space-y-2">
                <h1 class="text-xl font-extrabold text-slate-900 uppercase tracking-tight">
                    Manage Eligible Dependents
                </h1>
                <p class="text-xs font-bold text-[#0E3A59] uppercase tracking-widest font-mono">
                    Member Dependents Record
                </p>
            </div>
        </div>

        @if(session()->has('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <div class="space-y-6">

            <!-- 1. Legal Spouse Section (Multiple) -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 space-y-4 shadow-xs">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900">Legal Spouse(s)</h3>
                    @if(!$showSpouseInput)
                        <button type="button" wire:click="$set('showSpouseInput', true)" class="px-3 py-1.5 bg-[#0E3A59] text-white rounded-lg text-xs font-medium hover:bg-slate-800 transition">
                            + Add Spouse
                        </button>
                    @endif
                </div>

                <div class="space-y-3">
                    @foreach($spouses as $index => $spouse)
                        <div class="flex items-center justify-between bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <span class="text-xs font-semibold text-slate-800">{{ $spouse['name'] }}</span>
                            <button type="button" wire:click="removeSpouse({{ $index }})" class="text-xs text-rose-600 font-bold hover:underline">Remove</button>
                        </div>
                    @endforeach

                    @if($showSpouseInput)
                        <div class="flex gap-2 items-center bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <input type="text" wire:model="spouseName" placeholder="Spouse Full Name" class="flex-1 border border-slate-300 rounded-lg text-xs p-2 bg-white">
                            <button type="button" wire:click="saveSpouse" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-medium hover:bg-emerald-700 transition">
                                Save
                            </button>
                            <button type="button" wire:click="$set('showSpouseInput', false)" class="px-3 py-2 bg-slate-200 text-slate-700 rounded-lg text-xs font-medium hover:bg-slate-300 transition">
                                Cancel
                            </button>
                        </div>
                        @error('spouseName') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    @endif

                    @if(empty($spouses) && !$showSpouseInput)
                        <p class="text-xs text-slate-400 italic">No spouse declared.</p>
                    @endif
                </div>
            </div>

            <!-- 2. Biological or Foster Parents Section -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 space-y-4 shadow-xs">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900">Biological / Foster Parents</h3>
                    @if(count($parents) < 2 && !$showParentInput)
                        <button type="button" wire:click="$set('showParentInput', true)" class="px-3 py-1.5 bg-[#0E3A59] text-white rounded-lg text-xs font-medium hover:bg-slate-800 transition">
                            + Add Parent
                        </button>
                    @endif
                </div>

                <div class="space-y-3">
                    @foreach($parents as $index => $parent)
                        <div class="flex items-center justify-between bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <span class="text-xs font-semibold text-slate-800">{{ $parent['name'] }}</span>
                            <button type="button" wire:click="removeParent({{ $index }})" class="text-xs text-rose-600 font-bold hover:underline">Remove</button>
                        </div>
                    @endforeach

                    @if($showParentInput)
                        <div class="flex gap-2 items-center bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <input type="text" wire:model="parentName" placeholder="Parent Full Name" class="flex-1 border border-slate-300 rounded-lg text-xs p-2 bg-white">
                            <button type="button" wire:click="saveParent" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-medium hover:bg-emerald-700 transition">
                                Save
                            </button>
                            <button type="button" wire:click="$set('showParentInput', false)" class="px-3 py-2 bg-slate-200 text-slate-700 rounded-lg text-xs font-medium hover:bg-slate-300 transition">
                                Cancel
                            </button>
                        </div>
                        @error('parentName') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    @endif

                    @if(empty($parents) && !$showParentInput)
                        <p class="text-xs text-slate-400 italic">No parents declared.</p>
                    @endif
                </div>
            </div>

            <!-- 3. Biological or Adopted Children Section (Numbered) -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 space-y-4 shadow-xs">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900">Biological or Adopted Children</h3>
                    @if(!$showChildInput)
                        <button type="button" wire:click="$set('showChildInput', true)" class="px-3 py-1.5 bg-[#0E3A59] text-white rounded-lg text-xs font-medium hover:bg-slate-800 transition">
                            + Add Child
                        </button>
                    @endif
                </div>

                <div class="space-y-3">
                    @foreach($children as $index => $child)
                        <div class="flex items-center justify-between bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <span class="text-xs font-semibold text-slate-800">
                                <span class="text-slate-400 mr-2">{{ $index + 1 }}.</span> {{ $child['name'] }}
                            </span>
                            <button type="button" wire:click="removeChild({{ $index }})" class="text-xs text-rose-600 font-bold hover:underline">Remove</button>
                        </div>
                    @endforeach

                    @if($showChildInput)
                        <div class="flex gap-2 items-center bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <input type="text" wire:model="childName" placeholder="Child Full Name" class="flex-1 border border-slate-300 rounded-lg text-xs p-2 bg-white">
                            <button type="button" wire:click="saveChild" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-medium hover:bg-emerald-700 transition">
                                Save
                            </button>
                            <button type="button" wire:click="$set('showChildInput', false)" class="px-3 py-2 bg-slate-200 text-slate-700 rounded-lg text-xs font-medium hover:bg-slate-300 transition">
                                Cancel
                            </button>
                        </div>
                        @error('childName') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    @endif

                    @if(empty($children) && !$showChildInput)
                        <p class="text-xs text-slate-400 italic">No children declared.</p>
                    @endif
                </div>
            </div>

        </div>

    </div>
</div>