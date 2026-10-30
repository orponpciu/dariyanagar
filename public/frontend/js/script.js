/* ============================================================================
   START SECTION 1: GLOBAL LANGUAGE STATE
   Default language is Bengali ('bn'). Toggles to 'en' via #lang-toggle button.
   ============================================================================ */
let currentLang = 'bn';
/* END SECTION 1: GLOBAL LANGUAGE STATE */

/* ============================================================================
   START SECTION 2: MULTILINGUAL NAVIGATION CATEGORIES
   Populates the sticky <nav id="nav-list"> element
   ============================================================================ */
const categoriesData = [
    { bn: "প্রচ্ছদ", en: "Home" },
    { bn: "জাতীয়", en: "National" },
    { bn: "চট্টগ্রাম", en: "Chattogram" },
    { bn: "আন্তর্জাতিক", en: "International" },
    { bn: "রাজনীতি", en: "Politics" },
    { bn: "অর্থ-বাণিজ্য", en: "Economy" },
    { bn: "খেলাধুলা", en: "Sports" },
    { bn: "বিনোদন", en: "Entertainment" },
    { bn: "তথ্যপ্রযুক্তি", en: "Tech & AI" },
    { bn: "ভিডিও", en: "Video" }
];
/* END SECTION 2: MULTILINGUAL NAVIGATION CATEGORIES */

/* ============================================================================
   START SECTION 3: MULTILINGUAL BREAKING NEWS TICKER DATA
   Updates the <marquee id="breaking-marquee"> text when switching languages
   ============================================================================ */
const tickerTextData = {
    bn: "ডেইলি দরিয়ানগর প্রতিনিধি ও সাংবাদিকের ওপর বর্বরোচিত হামলা... দ্রুত তদন্তের নির্দেশ দিয়েছে স্বরাষ্ট্র মন্ত্রণালয়। • চট্টগ্রামের পটিয়ায় নতুন পরিবেশবান্ধব শিল্পপার্ক নির্মাণের ঘোষণা। • কর্ণফুলী নদীর তলদেশে টানেল ঘিরে দক্ষিণ চট্টগ্রামে নতুন অর্থনৈতিক বলয় তৈরির মহাপরিকল্পনা। • রমজানে নিত্যপণ্যের বাজার স্থিতিশীল রাখতে চট্টগ্রাম বন্দরে বিশেষ খালাস সেল চালু।",
    en: "Brutal attack on Daily Dariyanagar correspondent... Home Ministry orders immediate investigation. • New eco-friendly industrial park announced in Patiya, Chattogram. • Master plan underway for a new economic corridor in South Chattogram around the Karnaphuli Tunnel. • Special clearance cell launched at Chattogram Port to keep essential commodity prices stable during Ramadan."
};
/* END SECTION 3: MULTILINGUAL BREAKING NEWS TICKER DATA */

/* ============================================================================
   START SECTION 4: PRIMARY NEWS DATABASE (BILINGUAL)
   Feeds Hero (index 0), Sub-Grid (index 1-4), Latest Feed (index 1-8), & Related
   ============================================================================ */
