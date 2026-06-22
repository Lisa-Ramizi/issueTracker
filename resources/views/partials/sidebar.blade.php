<aside class="sidebar">
    <div class="sidebar__brand">I-track</div>

    <nav class="sidebar__nav">
        <a href="{{ route('projects.index') }}"
           class="sidebar__link {{ request()->routeIs('projects.*') ? 'sidebar__link--active' : '' }}">
            Projects
        </a>
        <a href="{{ route('tags.index') }}"
           class="sidebar__link {{ request()->routeIs('tags.*') ? 'sidebar__link--active' : '' }}">
            Tags
        </a>
    </nav>

    <div class="sidebar__footer">PRITECH · Issue Tracker</div>
</aside>
