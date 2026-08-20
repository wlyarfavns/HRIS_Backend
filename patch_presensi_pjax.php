<?php
$file = 'd:/WILLY ARIF AVINES/MAGANG/HRIS_FIX/hris_system/resources/views/hr/presensi/index.blade.php';
$content = file_get_contents($file);

// 1. Wrap the entire content in <div id="presensi-wrapper">
if (!str_contains($content, 'id="presensi-wrapper"')) {
    $content = str_replace("@section('content')\n", "@section('content')\n<div id=\"presensi-wrapper\">\n", $content);
    $content = str_replace("\n@endsection", "\n</div>\n@endsection", $content);
}

// 2. Add PJAX fetch logic to the form
$targetForm = <<<'EOT'
            <form method="GET" action="{{ route('hr.attendance.index') }}"
                class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap bg-gray-50/50">
EOT;

$replacementForm = <<<'EOT'
            <form method="GET" action="{{ route('hr.attendance.index') }}"
                class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap bg-gray-50/50"
                @submit.prevent="
                    let form = $event.target;
                    let url = new URL(form.action);
                    let formData = new FormData(form);
                    for(let [k,v] of formData.entries()) url.searchParams.set(k,v);
                    
                    document.getElementById('presensi-wrapper').style.opacity = '0.5';
                    document.getElementById('presensi-wrapper').style.pointerEvents = 'none';

                    fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(res => res.text())
                        .then(html => {
                            let doc = new DOMParser().parseFromString(html, 'text/html');
                            let newWrapper = doc.getElementById('presensi-wrapper');
                            if (newWrapper) {
                                document.getElementById('presensi-wrapper').outerHTML = newWrapper.outerHTML;
                            } else {
                                window.location.href = url.toString();
                            }
                        }).catch(() => {
                            window.location.href = url.toString();
                        });
                ">
EOT;

if (str_contains($content, $targetForm)) {
    $content = str_replace($targetForm, $replacementForm, $content);
    echo "Added PJAX form intercept to presensi.\n";
} else {
    echo "Form target not found in presensi.\n";
}

file_put_contents($file, $content);