const newsDb = [
    {
        id: 1,
        title_bn: "ডেইলি দরিয়ানগরের সাংবাদিকরা পেলেন বর্ষসেরা 'ইমপ্যাক্ট জার্নালিজম অ্যাওয়ার্ড'",
        title_en: "Daily Dariyanagar Journalists Win 'Impact Journalism Award' of the Year",
        img: "https://images.unsplash.com/photo-1526628953301-3e589a6a8b74?w=700&q=80",
        desc_bn: "জনস্বার্থ, দুর্নীতিবিরোধী অনুসন্ধান ও বস্তুনিষ্ঠ সাংবাদিকতায় ডেইলি দরিয়ানগর সবসময় আপসহীন। ২০২৬ সালের প্রথম প্রান্তিকেই অনুসন্ধানী প্রতিবেদনের জন্য জাতীয় পর্যায়ে এই বিশেষ স্বীকৃতি অর্জন করেছে পত্রিকাটির অনুসন্ধান সেল।",
        desc_en: "Uncompromising in public interest and investigative reporting, Daily Dariyanagar's investigative desk has earned national recognition in the first quarter of 2026 for exposing major syndicates.",
        time_bn: "১০ মিনিট আগে",
        time_en: "10 mins ago"
    },
    {
        id: 2,
        title_bn: "বাঁশখালীতে লবণের ট্রাকে দুর্ধর্ষ ডাকাতি, থানায় মামলা দায়ের ও গ্রেপ্তার ৩",
        title_en: "Highway Robbery on Salt Truck in Banshkhali: Case Filed, 3 Arrested",
        img: "https://images.unsplash.com/photo-1589829545856-d10d557cf95f?w=450&q=80",
        desc_bn: "উপকূলীয় বাঁশখালী সড়ক দিয়ে লবণবাহী ট্রাক শহরে আসার পথে ব্যারিকেড দিয়ে ডাকাতির ঘটনায় বিশেষ অভিযান চালিয়ে তিনজনকে আটক করেছে পুলিশ।",
        desc_en: "Police arrested three suspects in a special drive following a highway robbery targeting salt-laden trucks along the coastal Banshkhali route.",
        time_bn: "২৫ মিনিট আগে",
        time_en: "25 mins ago"
    },
    {
        id: 3,
        title_bn: "চট্টগ্রামে গ্যাসলাইন বিস্ফোরণে দগ্ধ ৯ জন, চিকিৎসাধীন অবস্থায় একজনের মৃত্যু",
        title_en: "Gas Pipeline Explosion in Chattogram Leaves 9 Burned, 1 Dead",
        img: "https://images.unsplash.com/photo-1509391366360-2e959784a276?w=450&q=80",
        desc_bn: "ভোররাতে রান্নাঘরে জমে থাকা গ্যাস থেকে ভয়াবহ বিস্ফোরণে একই পরিবারের নারী ও শিশুসহ নয়জন দগ্ধ হয়েছেন। চমেক বার্ন ইউনিটে চলছে চিকিৎসা।",
        desc_en: "Nine members of a family, including women and children, suffered severe burns after accumulated kitchen gas exploded at dawn.",
        time_bn: "৪০ মিনিট আগে",
        time_en: "40 mins ago"
    },
    {
        id: 4,
        title_bn: "চট্টগ্রামে রাতের আঁধারে পাহাড় কাটার দায়ে ৫ জনকে কারাদণ্ড ও জরিমানা",
        title_en: "5 Sentenced to Jail for Cutting Hills Under Cover of Night in Chattogram",
        img: "https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=450&q=80",
        desc_bn: "পরিবেশ অধিদপ্তর ও জেলা প্রশাসনের যৌথ ভ্রাম্যমাণ আদালতের অভিযানে স্কেভেটর জব্দসহ পাঁচজনকে কারাদণ্ড প্রদান।",
        desc_en: "A joint mobile court by the Department of Environment and District Administration seized excavators and jailed five offenders.",
        time_bn: "১ ঘণ্টা আগে",
        time_en: "1 hour ago"
    },
    {
        id: 5,
        title_bn: "পটিয়ায় শিল্পবর্জ্যে খাল ও নদী দূষণ, ৩ কারখানাকে পরিবেশ অধিদপ্তরের জরিমানা",
        title_en: "Industrial Waste Pollution in Patiya: 3 Factories Fined by DoE",
        img: "https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?w=450&q=80",
        desc_bn: "ইটিপি প্ল্যান্ট বন্ধ রেখে সরাসরি খালে রাসায়নিক বর্জ্য ফেলায় তিনটি কারখানাকে পনেরো লাখ টাকা জরিমানা করা হয়েছে।",
        desc_en: "Three factories were fined BDT 1.5 million for bypassing ETP plants and dumping untreated chemical waste into local canals.",
        time_bn: "২ ঘণ্টা আগে",
        time_en: "2 hours ago"
    },
    {
        id: 6,
        title_bn: "কর্ণফুলী ইপিজেডে নতুন ৫ হাজার কর্মসংস্থানের ঘোষণা বিদেশি বিনিয়োগকারীদের",
        title_en: "Foreign Investors Announce 5,000 New Jobs at Karnaphuli EPZ",
        img: "https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=450&q=80",
        desc_bn: "উচ্চপ্রযুক্তির গার্মেন্টস ও অ্যাক্সেসরিজ কারখানা সম্প্রসারণের মাধ্যমে চলতি বছরেই নতুন জনবল নিয়োগ দেওয়া হবে।",
        desc_en: "High-tech apparel and accessories manufacturing expansion will recruit thousands of skilled workers this year.",
        time_bn: "৩ ঘণ্টা আগে",
        time_en: "3 hours ago"
    },
    {
        id: 7,
        title_bn: "কক্সবাজার রেললাইনে যুক্ত হচ্ছে আরও ২ জোড়া আধুনিক পর্যটক এক্সপ্রেস ট্রেন",
        title_en: "2 More Pairs of Modern Tourist Express Trains Added to Cox's Bazar Route",
        img: "https://images.unsplash.com/photo-1474487548417-781cb71495f3?w=450&q=80",
        desc_bn: "পর্যটকদের উপচে পড়া ভিড় সামাল দিতে ঢাকা ও চট্টগ্রাম থেকে কক্সবাজার রুটে নতুন কোচ সংযোজনের সিদ্ধান্ত রেলওয়ের।",
        desc_en: "Bangladesh Railway decides to add new luxury coaches on the Dhaka-Chattogram-Cox's Bazar route to meet surging tourist demand.",
        time_bn: "৪ ঘণ্টা আগে",
        time_en: "4 hours ago"
    },
    {
        id: 8,
        title_bn: "চট্টগ্রাম মেডিকেল কলেজ হাসপাতালে চালু হলো অত্যাধুনিক রোবটিক সার্জারি ইউনিট",
        title_en: "State-of-the-Art Robotic Surgery Unit Launched at Chattogram Medical College",
        img: "https://images.unsplash.com/photo-1516549655169-df83a0774514?w=450&q=80",
        desc_bn: "জটিল অস্ত্রোপচারে সাধারণ রোগীদের বিদেশমুখিতা কমাতে সরকারি ব্যবস্থাপনায় যুক্ত হলো আধুনিক এই প্রযুক্তি।",
        desc_en: "Advanced surgical technology introduced in public healthcare to reduce patients' need to travel abroad for complex procedures.",
        time_bn: "৫ ঘণ্টা আগে",
        time_en: "5 hours ago"
    },
    {
        id: 9,
        title_bn: "শাহ আমানত আন্তর্জাতিক বিমানবন্দরে কার্গো ভিলেজ সম্প্রসারণের কাজ শুরু",
        title_en: "Cargo Village Expansion Begins at Shah Amanat International Airport",
        img: "https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=450&q=80",
        desc_bn: "রপ্তানি পণ্য দ্রুত বিদেশে পাঠাতে আধুনিক কোল্ড স্টোরেজ ও স্ক্যানিং সুবিধা যুক্ত হচ্ছে চট্টগ্রাম বিমানবন্দরে।",
        desc_en: "Modern cold storage and automated scanning facilities are being added at Chattogram airport to speed up export cargo.",
        time_bn: "৬ ঘণ্টা আগে",
        time_en: "6 hours ago"
    }
];
/* END SECTION 4: PRIMARY NEWS DATABASE */

