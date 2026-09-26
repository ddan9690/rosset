<div class="max-w-6xl mx-auto py-8 space-y-8">
    
    <!-- Upload Section Card (Always Visible) -->
    <div class="max-w-2xl mx-auto bg-white border border-slate-200 rounded-xl p-6 shadow-xs space-y-4">
        <div>
            <h2 class="text-base font-bold text-slate-900" style="color: #0E3A59;">Bulk Import Members</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Upload an Excel file containing columns: 
                <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-700">sn</code>, 
                <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-700">name</code>, 
                <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-700">school</code>, and 
                <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-700">phone</code>.
            </p>
        </div>

        @if (session()->has('message'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-lg font-medium flex items-center space-x-2">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 text-xs px-4 py-3 rounded-lg font-medium flex items-center space-x-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 flex-shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <form wire:submit="uploadMembers" class="space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Select Excel / CSV File</label>
                <input type="file" wire:model="excelFile" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-xl">
                @error('excelFile') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div wire:loading wire:target="excelFile" class="text-xs text-blue-600 font-medium">
                Uploading spreadsheet...
            </div>

            <button type="submit" wire:loading.attr="disabled" class="w-full py-3 px-4 rounded-xl text-white font-bold text-xs uppercase tracking-wider transition shadow-sm bg-slate-900 hover:bg-slate-800 cursor-pointer flex items-center justify-center space-x-2">
                <i data-lucide="upload" class="w-4 h-4" wire:loading.remove></i>
                <span wire:loading.remove>Process & Import Members</span>
                <span wire:loading>Importing data (Please wait)...</span>
            </button>
        </form>
    </div>

    <!-- Imported Members Directory List -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden space-y-4">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-sm uppercase tracking-wider" style="color: #0E3A59;">Imported Members Record</h3>
                <p class="text-xs text-slate-500">Live directory ordered by membership number.</p>
            </div>
            <span class="text-xs bg-blue-50 text-[#2EA3F2] border border-blue-200 font-bold px-3 py-1 rounded-full">
                Total: {{ $totalImported }} Members
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3 px-4">Membership No (SN)</th>
                        <th class="py-3 px-4">Member Name</th>
                        <th class="py-3 px-4">School</th>
                        <th class="py-3 px-4">Phone</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $user->membership_number ?? 'N/A' }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-800">{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $user->school ?? 'N/A' }}</td>
                            <td class="py-3 px-4 font-mono text-slate-600">{{ $user->phone }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $user->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                No members imported yet. Upload an Excel file above to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links Wrapper -->
        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>