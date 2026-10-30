/* ============================================================
   DAILY DARIYANAGAR — DYNAMIC CATEGORY PAGE

   Laravel 12/13
   Filament 5

   Uses database content passed from CategoryController.

   No hardcoded news posts.
   No redesign of homepage or article details.
   ============================================================ */

'use strict';

(() => {

    /*
    |--------------------------------------------------------------------------
    | 1. CATEGORY DATA
    |--------------------------------------------------------------------------
    */

    const data = window.DARIYANAGAR_CATEGORY || {};

    const lang = data.lang === 'en'
        ? 'en'
        : 'bn';

    const isPreview = data.preview === true;

    const homeUrl = data.homeUrl || '/';


    /*
    |--------------------------------------------------------------------------
    | 2. STATIC BILINGUAL LABELS
    |--------------------------------------------------------------------------
    */

    function localizeStaticElements() {

        document.documentElement.lang = lang;

        document
            .querySelectorAll('[data-bn][data-en]')
            .forEach((element) => {

                const value = element.getAttribute(
                    `data-${lang}`
                );

                if (value !== null) {
                    element.textContent = value;
                }

            });


        /*
        | Language toggle
        */

        const button = document.getElementById(
            'lang-toggle'
        );

        if (button) {

            button.textContent = lang === 'bn'
                ? 'English'
                : 'বাংলা';

            button.setAttribute(
                'aria-label',
                lang === 'bn'
                    ? 'Switch to English'
                    : 'বাংলায় দেখুন'
            );

        }


        /*
        | Search placeholder
        */

        const search = document.getElementById(
            'search-input'
        );

        if (search) {

            search.placeholder = search.getAttribute(
                `data-placeholder-${lang}`
            ) || (
                lang === 'bn'
                    ? 'খবর খুঁজুন...'
                    : 'Search news...'
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | 3. DYNAMIC CATEGORY NAVIGATION
    |--------------------------------------------------------------------------
    |
    | All categories come directly from NewsCategory.
    |--------------------------------------------------------------------------
    */

    function renderNavigation() {

        const nav = document.getElementById(
            'nav-list'
        );

        if (!nav) {
            return;
        }

        nav.replaceChildren();


        /*
        | Homepage link
        */

        const homeLink = document.createElement('a');

        const target = new URL(
            homeUrl,
            window.location.origin
        );

        if (lang === 'en') {
            target.searchParams.set('lang', 'en');
        }

        if (isPreview) {
            target.searchParams.set('preview', '1');
        }

        homeLink.href = target.toString();

        homeLink.textContent = lang === 'bn'
            ? 'প্রচ্ছদ'
            : 'Home';

        nav.appendChild(homeLink);


        /*
        | Category links
        */

        for (const category of (data.navigation || [])) {

            const link = document.createElement('a');

            link.href = category.url;

            link.textContent = lang === 'bn'
                ? category.bn
                : category.en;

            if (category.active) {

                link.setAttribute(
                    'aria-current',
                    'page'
                );

            }

            nav.appendChild(link);

        }

    }


    /*
    |--------------------------------------------------------------------------
    | 4. DYNAMIC BREAKING NEWS TICKER
    |--------------------------------------------------------------------------
    */

    function renderBreakingTicker() {

        const ticker = document.getElementById(
            'breaking-marquee'
        );

        if (!ticker) {
            return;
        }

        const items = data.ticker || [];

        if (!items.length) {
            return;
        }

        ticker.replaceChildren();

        items.forEach((item, index) => {

            if (index > 0) {

                ticker.appendChild(
                    document.createTextNode('   •   ')
                );

            }

            const link = document.createElement('a');

            link.href = item.url;

            link.textContent = lang === 'bn'
                ? item.bn
                : item.en;

            ticker.appendChild(link);

        });

    }


    /*
    |--------------------------------------------------------------------------
    | 5. LIVE DATE AND TIME
    |--------------------------------------------------------------------------
    */

    function updateClock() {

        const element = document.getElementById(
            'live-clock'
        );

        if (!element) {
            return;
        }

        element.textContent = new Intl.DateTimeFormat(
            lang === 'bn'
                ? 'bn-BD'
                : 'en-US',
            {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true,
                timeZone: 'Asia/Dhaka'
            }
        ).format(new Date());

    }


    /*
    |--------------------------------------------------------------------------
    | 6. LANGUAGE SWITCHING
    |--------------------------------------------------------------------------
    |
    | Reload with ?lang=en because article content
    | is translated by Laravel Blade.
    |--------------------------------------------------------------------------
    */

    function enableLanguageSwitch() {

        const button = document.getElementById(
            'lang-toggle'
        );

        if (!button) {
            return;
        }

        button.addEventListener('click', () => {

            const target = new URL(
                window.location.href
            );

            if (lang === 'bn') {

                target.searchParams.set(
                    'lang',
                    'en'
                );

            } else {

                target.searchParams.delete(
                    'lang'
                );

            }

            window.location.assign(
                target.toString()
            );

        });

    }


    /*
    |--------------------------------------------------------------------------
    | 7. GLOBAL NEWS SEARCH
    |--------------------------------------------------------------------------
    |
    | Searches news through HomepageController.
    |--------------------------------------------------------------------------
    */

    function enableGlobalSearch() {

        const searchInput = document.getElementById(
            'search-input'
        );

        const form = searchInput?.closest('form');

        if (!form) {
            return;
        }

        form.addEventListener('submit', (event) => {

            event.preventDefault();

            const term = searchInput.value.trim();

            const url = new URL(
                homeUrl,
                window.location.origin
            );

            if (term) {

                url.searchParams.set(
                    'q',
                    term
                );

            }

            if (lang === 'en') {

                url.searchParams.set(
                    'lang',
                    'en'
                );

            }

            if (isPreview) {

                url.searchParams.set(
                    'preview',
                    '1'
                );

            }

            window.location.assign(
                url.toString()
            );

        });

    }


    /*
    |--------------------------------------------------------------------------
    | 8. CATEGORY SORTING
    |--------------------------------------------------------------------------
    */

    function enableSort() {

        const sort = document.getElementById(
            'category-sort'
        );

        if (!sort) {
            return;
        }

        sort.addEventListener('change', () => {

            sort.form?.requestSubmit();

        });

    }


    /*
    |--------------------------------------------------------------------------
    | 9. ORIGINAL SCROLL REVEAL ANIMATIONS
    |--------------------------------------------------------------------------
    */

    function enableScrollAnimations() {

        const cards = document.querySelectorAll(
            '.reveal:not(.active)'
        );

        if (
            !('IntersectionObserver' in window)
            || window.matchMedia(
                '(prefers-reduced-motion: reduce)'
            ).matches
        ) {

            cards.forEach((card) => {

                card.classList.add('active');

            });

            return;
        }

        const observer = new IntersectionObserver(
            (entries) => {

                entries.forEach((entry) => {

                    if (entry.isIntersecting) {

                        entry.target.classList.add(
                            'active'
                        );

                        observer.unobserve(
                            entry.target
                        );

                    }

                });

            },
            {
                threshold: 0.07
            }
        );

        cards.forEach((card) => {

            observer.observe(card);

        });

    }


    /*
    |--------------------------------------------------------------------------
    | 10. SCROLL TO TOP
    |--------------------------------------------------------------------------
    */

    function enableScrollTop() {

        const button =
            document.getElementById('scrollTopBtn')
            || document.querySelector('.scroll-top');

        if (!button) {
            return;
        }

        const refresh = () => {

            button.style.display =
                window.scrollY > 350
                    ? 'flex'
                    : 'none';

        };

        button.addEventListener('click', () => {

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });

        });

        window.addEventListener(
            'scroll',
            refresh,
            {
                passive: true
            }
        );

        refresh();

    }


    /*
    |--------------------------------------------------------------------------
    | 11. INITIALIZE CATEGORY PAGE
    |--------------------------------------------------------------------------
    */

    function init() {

        localizeStaticElements();

        renderNavigation();

        renderBreakingTicker();

        updateClock();

        window.setInterval(
            updateClock,
            1000
        );

        enableLanguageSwitch();

        enableGlobalSearch();

        enableSort();

        enableScrollAnimations();

        enableScrollTop();

    }


    /*
    |--------------------------------------------------------------------------
    | 12. DOM READY
    |--------------------------------------------------------------------------
    */

    if (document.readyState === 'loading') {

        document.addEventListener(
            'DOMContentLoaded',
            init
        );

    } else {

        init();

    }

})();