/* ============================================================================
   START SECTION 5: MOST READ NEWS DATABASE (BILINGUAL)
   Populates the #most-read-list card in the middle column to fill vertical space
   ============================================================================ */
const mostReadDb = [
    {
        rank_bn: "১",
        rank_en: "1",
        title_bn: "কর্ণফুলী টানেল ঘিরে আনোয়ারা-কক্সবাজার রুটে হচ্ছে ৬ লেনের নতুন সংযোগ সড়ক",
        title_en: "New 6-Lane Connecting Highway Planned on Anwara-Cox's Bazar Route Around Tunnel"
    },
    {
        rank_bn: "২",
        rank_en: "2",
        title_bn: "রমজানে নিত্যপণ্যের দাম সহনীয় রাখতে চট্টগ্রামে ৫০টি ট্রাকে টিসিবির পণ্য বিক্রি",
        title_en: "TCB Sells Essential Goods via 50 Trucks in Chattogram to Keep Ramadan Prices Stable"
    },
    {
        rank_bn: "৩",
        rank_en: "3",
        title_bn: "মাতারবাড়ী গভীর সমুদ্রবন্দরে ভিড়ল পানামার বিশালাকৃতির মাদার ভেসেল",
        title_en: "Giant Panamanian Mother Vessel Docks at Matarbari Deep Sea Port"
    },
    {
        rank_bn: "৪",
        rank_en: "4",
        title_bn: "চট্টগ্রাম বিশ্ববিদ্যালয়ে শাটল ট্রেনের নতুন সময়সূচি ঘোষণা, বাড়ল বগির সংখ্যা",
        title_en: "Chattogram University Announces New Shuttle Train Schedule With Extra Coaches"
    },
    {
        rank_bn: "৫",
        rank_en: "5",
        title_bn: "সীতাকুণ্ডে শিপব্রেকিং ইয়ার্ডে গ্রিন শিপ রিসাইক্লিং সনদ পেল আরও ৪ প্রতিষ্ঠান",
        title_en: "4 More Shipbreaking Yards in Sitakunda Achieve Green Ship Recycling Certification"
    },
    {
        rank_bn: "৬",
        rank_en: "6",
        title_bn: "কাপ্তাই হ্রদে পর্যটকদের ভ্রমণে নতুন নিরাপত্তা নির্দেশনা জারি করল প্রশাসন",
        title_en: "Administration Issues New Safety Guidelines for Tourists Visiting Kaptai Lake"
    }
];
/* END SECTION 5: MOST READ NEWS DATABASE */

