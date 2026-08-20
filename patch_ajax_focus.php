<?php
$files = [
    'd:/WILLY ARIF AVINES/MAGANG/HRIS_FIX/hris_system/resources/views/layouts/hr.blade.php',
    'd:/WILLY ARIF AVINES/MAGANG/HRIS_FIX/hris_system/resources/views/layouts/admin.blade.php'
];

$script = <<<'EOT'
<script id="ajax-search-script">
function initAjaxForms() {
    const filterForms = document.querySelectorAll('main form[method="GET"]');
    
    filterForms.forEach(form => {
        // Remove existing listener to prevent duplicates if re-initialized
        const newForm = form.cloneNode(true);
        form.parentNode.replaceChild(newForm, form);
        
        newForm.addEventListener('submit', function(e) {
            e.preventDefault();
            fetchResults(newForm);
        });

        const inputs = newForm.querySelectorAll('select, input');
        inputs.forEach(input => {
            if(input.hasAttribute('onchange')) {
                input.removeAttribute('onchange');
                input.addEventListener('change', () => fetchResults(newForm));
            }
            if(input.hasAttribute('onkeydown')) {
                const oldOnKeyDown = input.getAttribute('onkeydown');
                if (oldOnKeyDown.includes('submit')) {
                    input.removeAttribute('onkeydown');
                    input.addEventListener('keydown', (e) => {
                        if(e.key === 'Enter') {
                            e.preventDefault();
                            fetchResults(newForm);
                        }
                    });
                }
            }
            
            // New auto-search handler
            if(input.hasAttribute('data-auto-search') || input.name === 'search') {
                input.addEventListener('input', function() {
                    clearTimeout(this.delay);
                    this.delay = setTimeout(() => {
                        fetchResults(newForm);
                    }, 400);
                });
            }
        });
    });
}

function fetchResults(form) {
    const url = new URL(form.action || window.location.href);
    const formData = new FormData(form);
    const searchParams = new URLSearchParams();
    
    for (const [key, value] of formData.entries()) {
        if(value) searchParams.append(key, value);
    }
    
    url.search = searchParams.toString();

    // Save focus state before fetch
    const activeElement = document.activeElement;
    const focusState = activeElement && activeElement.name ? {
        name: activeElement.name,
        selectionStart: activeElement.selectionStart,
        selectionEnd: activeElement.selectionEnd
    } : null;

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        const currentMain = document.querySelector('main');
        const newMain = doc.querySelector('main');
        
        if (currentMain && newMain) {
            currentMain.innerHTML = newMain.innerHTML;
            
            // Re-initialize AJAX forms inside the new main content
            initAjaxForms();
            
            // Restore focus
            if (focusState) {
                const inputToFocus = document.querySelector(`main input[name="${focusState.name}"]`);
                if (inputToFocus) {
                    inputToFocus.focus();
                    try {
                        inputToFocus.setSelectionRange(focusState.selectionStart, focusState.selectionEnd);
                    } catch(e) {} // ignore for non-text inputs
                }
            }
            
            // Update URL without reload
            window.history.pushState({}, '', url);
            
            // Re-initialize Alpine.js components
            if (window.Alpine) {
                window.Alpine.discoverUninitializedComponents(function(el) {
                    window.Alpine.initializeComponent(el);
                });
            }
        }
    })
    .catch(err => console.error('Error fetching data:', err));
}

document.addEventListener('DOMContentLoaded', initAjaxForms);
</script>
EOT;

foreach($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Remove old script
        $start = strpos($content, '<script id="ajax-search-script">');
        if ($start !== false) {
            $end = strpos($content, '</script>', $start) + 9;
            $content = substr_replace($content, '', $start, $end - $start);
        }
        
        // Inject new script
        $content = str_replace('</body>', $script . "\n</body>", $content);
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
