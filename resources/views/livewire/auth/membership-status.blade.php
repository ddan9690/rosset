<div class="max-w-md w-full mx-auto bg-white rounded-xl shadow-sm border border-slate-200 p-8 space-y-6 text-center" data-aos="fade-up">
    <!-- Logo & Brand Header -->
    <div class="space-y-3 flex flex-col items-center">
        <img src="{{ asset('images/logo.png') }}" alt="ROSSET-SWA Logo" class="w-16 h-16 object-contain">
        <div>
            <h2 class="text-base font-bold text-slate-800 tracking-tight">Rongo Sub County Teachers Welfare Association</h2>
            <p class="text-xs font-semibold text-emerald-700 tracking-wide mt-0.5">ROSSET-SWA</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium">
            {{ session('message') }}
        </div>
    @endif

    @php
        $status = $membershipReq->status ?? 'pending';
    @endphp

    <!-- Status Card -->
    <div class="space-y-4">
        @if($status === 'pending')
            <div class="p-5 rounded-xl bg-amber-50/70 border border-amber-200/80 space-y-2 text-center">
                <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-amber-100 text-amber-700 mb-1">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <h3 class="text-sm font-bold text-amber-900">Application Under Review</h3>
                <p class="text-xs text-amber-800/90 leading-relaxed capitalize-first">
                    Thank you for being part of ROSSET-SWA community. Your request has been received and is being processed. Kindly be patient and keep checking the page for further information regarding your request.
                </p>
            </div>
        @elseif($status === 'approved')
            <div class="p-5 rounded-xl bg-emerald-50/70 border border-emerald-200/80 space-y-2 text-center">
                <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 mb-1">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                </div>
                <h3 class="text-sm font-bold text-emerald-900">Application Approved</h3>
                <p class="text-xs text-emerald-800/90 leading-relaxed">
                    Your membership application has been successfully verified and approved by the administration. Please proceed to finalize your registration fee payment.
                </p>
            </div>
        @else
            <div class="p-5 rounded-xl bg-red-50/70 border border-red-200/80 space-y-2 text-center">
                <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-red-100 text-red-700 mb-1">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                </div>
                <h3 class="text-sm font-bold text-red-900">Application Update</h3>
                <p class="text-xs text-red-800/90 leading-relaxed">
                    Please contact the ROSSET welfare officials for further information regarding your membership request. Thank you.
                </p>
            </div>
        @endif
    </div>

    <!-- Actions -->
    <div class="space-y-3 pt-2">
        @if($status === 'approved')
            <a href="{{ route('register.fee') }}" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition shadow-sm">
                <i data-lucide="credit-card" class="w-4 h-4"></i>
                Proceed to Registration Fee Payment
            </a>
        @endif
    </div>
</div>