/* ============================================================================
   START SECTION 6: DISCUSSED / TRENDING NEWS DATABASE (BILINGUAL)
   Populates the #discussed-news-container grid below the sub-stories
   ============================================================================ */
const discussedDb = [
    {
        title_bn: "বিদ্যুৎ উৎপাদন নেই, তবুও ক্যাপাসিটি চার্জের নামে পকেটে মাসে ৩৯৩ কোটি টাকা",
        title_en: "Zero Power Generation, Yet BDT 393 Crore Pocketed Monthly as Capacity Charge",
        desc_bn: "বিগত সময়ে অসম চুক্তির আড়ালে বিদ্যুৎকেন্দ্র বসিয়ে রেখে হাজার কোটি টাকা হাতিয়ে নেওয়ার চাঞ্চল্যকর অডিট প্রতিবেদন প্রকাশ...",
        desc_en: "Startling audit report reveals thousands of crores siphoned off through idle power plants under unequal contracts...",
        img: "https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=300&q=80"
    },
    {
        title_bn: "রাজনীতির মাঠে সক্রিয় 'বটবাহিনী', নির্বাচন ঘিরে বাড়ছে এআই ভিডিও অপতথ্য",
        title_en: "'Bot Armies' Active in Politics as AI Video Disinformation Rises Ahead of Polls",
        desc_bn: "জাতীয় ও স্থানীয় নির্বাচন যত ঘনিয়ে আসছে, কৃত্রিম বুদ্ধিমত্তা ব্যবহার করে তৈরি ভুয়া ভিডিওর বিস্তার ততই উদ্বেগজনক হারে বাড়ছে...",
        desc_en: "As elections approach, the spread of synthetic deepfake videos generated using artificial intelligence is rising at an alarming rate...",
        img: "https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?w=300&q=80"
    },
    {
        title_bn: "চট্টগ্রাম বন্দরের অটোমেশন চালু হতেই পণ্য খালাসের সময় কমল অর্ধেকে",
        title_en: "Chattogram Port Automation Cuts Cargo Clearance Time in Half",
        desc_bn: "কাস্টমস ও বন্দর কর্তৃপক্ষের ডিজিটাল ওয়ান-স্টপ সার্ভিসের ফলে আমদানিকারকদের ডেমারেজ খরচ কমেছে লক্ষ লক্ষ টাকা...",
        desc_en: "Digital one-stop service by Customs and Port Authority saves importers millions in demurrage fees...",
        img: "https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=300&q=80"
    },
    {
        title_bn: "পাহাড়ি ঢল ও অতিবৃষ্টিতে দক্ষিণ চট্টগ্রামের লবণ চাষিদের শতকোটি টাকার ক্ষতি",
        title_en: "Flash Floods and Heavy Rain Cause Billions in Losses for South Chattogram Salt Farmers",
        desc_bn: "মৌসুমের মাঝপথে অসময়ের বৃষ্টিতে ভেসে গেছে বাঁশখালী, আনোয়ারা ও কক্সবাজার উপকূলের হাজার হাজার একর লবণ মাঠ...",
        desc_en: "Unseasonal downpours mid-season washed away thousands of acres of salt fields across Banshkhali, Anwara, and Cox's Bazar...",
        img: "https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=300&q=80"
    }
];
/* END SECTION 6: DISCUSSED / TRENDING NEWS DATABASE */

