<div class="space-y-6 max-w-3xl mx-auto">
    <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl px-6 py-4 shadow-xs">
        <div>
            <h3 class="font-bold text-sm uppercase tracking-wider" style="color: #0E3A59;">Edit Benevolence Case: {{ $case->case_number }}</h3>
            <p class="text-xs text-slate-500 mt-0.5">Update information for this benevolence case.</p>
        </div>
        <a href="{{ route('admin.benevolence.cases.index') }}" wire:navigate class="text-xs font-bold text-slate-600 hover:underline">&larr; Back to List</a>
    </div>

    <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-6">
        <form wire:submit="update" class="space-y-4 text-xs">
            <div>
                <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Affected Member</label>
                <select wire:model="user_id" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none bg-white">
                    @foreach($members as $member)
                        <option value="{{ $member->id }}">{{ $member->first_name }} {{ $member->last_name }} (TSC: {{ $member->tsc_number }})</option>
                    @endforeach
                </select>
                @error('user_id') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Benevolence Category</label>
                <select wire:model="benevolence_category_id" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none bg-white">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }} (KES {{ number_format($category->amount) }})</option>
                    @endforeach
                </select>
                @error('benevolence_category_id') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Case Details / Description</label>
                <textarea wire:model="case_details" rows="4" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none bg-white"></textarea>
                @error('case_details') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Contribution Deadline</label>
                    <input type="date" wire:model="deadline" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none bg-white">
                    @error('deadline') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Status</label>
                    <select wire:model="status" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#2EA3F2] focus:outline-none bg-white">
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                        <option value="closed">Closed</option>
                    </select>
                    @error('status') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4">
                <a href="{{ route('admin.benevolence.cases.index') }}" wire:navigate class="px-4 py-2.5 rounded-lg bg-slate-200 text-slate-700 font-bold hover:bg-slate-300 transition">Cancel</a>
                <button type="submit" wire:loading.attr="disabled" class="px-5 py-2.5 rounded-lg text-white font-bold uppercase tracking-wider transition shadow-xs bg-[#2EA3F2] hover:bg-sky-500 cursor-pointer">
                    <span wire:loading.remove>Update Case</span>
                    <span wire:loading>Updating...</span>
                </button>
            </div>
        </form>
    </div>
</div>