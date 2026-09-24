<div class="space-y-6">
    
    <!-- Stat Cards Grid (2 per row on mobile, 3 on desktop) -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        
        <!-- Total Members -->
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-xs border border-slate-200 flex items-center justify-between">
            <div>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">Total Registered Members</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold mt-1" style="color: #0E3A59;">148</h3>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center bg-blue-50 text-[#2EA3F2] flex-shrink-0">
                <i data-lucide="users" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </div>
        </div>

        <!-- Paid Members -->
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-xs border border-slate-200 flex items-center justify-between">
            <div>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">Fee Paid Members</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold mt-1 text-emerald-600">132</h3>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center bg-emerald-50 text-emerald-600 flex-shrink-0">
                <i data-lucide="check-circle" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-xs border border-slate-200 flex items-center justify-between col-span-2 lg:col-span-1">
            <div>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400">Pending Approvals</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold mt-1 text-amber-600">16</h3>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center bg-amber-50 text-amber-600 flex-shrink-0">
                <i data-lucide="clock" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </div>
        </div>

    </div>

    <!-- Recent Registrations Table Section (Compact & Horizontal Scroll) -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-bold text-sm uppercase tracking-wider" style="color: #0E3A59;">Recent Member Registrations</h3>
            <span class="text-xs font-bold text-slate-400">Static Preview Feed</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-2.5 px-4">Teacher Name</th>
                        <th class="py-2.5 px-4">TSC Number</th>
                        <th class="py-2.5 px-4">School</th>
                        <th class="py-2.5 px-4">Phone</th>
                        <th class="py-2.5 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 font-semibold text-slate-800">Mr. David Ouma</td>
                        <td class="py-3 px-4 text-slate-600 font-mono">1098234</td>
                        <td class="py-3 px-4 text-slate-600">Rongo Secondary School</td>
                        <td class="py-3 px-4 text-slate-600">0712345678</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                                Approved
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 font-semibold text-slate-800">Mrs. Beatrice Akinyi</td>
                        <td class="py-3 px-4 text-slate-600 font-mono">1145672</td>
                        <td class="py-3 px-4 text-slate-600">Kameji Mixed Secondary</td>
                        <td class="py-3 px-4 text-slate-600">0723456789</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800">
                                Pending
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 font-semibold text-slate-800">Mr. Kevin Otieno</td>
                        <td class="py-3 px-4 text-slate-600 font-mono">1209845</td>
                        <td class="py-3 px-4 text-slate-600">Kanga High School</td>
                        <td class="py-3 px-4 text-slate-600">0734567890</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                                Approved
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>