/* ============================================================================
   START SECTION 7: REAL-TIME CLOCK FUNCTION
   Formats current time in Bengali ('bn-BD') or English ('en-US') every second
   ============================================================================ */
function updateClock() {
    const clockEl = document.getElementById('live-clock');
    if (!clockEl) return;

    const now = new Date();
    const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };

    if (currentLang === 'bn') {
        const timeString = now.toLocaleTimeString('bn-BD', timeOptions);
        clockEl.innerText = `মঙ্গলবার, ২৪ ফেব্রুয়ারি ২০২৬ | ১২ ফাল্গুন ১৪৩২ | সময়: ${timeString}`;
    } else {
        const timeString = now.toLocaleTimeString('en-US', timeOptions);
        clockEl.innerText = `Tuesday, 24 February 2026 | 12 Falgun 1432 | Time: ${timeString}`;
    }
}
/* END SECTION 7: REAL-TIME CLOCK FUNCTION */

/* ============================================================================
   START SECTION 8: DYNAMIC CONTENT RENDERER
   Renders JavaScript-driven sections on load and re-renders on language toggle
   ============================================================================ */
function renderDynamicContent() {
    
    // 8.1: Render Sticky Navbar Categories
    const nav = document.getElementById('nav-list');
    if (nav) {
        nav.innerHTML = '';
        categoriesData.forEach(cat => {
            const a = document.createElement('a');
            a.href = "#";
            a.innerText = currentLang === 'bn' ? cat.bn : cat.en;
            nav.appendChild(a);
        });
    }

    // 8.2: Render Lead Hero Story (Homepage)
    const heroSection = document.getElementById('hero-section');
    if (heroSection) {
        const hero = newsDb[0];
        const title = currentLang === 'bn' ? hero.title_bn : hero.title_en;
        const desc = currentLang === 'bn' ? hero.desc_bn : hero.desc_en;
        const btnText = currentLang === 'bn' ? 'বিস্তারিত পড়ুন' : 'Read Full Story';

        heroSection.innerHTML = `
            <div class="hero-banner">
                <img src="${hero.img}" alt="${title}" fetchpriority="high">
            </div>
            <div class="hero-content">
                <h1>${title}</h1>
                <p>${desc}</p>
                <button class="hero-btn" type="button" onclick="window.location.href='/post-details'">${btnText}</button>
            </div>
        `;
    }

    // 8.3: Render 4 Sub-Stories Grid Below Hero (Homepage)
    const subStoriesGrid = document.getElementById('sub-stories');
    if (subStoriesGrid) {
        subStoriesGrid.innerHTML = '';
        newsDb.slice(1, 5).forEach(news => {
            const title = currentLang === 'bn' ? news.title_bn : news.title_en;
            const desc = currentLang === 'bn' ? news.desc_bn : news.desc_en;
            subStoriesGrid.innerHTML += `
                <article class="sub-card reveal active" onclick="window.location.href='/post-details'">
                    <img src="${news.img}" alt="${title}" loading="lazy" decoding="async">
                    <div class="sub-card-body">
                        <h3>${title}</h3>
                        <p>${desc}</p>
                    </div>
                </article>
            `;
        });
    }

    // 8.4: Render Latest Updates Sidebar Feed (7 items in Middle Column Top)
    const sideFeedList = document.getElementById('side-feed-list') || document.getElementById('side-feed');
    if (sideFeedList) {
        if (sideFeedList.id === 'side-feed-list') {
            sideFeedList.innerHTML = '';
        } else {
            const headingText = currentLang === 'bn' ? 'সর্বশেষ আপডেট' : 'Latest Updates';
            sideFeedList.innerHTML = `<h3 class="sidebar-title">${headingText}</h3>`;
        }

        newsDb.slice(2, 9).forEach(news => {
            const title = currentLang === 'bn' ? news.title_bn : news.title_en;
            const time = currentLang === 'bn' ? news.time_bn : news.time_en;
            sideFeedList.innerHTML += `
                <div class="feed-item" onclick="window.location.href='/post-details'">
                    <img src="${news.img}" alt="${title}" loading="lazy" decoding="async">
                    <div>
                        <h4>${title}</h4>
                        <span class="feed-time"><i class="fa fa-clock-o"></i> ${time}</span>
                    </div>
                </div>
            `;
        });
    }

    // 8.5: Render Most Read Sidebar List (6 ranked items in Middle Column Bottom)
    const mostReadList = document.getElementById('most-read-list');
    if (mostReadList) {
        mostReadList.innerHTML = '';
        mostReadDb.forEach(item => {
            const rank = currentLang === 'bn' ? item.rank_bn : item.rank_en;
            const title = currentLang === 'bn' ? item.title_bn : item.title_en;
            mostReadList.innerHTML += `
                <div class="most-read-item" onclick="window.location.href='/post-details'">
                    <span class="most-read-rank">${rank}</span>
                    <h4>${title}</h4>
                </div>
            `;
        });
    }

    // 8.6: Render Discussed News Grid (Homepage)
    const discussedContainer = document.getElementById('discussed-news-container');
    if (discussedContainer) {
        discussedContainer.innerHTML = '';
        discussedDb.forEach(item => {
            const title = currentLang === 'bn' ? item.title_bn : item.title_en;
            const desc = currentLang === 'bn' ? item.desc_bn : item.desc_en;
            discussedContainer.innerHTML += `
                <article class="discussed-item" onclick="window.location.href='/post-details'">
                    <img src="${item.img}" alt="${title}" loading="lazy" decoding="async">
                    <div class="discussed-text">
                        <h4>${title}</h4>
                        <p>${desc}</p>
                    </div>
                </article>
            `;
        });
    }
}
/* END SECTION 8: DYNAMIC CONTENT RENDERER */

