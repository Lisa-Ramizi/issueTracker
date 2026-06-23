import { partialHtmlHeaders } from './helpers';

function initIssuesFilter() {
    const form = document.getElementById('issues-filter-form');
    const list = document.getElementById('issue-list');
    const subtitle = document.getElementById('issues-result-count');
    const kanban = document.getElementById('project-board-kanban');
    const searchPanel = document.getElementById('issue-search-panel');

    if (!form || !list) {
        return;
    }

    const searchInput = form.querySelector('#search');
    let debounceTimer = null;

    function hasActiveFilters() {
        const params = new URLSearchParams(new FormData(form));

        return ['search', 'status', 'priority', 'tag_id'].some((key) => {
            const value = params.get(key);

            return value !== null && value !== '';
        });
    }

    function syncBoardView() {
        if (!kanban || !searchPanel) {
            return;
        }

        const filtering = hasActiveFilters();
        kanban.hidden = filtering;
        searchPanel.hidden = !filtering;
    }

    async function fetchIssues(url) {
        const response = await fetch(url, { headers: partialHtmlHeaders() });

        if (!response.ok) {
            return;
        }

        const html = await response.text();
        list.innerHTML = html;

        const meta = list.querySelector('#issue-list-meta');
        const total = meta ? meta.dataset.total : list.dataset.total;

        if (subtitle && total !== undefined) {
            const count = parseInt(total, 10);
            subtitle.textContent = `${count} result${count === 1 ? '' : 's'}`;
            list.dataset.total = total;
        }

        if (kanban) {
            const params = new URLSearchParams(new FormData(form));
            const query = params.toString();
            const boardUrl = new URL(window.location.href);
            boardUrl.search = query;
            window.history.replaceState({}, '', boardUrl);
        } else {
            window.history.replaceState({}, '', url);
        }
    }

    function buildUrl() {
        const params = new URLSearchParams(new FormData(form));
        const base = form.action.split('?')[0];
        const query = params.toString();

        return query ? `${base}?${query}` : base;
    }

    function applyFilters() {
        syncBoardView();

        if (kanban && !hasActiveFilters()) {
            return;
        }

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
        syncBoardView();

        if (kanban) {
            window.history.replaceState({}, '', window.location.pathname);
            return;
        }

        fetchIssues(form.action.split('?')[0]);
    });

    if (hasActiveFilters()) {
        applyFilters();
    }
}

document.addEventListener('DOMContentLoaded', initIssuesFilter);
