<aside class="sidebar" aria-label="Main navigation">
    <div class="sidebar__panel">
        <a href="{{ route('projects.index') }}"
           class="sidebar__brand {{ request()->routeIs('projects.*') ? 'sidebar__brand--active' : '' }}"
           title="Home">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M3 11L12 3l9 8"/>
                <path d="M5 10v10h14V10"/>
                <rect x="9" y="14" width="6" height="6"/>
            </svg>
        </a>

        <nav class="sidebar__nav" aria-label="Primary">
            <a href="{{ route('tags.index') }}"
               class="sidebar__link {{ request()->routeIs('tags.*') ? 'sidebar__link--active' : '' }}"
               title="Tags">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                    <rect x="5" y="5" width="4" height="4" fill="currentColor" stroke="none"/>
                </svg>
            </a>
        </nav>

        <div class="sidebar__spacer"></div>

        <nav class="sidebar__footer-nav" aria-label="Secondary">
            <a href="{{ route('projects.create') }}" class="sidebar__link" title="New project">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="sidebar__logout">
                @csrf
                <button type="submit" class="sidebar__link" title="Sign out">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </button>
            </form>
        </nav>
    </div>
</aside>
