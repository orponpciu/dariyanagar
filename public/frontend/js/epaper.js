'use strict';

(() => {
    const data = window.EPAPER_DATA || {};

    const archiveDates = Array.isArray(data.archive) ? data.archive : [];
    const pageCount = Number(data.count || 0);
    const lang = data.lang === 'en' ? 'en' : 'bn';

    let currentPage = Math.max(1, Math.min(pageCount || 1, Number(data.page || 1)));

    const selectedDate = data.date || '';
    const baseUrl = data.baseUrl || '/epaper';

    const archiveSet = new Set(archiveDates);

    let currentMonth = selectedDate
        ? new Date(selectedDate + 'T00:00:00')
        : new Date();

    const bnDigits = (value) => String(value).replace(/\d/g, (digit) => {
        return ['০','১','২','৩','৪','৫','৬','৭','৮','৯'][Number(digit)];
    });

    const byId = (id) => document.getElementById(id);

    function buildUrl(params = {}) {
        const url = new URL(baseUrl, window.location.origin);

        const date = params.date ?? selectedDate;
        if (date) url.searchParams.set('date', date);

        const page = params.page ?? currentPage;
        if (page) url.searchParams.set('page', page);

        if (lang === 'en') {
            url.searchParams.set('lang', 'en');
        }

        return url.toString();
    }

    function updatePage(page) {
        if (!pageCount) return;

        currentPage = Math.max(1, Math.min(pageCount, Number(page)));

        document.querySelectorAll('.preview-page-slot').forEach((slot) => {
            slot.classList.toggle('is-active', Number(slot.dataset.page) === currentPage);
        });

        document.querySelectorAll('[data-go-page]').forEach((el) => {
            const isActive = Number(el.dataset.goPage) === currentPage;
            el.classList.toggle('is-active', isActive);
        });

        document.querySelectorAll('.toolbar-page-tab').forEach((el) => {
            const isActive = Number(el.dataset.goPage) === currentPage;
            el.classList.toggle('is-active', isActive);
        });

        const url = new URL(window.location.href);
        url.searchParams.set('page', currentPage);
        history.replaceState(null, '', url.toString());
    }

    function attachPageSwitching() {
        document.querySelectorAll('[data-go-page]').forEach((button) => {
            button.addEventListener('click', () => {
                updatePage(Number(button.dataset.goPage));
            });
        });

        window.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowLeft') {
                updatePage(currentPage - 1);
            }

            if (event.key === 'ArrowRight') {
                updatePage(currentPage + 1);
            }
        });
    }

    function renderCalendar() {
        const monthLabel = byId('calendar-month-label');
        const grid = byId('calendar-grid');

        if (!monthLabel || !grid) return;

        const year = currentMonth.getFullYear();
        const month = currentMonth.getMonth();

        const dateForLabel = new Date(year, month, 1);

        monthLabel.textContent = dateForLabel.toLocaleDateString(
            lang === 'en' ? 'en-US' : 'en-US',
            {
                month: 'long',
                year: 'numeric',
            }
        );

        grid.innerHTML = '';

        const firstDayIndex = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        for (let i = 0; i < firstDayIndex; i++) {
            const empty = document.createElement('div');
            empty.className = 'calendar-day is-empty';
            grid.appendChild(empty);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const formattedMonth = String(month + 1).padStart(2, '0');
            const formattedDay = String(day).padStart(2, '0');
            const isoDate = `${year}-${formattedMonth}-${formattedDay}`;

            const cell = document.createElement('button');
            cell.type = 'button';
            cell.className = 'calendar-day';

            if (archiveSet.has(isoDate)) {
                cell.classList.add('has-edition');
            }

            if (isoDate === selectedDate) {
                cell.classList.add('is-selected');
            }

            cell.textContent = lang === 'bn' ? bnDigits(day) : String(day);

            if (archiveSet.has(isoDate)) {
                cell.addEventListener('click', () => {
                    window.location.href = buildUrl({
                        date: isoDate,
                        page: 1,
                    });
                });
            } else {
                cell.disabled = true;
            }

            grid.appendChild(cell);
        }
    }

    function attachCalendarNavigation() {
        byId('calendar-prev-month')?.addEventListener('click', () => {
            currentMonth = new Date(
                currentMonth.getFullYear(),
                currentMonth.getMonth() - 1,
                1
            );
            renderCalendar();
        });

        byId('calendar-next-month')?.addEventListener('click', () => {
            currentMonth = new Date(
                currentMonth.getFullYear(),
                currentMonth.getMonth() + 1,
                1
            );
            renderCalendar();
        });
    }

    function init() {
        updatePage(currentPage);
        attachPageSwitching();
        renderCalendar();
        attachCalendarNavigation();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();