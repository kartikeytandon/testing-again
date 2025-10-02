<div class="main-nav">
    <!-- Sidebar Logo -->
    <div class="logo-box">
        <a href="{{ route('any', 'home') }}" class="logo-dark text-decoration-none">
            <span class="logo-sm d-inline-block me-2"></span>
            <span class="logo-lg fs-5 fw-semibold text-primary">RD &amp; Company</span>
        </a>

        <a href="{{ route('any', 'home') }}" class="logo-light text-decoration-none">
            <span class="logo-sm d-inline-block me-2"></span>
            <span class="logo-lg fs-5 fw-semibold text-primary">RD &amp; Company</span>
        </a>
    </div>

    <!-- Menu Toggle Button (sm-hover) -->
    <button type="button" class="button-sm-hover" aria-label="Show Full Sidebar">
        <iconify-icon icon="iconamoon:arrow-left-4-square-duotone" class="button-sm-hover-icon"></iconify-icon>
    </button>

    <div class="scrollbar" data-simplebar>
        <ul class="navbar-nav" id="navbar-nav">
            <li class="menu-title">Menus</li>

            <li class="nav-item">
                <a class="nav-link" href="/admin" onclick="setActiveNav(this)">
                    <span class="nav-icon"><iconify-icon icon="iconamoon:chart-line-duotone"></iconify-icon></span>
                    <span class="nav-text"> Dashboard </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('second', ['apps', 'contact']) }}" onclick="setActiveNav(this)">
                    <span class="nav-icon"><iconify-icon icon="iconamoon:users-duotone"></iconify-icon></span>
                    <span class="nav-text"> Employees Profiles </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" onclick="setActiveNav(this); localStorage.setItem('navigate_to','attendance'); window.location.href='/crm';">
                    <span class="nav-icon"><iconify-icon icon="iconamoon:time-duotone"></iconify-icon></span>
                    <span class="nav-text"> Attendance </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" onclick="setActiveNav(this); localStorage.setItem('navigate_to','smm_work'); window.location.href='/crm';">
                    <span class="nav-icon"><iconify-icon icon="iconamoon:notification-bing-duotone"></iconify-icon></span>
                    <span class="nav-text"> SMM Work </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" onclick="setActiveNav(this); localStorage.setItem('navigate_to','daily_work'); window.location.href='/crm';">
                    <span class="nav-icon"><iconify-icon icon="iconamoon:menu-duotone"></iconify-icon></span>
                    <span class="nav-text"> Daily Work </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" onclick="setActiveNav(this); localStorage.setItem('navigate_to','development_work'); window.location.href='/crm';">
                    <span class="nav-icon"><iconify-icon icon="iconamoon:code-duotone"></iconify-icon></span>
                    <span class="nav-text"> Development Work </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" onclick="setActiveNav(this); localStorage.setItem('navigate_to','clients'); window.location.href='/crm';">
                    <span class="nav-icon"><iconify-icon icon="iconamoon:users-group-duotone"></iconify-icon></span>
                    <span class="nav-text"> Clients </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" onclick="setActiveNav(this); localStorage.setItem('navigate_to','client_ledgers'); window.location.href='/crm';">
                    <span class="nav-icon"><iconify-icon icon="iconamoon:file-text-duotone"></iconify-icon></span>
                    <span class="nav-text"> Client Ledgers </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" onclick="setActiveNav(this); localStorage.setItem('navigate_to','holidays'); window.location.href='/crm';">
                    <span class="nav-icon"><iconify-icon icon="iconamoon:calendar-1-duotone"></iconify-icon></span>
                    <span class="nav-text"> Holidays </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" onclick="setActiveNav(this); localStorage.setItem('navigate_to','tasks_analysis'); window.location.href='/crm';">
                    <span class="nav-icon"><iconify-icon icon="iconamoon:graph-up-new-duotone"></iconify-icon></span>
                    <span class="nav-text"> Tasks Analysis </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" onclick="setActiveNav(this); localStorage.setItem('navigate_to','smm_tasks_analysis'); window.location.href='/crm';">
                    <span class="nav-icon"><iconify-icon icon="iconamoon:chart-bar-horizontal-duotone"></iconify-icon></span>
                    <span class="nav-text"> SMM Tasks Analysis </span>
                </a>
            </li>

        </ul>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            try {
                const nav = document.getElementById('navbar-nav');
                if (!nav) return;
                const hasActive = nav.querySelector('.nav-link.active');
                if (!hasActive) {
                    const dashboardLink = nav.querySelector('a.nav-link[href="/admin"]');
                    if (dashboardLink) {
                        dashboardLink.classList.add('active');
                        dashboardLink.setAttribute('aria-current', 'page');
                    }
                }
            } catch (_) {}
        });

        function setActiveNav(clickedLink) {
            try {
                const nav = document.getElementById('navbar-nav');
                if (!nav) return;
                // Remove active class from all nav links
                const allLinks = nav.querySelectorAll('.nav-link');
                allLinks.forEach(link => {
                    link.classList.remove('active');
                    link.removeAttribute('aria-current');
                });
                // Add active class to clicked link
                clickedLink.classList.add('active');
                clickedLink.setAttribute('aria-current', 'page');
            } catch (_) {}
        }
    </script>
</div>
