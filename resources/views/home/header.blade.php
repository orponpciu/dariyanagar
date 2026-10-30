<!-- ===================================================================
         START SECTION 6: TOP UTILITY BAR
         Contains: Live JS Clock/Date, E-Paper Button, and Language Switcher
    ==================================================================== -->
    <div class="top-bar">
        <div id="live-clock">মঙ্গলবার, ২৪ ফেব্রুয়ারি ২০২৬ | ১২ ফাল্গুন ১৪৩২</div>
        <div class="top-bar-right">
            <span class="epaper-btn" data-bn="ই-পেপার" data-en="E-Paper">ই-পেপার</span>
            <button id="lang-toggle" class="lang-btn" type="button" aria-label="Toggle Language">English</button>
        </div>
    </div>
    <!-- END SECTION 6: TOP UTILITY BAR -->

    <!-- ===================================================================
         START SECTION 7: BRANDING HEADER
         Contains: Image-Only Logo (frontend/img/logo.png) and Search Bar
    ==================================================================== -->
    <header>
        <a href="{{ url('/') }}" class="logo" aria-label="ডেইলি দরিয়ানগর হোমপেজ">
            <img src="{{ asset('frontend/img/logo.png') }}" alt="ডেইলি দরিয়ানগর | Daily Dariyanagar" class="logo-img" fetchpriority="high">
        </a>
        <form class="search-box" role="search" onsubmit="event.preventDefault();">
            <input type="text" id="search-input" placeholder="খবর খুঁজুন..." data-placeholder-bn="খবর খুঁজুন..." data-placeholder-en="Search news..." aria-label="Search News">
            <button type="submit" data-bn="খুঁজুন" data-en="Search">খুঁজুন</button>
        </form>
    </header>
    <!-- END SECTION 7: BRANDING HEADER -->

    <!-- ===================================================================
         START SECTION 8: STICKY NAVIGATION BAR
         Populated dynamically via renderDynamicContent() in script.js
    ==================================================================== -->
    <nav id="nav-list" aria-label="Primary Navigation"></nav>
    <!-- END SECTION 8: STICKY NAVIGATION BAR -->

    <!-- ===================================================================
         START SECTION 9: BREAKING NEWS TICKER
         Contains: Animated live indicator dot and scrolling marquee headlines
    ==================================================================== -->
    <div class="ticker">
        <div class="ticker-title">
            <span class="live-dot"></span>
            <span data-bn="ব্রেকিং নিউজ" data-en="BREAKING NEWS">ব্রেকিং নিউজ</span>
        </div>
        <marquee id="breaking-marquee" scrollamount="6" onmouseover="this.stop();" onmouseout="this.start();">
            ডেইলি দরিয়ানগর প্রতিনিধি ও সাংবাদিকের ওপর বর্বরোচিত হামলা... দ্রুত তদন্তের নির্দেশ দিয়েছে স্বরাষ্ট্র মন্ত্রণালয়। • চট্টগ্রামের পটিয়ায় নতুন পরিবেশবান্ধব শিল্পপার্ক নির্মাণের ঘোষণা। • কর্ণফুলী নদীর তলদেশে টানেল ঘিরে দক্ষিণ চট্টগ্রামে নতুন অর্থনৈতিক বলয় তৈরির মহাপরিকল্পনা। • রমজানে নিত্যপণ্যের বাজার স্থিতিশীল রাখতে চট্টগ্রাম বন্দরে বিশেষ খালাস সেল চালু।
        </marquee>
    </div>
    <!-- END SECTION 9: BREAKING NEWS TICKER -->