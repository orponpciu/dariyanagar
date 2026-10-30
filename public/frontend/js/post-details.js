/*
|--------------------------------------------------------------------------
| DAILY DARIYANAGAR — DYNAMIC POST DETAILS
|--------------------------------------------------------------------------
| Existing frontend design preserved.
| News content comes from Laravel / Filament.
| Bengali and English support.
|--------------------------------------------------------------------------
*/

'use strict';

(() => {

    /*
    |--------------------------------------------------------------------------
    | 1. CURRENT LANGUAGE
    |--------------------------------------------------------------------------
    */

    const currentLang =
        document.documentElement.lang === 'en'
            ? 'en'
            : 'bn';


    /*
    |--------------------------------------------------------------------------
    | 2. LIVE CLOCK
    |--------------------------------------------------------------------------
    */

    function updateClock() {

        const clock = document.getElementById('live-clock');

        if (!clock) {
            return;
        }

        const language =
            currentLang === 'bn'
                ? 'bn-BD'
                : 'en-US';

        const formatter = new Intl.DateTimeFormat(language, {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            timeZone: 'Asia/Dhaka'
        });

        clock.textContent = formatter.format(new Date());
    }


    /*
    |--------------------------------------------------------------------------
    | 3. STATIC TEXT LANGUAGE
    |--------------------------------------------------------------------------
    */

    function applyStaticLanguage() {

        document
            .querySelectorAll('[data-bn][data-en]')
            .forEach((element) => {

                const translatedText = element.getAttribute(
                    `data-${currentLang}`
                );

                if (translatedText !== null) {
                    element.textContent = translatedText;
                }

            });

        // Search box placeholder.
        const search = document.getElementById('search-input');

        if (search) {

            const placeholder = search.getAttribute(
                `data-placeholder-${currentLang}`
            );

            search.placeholder = placeholder || (
                currentLang === 'bn'
                    ? 'খবর খুঁজুন...'
                    : 'Search news...'
            );
        }

        // Language button label.
        const toggle = document.getElementById('lang-toggle');

        if (toggle) {

            toggle.textContent =
                currentLang === 'bn'
                    ? 'English'
                    : 'বাংলা';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 4. BENGALI / ENGLISH SWITCHER
    |--------------------------------------------------------------------------
    */

    function setupLanguageToggle() {

        const toggle = document.getElementById('lang-toggle');

        if (!toggle) {
            return;
        }

        toggle.addEventListener('click', () => {

            const url = new URL(window.location.href);

            if (currentLang === 'bn') {

                url.searchParams.set('lang', 'en');

            } else {

                url.searchParams.delete('lang');

            }

            // Reload the page with language-specific
            // article content and SEO metadata.
            window.location.assign(url.toString());

        });
    }


    /*
    |--------------------------------------------------------------------------
    | 5. ORIGINAL SCROLL REVEAL ANIMATIONS
    |--------------------------------------------------------------------------
    */

    function setupRevealAnimations() {

        const elements = document.querySelectorAll('.reveal');

        if (!('IntersectionObserver' in window)) {

            elements.forEach((element) => {
                element.classList.add('active');
            });

            return;
        }

        const observer = new IntersectionObserver(
            (entries) => {

                entries.forEach((entry) => {

                    if (entry.isIntersecting) {

                        entry.target.classList.add('active');

                        observer.unobserve(entry.target);
                    }

                });

            },
            {
                threshold: 0.08
            }
        );

        elements.forEach((element) => {
            observer.observe(element);
        });
    }


    /*
    |--------------------------------------------------------------------------
    | 6. SCROLL TO TOP
    |--------------------------------------------------------------------------
    */

    function setupScrollTopButton() {

        const button = document.getElementById('scrollTopBtn');

        if (!button) {
            return;
        }

        const updateButton = () => {

            button.style.display =
                window.scrollY > 350
                    ? 'flex'
                    : 'none';
        };

        window.addEventListener(
            'scroll',
            updateButton,
            { passive: true }
        );

        updateButton();
    }


    /*
    |--------------------------------------------------------------------------
    | 7. PAGE INITIALIZATION
    |--------------------------------------------------------------------------
    */

    document.addEventListener('DOMContentLoaded', () => {

        applyStaticLanguage();

        updateClock();

        window.setInterval(updateClock, 1000);

        setupLanguageToggle();

        setupRevealAnimations();

        setupScrollTopButton();

    });

})();