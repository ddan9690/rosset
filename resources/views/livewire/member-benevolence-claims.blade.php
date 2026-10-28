<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6"
     x-data="{}"
     @swal:success.window="Swal.fire({ title: 'Success!', text: $event.detail[0].message, icon: 'success', confirmButtonColor: '#0E3A59', confirmButtonText: 'OK' })"
     @swal:pending-claim.window="Swal.fire({ title: $event.detail[0].title, text: $event.detail[0].message, icon: 'info', confirmButtonColor: '#0E3A59', confirmButtonText: 'Understood' })">

    <!-- Top Header & Navigation -->
    <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl px-6 py-4 shadow-xs">
        <div>
            <h1 class="text-base font-bold text-slate-900">My Benevolence Claims</h1>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('portal') }}" wire:navigate class="text-xs font-bold text-slate-600 hover:underline">
                &larr; Back to Portal
            </a>
            <button type="button" wire:click="openClaimModal"
                class="px-4 py-2 bg-gradient-to-r from-sky-500 to-[#2EA3F2] hover:from-sky-600 hover:to-sky-500 text-white text-xs font-extrabold uppercase tracking-wider rounded-xl shadow-md transition flex items-center space-x-1.5 cursor-pointer">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Submit New Claim</span>
            </button>
        </div>
    </div>

    <!-- Tailwind Flash Error Notice -->
    @if (session()->has('error'))
        <div class="bg-amber-50 border border-amber-300 text-amber-900 text-xs px-5 py-4 rounded-xl font-medium flex items-start space-x-3 shadow-sm">
            <i data-lucide="info" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5"></i>
            <div class="space-y-1">
                <strong class="font-bold uppercase tracking-wider text-[11px] block text-amber-900">Constitutional Eligibility Notice</strong>
                <p class="leading-relaxed">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Claims Table View -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="font-bold text-sm uppercase tracking-wider text-slate-800">Claim History</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/70 border-b border-slate-200 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                    <tr>
                        <th class="py-3 px-5">Claim No.</th>
                        <th class="py-3 px-5">Date</th>
                        <th class="py-3 px-5">Description</th>
                        <th class="py-3 px-5">Status</th>
                        <th class="py-3 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($claims as $claim)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-5 whitespace-nowrap font-bold font-mono text-slate-900">
                                {{ $claim->claim_number }}
                            </td>
                            <td class="py-4 px-5 whitespace-nowrap font-mono text-slate-500">
                                {{ $claim->created_at->setTimezone('Africa/Nairobi')->format('d-m-y') }}
                            </td>
                            <td class="py-4 px-5 max-w-md leading-relaxed">
                                {{ $claim->description }}
                            </td>
                            <td class="py-4 px-5 whitespace-nowrap">
                                @if($claim->status === 'approved')
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Approved
                                    </span>
                                @elseif($claim->status === 'rejected')
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-800 border border-red-200">
                                        Rejected
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">
                                        Pending Review
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-right whitespace-nowrap">
                                @if($claim->status === 'pending')
                                    <button type="button" 
                                            @click="Swal.fire({
                                                title: 'Withdraw Claim?',
                                                text: 'Are you sure you want to withdraw claim {{ $claim->claim_number }}?',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#ef4444',
                                                cancelButtonColor: '#64748b',
                                                confirmButtonText: 'Yes, Withdraw'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.withdrawClaim({{ $claim->id }});
                                                }
                                            })"
                                            class="px-3 py-1 bg-red-50 hover:bg-red-100 text-red-700 font-bold rounded-lg border border-red-200 transition cursor-pointer">
                                        Withdraw
                                    </button>
                                @else
                                    <span class="text-slate-400 font-medium">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                You have not submitted any benevolence claims yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($claims->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $claims->links() }}
            </div>
        @endif
    </div>

    <!-- ================= SUBMIT CLAIM MODAL ================= -->
    @if($showClaimModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 border border-slate-200 animate-in fade-in zoom-in-95 duration-200">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-extrabold text-sm uppercase tracking-wider text-slate-900">Submit Benevolence Claim</h3>
                    </div>
                    <button type="button" wire:click="closeClaimModal" class="text-slate-400 hover:text-slate-600 font-bold cursor-pointer text-sm p-1">✕</button>
                </div>

                <form wire:submit.prevent="saveClaim" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-700 mb-1">Claim Description / Reason <span class="text-red-500">*</span></label>
                        <textarea wire:model="description" rows="5" placeholder="Describe the nature of claim and bereavements..."
                                  class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-slate-900 bg-white focus:ring-2 focus:ring-[#2EA3F2] focus:outline-hidden"></textarea>
                        @error('description') <span class="text-red-500 text-[10px] mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="closeClaimModal" class="px-4 py-2.5 rounded-lg bg-slate-200 text-slate-700 font-bold hover:bg-slate-300 transition cursor-pointer">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-5 py-2.5 rounded-lg text-white font-bold uppercase tracking-wider transition shadow-xs bg-[#0E3A59] hover:bg-slate-900 cursor-pointer">
                            <span wire:loading.remove wire:target="saveClaim">Submit Claim</span>
                            <span wire:loading wire:target="saveClaim">Submitting...</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:navigated', () => { if (window.lucide) lucide.createIcons(); });
    document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons(); });
</script>