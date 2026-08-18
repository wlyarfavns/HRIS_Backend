@props(['interval' => 15000])

<script>
    if (window.autoRefreshInterval) {
        clearInterval(window.autoRefreshInterval);
    }
    
    window.autoRefreshInterval = setInterval(() => {
        fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");
            const newMain = doc.querySelector('main');
            const oldMain = document.querySelector('main');
            if (newMain && oldMain) {
                oldMain.innerHTML = newMain.innerHTML;
            }
        });
    }, {{ $interval }});
    
    document.addEventListener("turbolinks:before-visit", function() {
        if (window.autoRefreshInterval) {
            clearInterval(window.autoRefreshInterval);
        }
    }, { once: true });
</script>