/* ============================================================================
   START SECTION 9: FULL-PAGE BILINGUAL TRANSLATION ENGINE
   Scans all HTML elements with [data-bn] and [data-en] attributes and swaps text
   ============================================================================ */
function applyLanguageToDOM() {
    document.documentElement.lang = currentLang;

    // 9.1: Update all static headings, paragraphs, list items, and links
    document.querySelectorAll('[data-bn][data-en]').forEach(el => {
        el.innerText = currentLang === 'bn' ? el.getAttribute('data-bn') : el.getAttribute('data-en');
    });

    // 9.2: Update Search Input Placeholder text
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.placeholder = currentLang === 'bn'
            ? searchInput.getAttribute('data-placeholder-bn')
            : searchInput.getAttribute('data-placeholder-en');
    }

    // 9.3: Update Breaking News Marquee text
    const marqueeEl = document.getElementById('breaking-marquee');
    if (marqueeEl) {
        marqueeEl.innerText = currentLang === 'bn' ? tickerTextData.bn : tickerTextData.en;
    }

    // 9.4: Update Language Switcher Button Label
    const langBtn = document.getElementById('lang-toggle');
    if (langBtn) {
        langBtn.innerText = currentLang === 'bn' ? 'English' : 'বাংলা';
    }

    // 9.5: Re-render dynamic JS sections and refresh clock in the selected language
    renderDynamicContent();
    updateClock();
}
/* END SECTION 9: FULL-PAGE BILINGUAL TRANSLATION ENGINE */

/* ============================================================================
   START SECTION 10: SCROLL REVEAL ANIMATION OBSERVER
   Uses IntersectionObserver API to add '.active' class as user scrolls down
   ============================================================================ */
function initScrollAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, { threshold: 0.08 });

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
}
/* END SECTION 10: SCROLL REVEAL ANIMATION OBSERVER */

/* ============================================================================
   START SECTION 11: PORTAL INITIALIZER (RUNS ON DOMCONTENTLOADED)
   Bootstraps clock, dynamic grids, language switcher, and scroll animations
   ============================================================================ */
function initPortal() {
    // 1. Start Live Clock immediately and update every 1000ms (1 second)
    updateClock();
    setInterval(updateClock, 1000);

    // 2. Render all dynamic news sections
    renderDynamicContent();

    // 3. Attach click listener to Bilingual Toggle Button (#lang-toggle)
    const langBtn = document.getElementById('lang-toggle');
    if (langBtn) {
        langBtn.addEventListener('click', () => {
            currentLang = currentLang === 'bn' ? 'en' : 'bn';
            applyLanguageToDOM();
        });
    }

    // 4. Initialize Scroll Reveal Animations
    initScrollAnimations();
}

document.addEventListener('DOMContentLoaded', initPortal);
/* END SECTION 11: PORTAL INITIALIZER */