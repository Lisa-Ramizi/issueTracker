export function csrfHeaders(includeJson = true) {
    const headers = {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };

    if (includeJson) {
        headers['Content-Type'] = 'application/json';
    }

    return headers;
}

export function partialHtmlHeaders() {
    return {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'text/html',
        'X-Requested-With': 'XMLHttpRequest',
    };
}

export function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
