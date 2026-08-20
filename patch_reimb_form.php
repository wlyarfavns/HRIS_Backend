<?php
$file = 'd:/WILLY ARIF AVINES/MAGANG/HRIS_FIX/hris_system/resources/views/hr/persetujuan/reimbursement.blade.php';
$content = file_get_contents($file);

$target = <<<'EOT'
              <div class="flex items-center gap-3">
                  <div class="relative">
                      <input type="text" id="searchClaim" placeholder="Cari nama atau kategori..."
                             class="w-64 pl-10 pr-4 py-2.5 bg-white rounded-md text-sm border border-gray-200
                                    focus:outline-none focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] transition"
                             onkeydown="if(event.key==='Enter'){window.location.href='{{ route('hr.approvals.reimbursement') }}?q='+encodeURIComponent(this.value)}">
                  </div>
              </div>
EOT;

$replacement = <<<'EOT'
              <form method="GET" action="{{ route('hr.approvals.reimbursement') }}" class="flex items-center gap-3">
                  <div class="relative">
                      <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 material-symbols-outlined text-[20px]">search</span>
                      <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau kategori..."
                             class="w-64 pl-10 pr-4 py-2.5 bg-white rounded-md text-sm border border-gray-200
                                    focus:outline-none focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] transition"
                             onkeydown="if(event.key==='Enter'){this.form.submit();}">
                  </div>
              </form>
EOT;

$content = str_replace($target, $replacement, $content);
file_put_contents($file, $content);
echo "Reimbursement search form updated.";
