<div class="min-h-screen bg-slate-50 py-8" x-data="{ 
    scrollToSection(id) {
        const el = document.getElementById(id);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            el.classList.add('ring-2', 'ring-[#0E3A59]', 'transition-all', 'duration-500');
            setTimeout(() => {
                el.classList.remove('ring-2', 'ring-[#0E3A59]');
            }, 1500);
        }
    }
}">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Top Navigation / Back Bar -->
        <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl px-6 py-3 shadow-2xs">
            <a href="{{ route('portal') }}" wire:navigate
                class="text-xs font-bold text-slate-600 hover:text-slate-900 hover:underline flex items-center space-x-1">
                <span>&larr; Back to Portal</span>
            </a>

            <div class="text-xs font-mono font-semibold text-slate-500 uppercase tracking-wider">
                5th Edition (2021)
            </div>
        </div>

        <!-- Document Header Card -->
        <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-xs text-center space-y-4">
            <div class="flex justify-center">
                <img src="{{ asset('images/logo.png') }}" alt="ROSSET-SWA Logo" class="h-16 w-auto object-contain">
            </div>

            <div class="space-y-2">
                <h1 class="text-xl font-extrabold text-slate-900 uppercase tracking-tight">
                    Rongo Sub County Secondary Teachers Social Welfare Association
                </h1>
                <p class="text-xs font-bold text-[#0E3A59] uppercase tracking-widest font-mono">
                    ROSSET–SWA Constitution
                </p>
                <p class="text-xs text-slate-500 italic">
                    &ldquo;Coming together for unity and support&rdquo; | Email: rongosubcountyswa@gmail.com
                </p>
            </div>
        </div>

        <!-- Table of Contents / Navigation Toolbar -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-6 shadow-xs space-y-3">
            <div>
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700">Table of Contents</h3>
            </div>

            <div class="flex flex-wrap gap-1.5 pt-2 border-t border-slate-100">
                <button @click="scrollToSection('sec-preamble')" class="px-3 py-1.5 bg-slate-100 hover:bg-[#0E3A59] hover:text-white text-slate-700 rounded-lg text-xs font-medium transition-colors">Preamble</button>
                <button @click="scrollToSection('sec-ch1')" class="px-3 py-1.5 bg-slate-100 hover:bg-[#0E3A59] hover:text-white text-slate-700 rounded-lg text-xs font-medium transition-colors">Chapter 1: Name</button>
                <button @click="scrollToSection('sec-ch2')" class="px-3 py-1.5 bg-slate-100 hover:bg-[#0E3A59] hover:text-white text-slate-700 rounded-lg text-xs font-medium transition-colors">Chapter 2: Membership</button>
                <button @click="scrollToSection('sec-ch3')" class="px-3 py-1.5 bg-slate-100 hover:bg-[#0E3A59] hover:text-white text-slate-700 rounded-lg text-xs font-medium transition-colors">Chapter 3: Termination</button>
                <button @click="scrollToSection('sec-ch4')" class="px-3 py-1.5 bg-slate-100 hover:bg-[#0E3A59] hover:text-white text-slate-700 rounded-lg text-xs font-medium transition-colors">Chapter 4: Management</button>
                <button @click="scrollToSection('sec-ch5')" class="px-3 py-1.5 bg-slate-100 hover:bg-[#0E3A59] hover:text-white text-slate-700 rounded-lg text-xs font-medium transition-colors">Chapter 5: Benevolence</button>
                <button @click="scrollToSection('sec-ch6-13')" class="px-3 py-1.5 bg-slate-100 hover:bg-[#0E3A59] hover:text-white text-slate-700 rounded-lg text-xs font-medium transition-colors">Chapters 6–13: General</button>
            </div>
        </div>

        <!-- Main Constitution Body Card -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-10 shadow-xs space-y-8 text-slate-800 text-xs sm:text-sm leading-relaxed">

            <!-- Preamble & Main Objectives -->
            <section id="sec-preamble" class="space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-[#0E3A59] border-b border-slate-200 pb-2">
                    <span>Preamble & Main Objectives</span>
                </h2>
                <p>
                    As from the 12th day of October 2021, there shall be established the <strong>Rongo Sub County Secondary Teachers Social Welfare Association (ROSSET–SWA)</strong>. This is a benevolent group whose primary objective is to assist members during times of bereavement. The existence, management, and operations of this Association shall be guided by this Constitution.
                </p>

                <div class="space-y-2 pt-2">
                    <h3 class="font-bold text-slate-900">Main Objectives:</h3>
                    <ul class="list-decimal pl-5 space-y-1.5 text-slate-700">
                        <li>To promote the wellbeing of members and/or eligible dependents of a principal member (as per Chapter Five) through benevolence and without prejudice.</li>
                        <li>To obtain, raise, collect, and receive money from members’ contributions or any other legal means to assist members and/or eligible dependents during bereavements.</li>
                        <li>To promote unity of purpose among member teachers.</li>
                        <li>To develop and maintain solidarity among teachers.</li>
                    </ul>
                </div>
            </section>

            <!-- Chapter One -->
            <section id="sec-ch1" class="space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-[#0E3A59] border-b border-slate-200 pb-2">
                    <span>Chapter One: Name of the Association</span>
                </h2>
                <div>
                    <p>The name of the welfare association shall be Rongo Sub County Secondary Teachers Social Welfare Association (ROSSET–SWA).</p>
                </div>
            </section>

            <!-- Chapter Two -->
            <section id="sec-ch2" class="space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-[#0E3A59] border-b border-slate-200 pb-2">
                    <span>Chapter Two: Membership</span>
                </h2>
                <div class="space-y-3">
                    <div>
                        <p class="font-semibold text-slate-700 mb-1">Membership Composition:</p>
                        <ul class="list-disc pl-5 space-y-1 text-slate-700 mb-3">
                            <li>All registered secondary school teachers working in Rongo Sub County.</li>
                            <li>All actively contributing secondary school teachers who joined the welfare while working within Rongo Sub County and later got transferred, interdicted, or retired.</li>
                        </ul>

                        <p class="font-semibold text-slate-700 mb-1">Membership Conditions:</p>
                        <ol class="list-decimal pl-5 space-y-1.5 text-slate-700">
                            <li>There shall be a non-refundable annual registration fee of <strong>Ksh 150</strong> for office operations and facilitation during bereavements. All members must register by the end of February of every financial year. Failure to do so constitutes defaulting.</li>
                            <li>One is considered a member after paying the registration fee and having their details captured in the membership register. Members are encouraged to register online.</li>
                            <li>Late registration after 28th February requires a 30-day waiting period before benefiting from the scheme. Such members will not clear arrears for previous cases.</li>
                            <li>Any person found to have joined the welfare irregularly shall receive a regret letter and their registration fee shall be refunded.</li>
                        </ol>
                    </div>
                </div>
            </section>

            <!-- Chapter Three -->
            <section id="sec-ch3" class="space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-[#0E3A59] border-b border-slate-200 pb-2">
                    <span>Chapter Three: Termination of Membership & Readmission</span>
                </h2>
                <div class="space-y-2">
                    <p class="font-semibold text-slate-700">Membership may be terminated on the following grounds:</p>
                    <ul class="list-disc pl-5 space-y-1.5 text-slate-700">
                        <li><strong>Voluntary Withdrawal:</strong> A member fills an official deregistration form (verbal deregistration is not allowed).</li>
                        <li><strong>Death</strong> of a member.</li>
                        <li><strong>Gross Misconduct:</strong> After a fair hearing and determination by the Welfare Council.</li>
                        <li><strong>Defaulting:</strong> Failure to contribute in any bereavement case. A defaulter shall serve a 90-day waiting period after clearing all arrears in double.</li>
                    </ul>
                    <p class="pt-2 text-slate-700">
                        <strong>Readmission:</strong> A member may be readmitted only after clearing all arrears at once. Members shall be notified via official WhatsApp regarding all readmissions.
                    </p>
                </div>
            </section>

            <!-- Chapter Four -->
            <section id="sec-ch4" class="space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-[#0E3A59] border-b border-slate-200 pb-2">
                    <span>Chapter Four: Management & Officials</span>
                </h2>
                <div class="space-y-3 text-slate-700">
                    <p><strong>1. Executive Officials:</strong> Chairperson, Secretary, and Treasurer.</p>
                    <p><strong>2. Other Officials:</strong> Patrons (Serving BGC Rongo & Rongo Sub County KESSHA Chair), Communication/ICT Director, Zonal Representatives (Central, South, North - two per zone, gender-inclusive), School Representatives (one per school), JSS Representative, and Persons with Disability Representative.</p>
                    <p><strong>3. Welfare Council:</strong> Consists of all Executive Officials, Patrons, Communication/ICT Director, and all Sector/Zonal/School Representatives.</p>
                    <p><strong>4. Tenure & Transfer:</strong> All officials serve for three (3) years, eligible for one re-election. All elected officials must be teaching within the sub-county; if an official transfers, the seat becomes vacant and a by-election shall be held at the next AGM (Effective from AGM 2024).</p>
                    <p><strong>5. Financial Year:</strong> Runs from 1st January to 31st December.</p>
                </div>
            </section>

            <!-- Chapter Five -->
            <section id="sec-ch5" class="space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-[#0E3A59] border-b border-slate-200 pb-2">
                    <span>Chapter Five: Benevolence and Contribution</span>
                </h2>
                <div class="space-y-3 text-slate-700">
                    <div>
                        <strong class="text-slate-900 block font-bold mb-1">Eligible Cases:</strong>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Principal contributor (Teacher)</li>
                            <li>Legal spouse(s)</li>
                            <li>Biological or legally adopted children (live births only; miscarriages/stillbirths excluded)</li>
                            <li>Biological or foster parents (foster parents must be verifiable and captured at registration; one male and one female allowed)</li>
                        </ul>
                    </div>

                    <div class="pt-2">
                        <strong class="text-slate-900 block font-bold mb-2">Contribution Per Case Schedule:</strong>
                        <div class="overflow-x-auto border border-slate-200 rounded-lg">
                            <table class="w-full text-left">
                                <thead class="bg-slate-50 border-b border-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                    <tr>
                                        <th class="px-4 py-2.5">Loss Category</th>
                                        <th class="px-4 py-2.5">Contribution Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-mono text-xs">
                                    <tr>
                                        <td class="px-4 py-2.5 font-sans font-semibold text-slate-800">Principal Contributor (Teacher)</td>
                                        <td class="px-4 py-2.5 font-bold text-slate-900">Ksh 200</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2.5 font-sans font-semibold text-slate-800">Legal Spouse(s)</td>
                                        <td class="px-4 py-2.5 font-bold text-slate-900">Ksh 200</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2.5 font-sans font-semibold text-slate-800">Biological/Adopted Child(ren)</td>
                                        <td class="px-4 py-2.5 font-bold text-slate-900">Ksh 100</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2.5 font-sans font-semibold text-slate-800">Biological/Foster Parents</td>
                                        <td class="px-4 py-2.5 font-bold text-slate-900">Ksh 100</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 italic pt-1">
                        * All dependents must be declared at registration. Changes after nomination are not allowed. Late contributions from defaulters after case closure are remitted at the end of the financial year.
                    </p>
                </div>
            </section>

            <!-- Chapters Six to Thirteen -->
            <section id="sec-ch6-13" class="space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-[#0E3A59] border-b border-slate-200 pb-2">
                    <span>Chapters Six to Thirteen: Funds, Records, Meetings & Dissolution</span>
                </h2>
                <div class="space-y-2.5 text-slate-700">
                    <p><strong>Chapter Six (Source of Funds):</strong> Members’ registration fee (Ksh 150), donations from well-wishers, and AGM contribution fee (Ksh 150).</p>
                    <p><strong>Chapter Seven (Record Keeping):</strong> Hard-copy register kept by Secretary; soft copy by Communication Director. Notices communicated via SMS; contributions made within five (5) days. Financial report issued after every case.</p>
                    <p><strong>Chapter Eight (Elections):</strong> Officials serve for 3 years; elected by simple majority. Can be removed by a 2/3 majority via petition during an SGM.</p>
                    <p><strong>Chapter Nine (Management of Contributions):</strong> Made through the Treasurer. Next of kin receives support upon a member's death. Updates shared via WhatsApp or Telegram.</p>
                    <p><strong>Chapter Ten (Meetings):</strong> AGM held every October (2 weeks prior notice). SGM convened within 14 days upon request by members. Each member contributes Ksh 150 for the AGM.</p>
                    <p><strong>Chapters Eleven, Twelve & Thirteen (Legality, Amendment & Dissolution):</strong> Binding upon approval. Amended during AGM by simple majority. Dissolution supported by 2/3 of registered members in a Special Meeting.</p>
                </div>
            </section>

        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:navigated', () => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush