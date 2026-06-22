import { csrfHeaders } from './helpers';

function initIssuesFilter() {
    const form = document.getElementById('issues-filter-form');
    const list = document.getElementById('issue-list');
    const subtitle = document.getElementById('issues-result-count');

    if (!form || !list) {
        return;
    }

    const searchInput = form.querySelector('#search');
    let debounceTimer = null;

    async function fetchIssues(url) {
        const response = await fetch(url, { headers: csrfHeaders(false) });
        const html = await response.text();
        list.innerHTML = html;

        const meta = list.querySelector('#issue-list-meta');
        const total = meta ? meta.dataset.total : list.dataset.total;
        if (subtitle && total !== undefined) {
            const count = parseInt(total, 10);
            subtitle.textContent = `Issues · ${count} result${count === 1 ? '' : 's'}`;
            list.dataset.total = total;
        }

        window.history.replaceState({}, '', url);
    }

    function buildUrl() {
        const params = new URLSearchParams(new FormData(form));
        const base = form.action.split('?')[0];
        const query = params.toString();
        return query ? `${base}?${query}` : base;
    }

    function applyFilters() {
        fetchIssues(buildUrl());
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        applyFilters();
    });

    form.querySelectorAll('select').forEach((select) => {
        select.addEventListener('change', applyFilters);
    });

    searchInput?.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(applyFilters, 300);
    });

    list.addEventListener('click', (event) => {
        const link = event.target.closest('.pagination a');
        if (!link) {
            return;
        }

        event.preventDefault();
        fetchIssues(link.href);
    });

    document.getElementById('issues-filter-clear')?.addEventListener('click', (event) => {
        event.preventDefault();
        form.reset();
        fetchIssues(form.action.split('?')[0]);
    });
}

document.addEventListener('DOMContentLoaded', initIssuesFilter);
