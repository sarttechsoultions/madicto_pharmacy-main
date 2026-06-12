<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body>



    <section class="main-container">

        <header class="header">

            <!-- LOGO -->
            <div class="header-logo">
                <a href="#">
                    <img src="image/medicto logo.jpg 1.png" alt="Logo" class="logo-image">
                </a>
            </div>

            <!-- NAVBAR -->
            <nav class="header-navbar" id="mobileMenu">

                <ul class="navbar-menu">

                    <li class="navbar-item">
                        <a href="{{ route('home') }}" class="navbar-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                    </li>

                    <li class="navbar-item">
                        <a href="#" class="navbar-link">About</a>
                    </li>

                    <li class="navbar-item">
                        <a href="{{ route('privacy') }}" class="navbar-link {{ request()->routeIs('privacy') ? 'active' : '' }}">Privacy Policy</a>
                    </li>

                    <li class="navbar-item">
                        <a href="#" class="navbar-link">Contact Us</a>
                    </li>

                </ul>

            </nav>

            <!-- RIGHT -->
            <div class="header-right">

                <!-- DOWNLOAD BUTTON -->
                <button class="download-btn">

                    Download App
                </button>

                <!-- MENU TOGGLE -->
                <button class="menu-toggle" id="menuToggle">
                    <i class="bi bi-list"></i>
                </button>

            </div>

        </header>

    </section>
    <!--  -->


    <section class="main-container hero-section">

        <div class="hero-container">

            <!-- LEFT CONTENT -->
            <div class="hero-content">

                <!-- TAG -->
                <h6 class="hero-tag">
                    <i class="bi bi-patch-check-fill"></i>
                    Trusted by 10,000+ users
                </h6>

                <!-- TITLE -->
                <h1 class="hero-title">
                    Order Medicines <span>Online</span>, Anytime.
                </h1>
                <!-- DESCRIPTION -->
                <p class="hero-description">
                    Fast delivery, genuine medicines, and secure payments.
                    Experience the clinical sanctuary of modern pharmacy right at your
                    doorstep.
                </p>

                <!-- BUTTONS -->
                <div class="hero-buttons">

                    <button class="store-btn">
                        <i class="bi bi-apple"></i>
                        App Store
                    </button>

                    <button class="store-btn">
                        <i class="bi bi-google-play"></i>
                        Play Store
                    </button>

                </div>

                <!-- FEATURES -->
                <div class="hero-features">

                    <div class="feature-box">
                        <i class="bi bi-shield-check"></i>
                        <p>100% Genuine</p>
                    </div>

                    <div class="feature-box">
                        <i class="bi bi-lightning-charge"></i>
                        <p>Fast Delivery</p>
                    </div>

                    <div class="feature-box">
                        <i class="bi bi-shield-lock"></i>
                        <p>Secure Payment</p>
                    </div>

                </div>

            </div>

            <!-- RIGHT IMAGE -->
            <!-- <div class="hero-image-area">

                <img src="image/hero-image.png" alt="Hero Image" class="hero-image">

            </div> -->
            <div class="hero-image-area">
                <img src="image/hero-image.png" alt="">
            </div>

        </div>

    </section>
    <!--  -->

    <section class="trust-section">

        <!-- SECTION HEADING -->
        <div class="trust-heading">

            <h3 class="trust-title">
                Trust & Certifications
            </h3>

            <p class="trust-subtitle">
                Verified by industry leaders.
            </p>

        </div>

        <!-- CARDS -->
        <div class="trust-card-wrapper">

            <!-- CARD 1 -->
            <div class="trust-card">

                <div class="trust-icon">
                    <i class="bi bi-award-fill"></i>
                </div>

                <h4 class="trust-card-title">
                    PHARMACY LICENSE
                </h4>

                <p class="trust-card-text">
                    State Board Certified
                </p>

            </div>

            <!-- CARD 2 -->
            <div class="trust-card">

                <div class="trust-icon">
                    <i class="bi bi-file-earmark-lock-fill"></i>
                </div>

                <h4 class="trust-card-title">
                    HIPAA COMPLIANCE
                </h4>

                <p class="trust-card-text">
                    Secure Data Handling
                </p>

            </div>

            <!-- CARD 3 -->
            <div class="trust-card">

                <div class="trust-icon">
                    <i class="bi bi-shield-fill-check"></i>
                </div>

                <h4 class="trust-card-title">
                    FDA APPROVAL
                </h4>

                <p class="trust-card-text">
                    Quality Standards Met
                </p>

            </div>

            <!-- CARD 4 -->
            <div class="trust-card">

                <div class="trust-icon">
                    <i class="bi bi-patch-check-fill"></i>
                </div>

                <h4 class="trust-card-title">
                    ISO CERTIFIED
                </h4>

                <p class="trust-card-text">
                    Safety Management
                </p>

            </div>

        </div>

    </section>

    <!--  -->

    <section class="steps-section">

        <!-- HEADING -->
        <div class="steps-heading">

            <h2 class="steps-title">
                Your Health in <span>4 Simple Steps</span>
            </h2>

            <p class="steps-subtitle">
                Skip the queue. Our platform integrates high-end technology with clinical expertise to bring wellness to
                your phone.
            </p>

        </div>

        <!-- GRID -->
        <div class="steps-grid">

            <!-- CARD 1 -->
            <div class="steps-card large-card">

                <div class="steps-content">

                    <div class="steps-icon">
                        <i class="bi bi-search"></i>
                    </div>

                    <h3>Easy Medicine Search</h3>

                    <p>
                        Find your prescriptions in seconds with our AI-powered search and category filters.
                        We house over 50k+ genuine medicines.
                    </p>

                </div>

                <div class="steps-image">
                    <img src="image/Medicine bottle.png" alt="Medicine">
                </div>

            </div>

            <!-- CARD 2 -->
            <div class="steps-card">

                <div class="steps-icon">
                    <i class="bi bi-geo-alt"></i>
                </div>

                <h3>Order Tracking</h3>

                <p>
                    Real-time GPS tracking for every delivery. Know exactly when your essentials will arrive.
                </p>

            </div>

            <!-- CARD 3 -->
            <div class="steps-card">

                <div class="steps-icon">
                    <i class="bi bi-credit-card"></i>
                </div>

                <h3>Secure Payments</h3>

                <p>
                    UPI, Credit Cards, or Cash on Delivery. All transactions are protected with bank-grade security.
                </p>

            </div>

            <!-- CARD 4 -->
            <div class="steps-card active-card">

                <div class="steps-icon active-icon">
                    <i class="bi bi-capsule-pill"></i>
                </div>

                <h3>Verified Medicines</h3>

                <p>
                    Every pill is sourced directly from certified pharmacies and licensed manufacturers.
                </p>

            </div>

            <!-- CARD 5 -->
            <div class="steps-card">

                <div class="steps-icon">
                    <i class="bi bi-lightning-charge"></i>
                </div>

                <h3>Fast Delivery</h3>

                <p>
                    Urgent needs deserve urgent speed. Hyper-local delivery network for express arrivals.
                </p>

            </div>

        </div>

    </section>

    <!--  -->

    <section class="healthcare-section">
        <div class="healthcare-container">

            <!-- Left Image -->
            <div class="healthcare-image-box">
                <img src="image/Medical Science.png" alt="Medical Science" class="healthcare-image">

                <div class="trust-badge">
                    <h4>15+</h4>
                    <p>YEARS OF TRUST</p>
                </div>
            </div>

            <!-- Right Content -->
            <div class="healthcare-content">

                <h2 class="healthcare-title">
                    Redefining Healthcare <br>
                    Through Precision
                </h2>

                <p class="healthcare-text">
                    At Vitality Care, we believe that premium health services should be
                    accessible to everyone. Our platform integrates advanced
                    pharmaceutical logistics with a human-centric approach, ensuring
                    you get exactly what you need, when you need it.
                </p>

                <ul class="healthcare-list">
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        Certified Pharmaceutical Standards
                    </li>

                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        Direct Manufacturer Partnerships
                    </li>

                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        Global Quality Certifications
                    </li>
                </ul>

                <button class="healthcare-btn">
                    Learn more about our standards
                    <i class="bi bi-arrow-right"></i>
                </button>

            </div>
        </div>
    </section>
    <!--  -->

    <section class="pharmacy-section">

        <div class="pharmacy-container">

            <!-- TOP -->
            <div class="pharmacy-top">

                <div class="pharmacy-heading">
                    <h5>OUR PHARMACY</h5>
                    <h2>Essential Medications</h2>
                </div>

                <div class="pharmacy-tabs">
                    <button class="tab-btn">Trending</button>
                    <button class="tab-btn active">Antibiotics</button>
                    <button class="tab-btn">Vitamins</button>
                </div>

            </div>

            <!-- SCROLL BUTTONS -->
            <div class="scroll-buttons">

                <button class="scroll-btn" id="scrollLeft">
                    <i class="bi bi-arrow-left"></i>
                </button>

                <button class="scroll-btn" id="scrollRight">
                    <i class="bi bi-arrow-right"></i>
                </button>

            </div>

            <!-- CARDS -->
            <div class="pharmacy-grid">

                <!-- CARD 1 -->


                @foreach ($medicines as $medicine)
                <div class="medicine-card">

                    <div class="medicine-image">
                        <img src="{{ $medicine->image }}" alt="{{ $medicine->name }}">
                    </div>

                    <div class="medicine-content">

                        <h4>{{ $medicine->name }}</h4>

                        <p>
                            {{ $medicine->description }}
                        </p>

                        <div class="medicine-bottom">

                            <span class="medicine-price">₹{{ $medicine->price }}</span>

                            <button class="cart-btn">
                                <i class="bi bi-cart-check-fill"></i>
                            </button>

                        </div>

                    </div>

                </div>
                @endforeach

            </div>

        </div>

    </section>

    <!--  -->
    <section class="main-container">

        <div class="medical-app-container">

            <!-- LEFT CONTENT -->
            <div class="medical-app-content-ap">

                <h2 class="medical-app-content-text">
                    A Medical Hub in
                    <span>Your Pocket</span>
                </h2>

                <!-- ITEM -->
                <div class="medical-feature">

                    <div class="feature-icon">
                        <i class="bi bi-file-earmark-medical"></i>
                    </div>

                    <div class="feature-text">
                        <h4>Clinical Records</h4>
                        <p>
                            View your entire purchase history and
                            medical bills in one place.
                        </p>
                    </div>

                </div>

                <!-- ITEM -->
                <div class="medical-feature">

                    <div class="feature-icon">
                        <i class="bi bi-geo-alt"></i>
                    </div>

                    <div class="feature-text">
                        <h4>Dose Reminders</h4>
                        <p>
                            Set smart alarms for your daily
                            medication schedule.
                        </p>
                    </div>

                </div>

                <!-- ITEM -->
                <div class="medical-feature">

                    <div class="feature-icon">
                        <i class="bi bi-person-heart"></i>
                    </div>

                    <div class="feature-text">
                        <h4>Consult Experts</h4>
                        <p>
                            Direct access to pharmacists for
                            medication guidance.
                        </p>
                    </div>

                </div>

            </div>

            <!-- RIGHT MOBILE IMAGES -->
            <div class="medical-mobile-wrapper">

                <!-- PHONE 1 -->
                <div class="mobile-card">
                    <img src="image/image 14.png" alt="">
                </div>

                <!-- PHONE 2 -->
                <div class="mobile-card center-card">
                    <img src="image/image 14.png" alt="">
                </div>

                <!-- PHONE 3 -->
                <div class="mobile-card">
                    <img src="image/image 14.png" alt="">
                </div>

            </div>

        </div>

    </section>

    <!--  -->
    <section class="download-app-section">

        <div class="download-app-container">

            <!-- CONTENT -->
            <div class="download-app-content">

                <h2>
                    Ready for a Healthier You?
                    <br>
                    Download the App Now.
                </h2>

                <p>
                    Join 10,000+ happy users and get your first
                    medicine delivered in under 60 minutes.
                </p>

                <!-- BUTTONS -->
                <div class="download-buttons">

                    <!-- APP STORE -->
                    <button class="store-btn">

                        <i class="bi bi-apple"></i>

                        <div>
                            <span>DOWNLOAD ON THE</span>
                            <h4>App Store</h4>
                        </div>

                    </button>

                    <!-- GOOGLE PLAY -->
                    <button class="store-btn white-btn">

                        <i class="bi bi-google-play"></i>

                        <div>
                            <span>GET IT ON</span>
                            <h4>Google Play</h4>
                        </div>

                    </button>

                </div>

            </div>

        </div>

    </section>

    <!--  -->

    <section class="location-section">

        <div class="location-container">

            <!-- LEFT CONTENT -->
            <div class="location-content">

                <h5>OUR LOCATIONS</h5>

                <h2>Visit Our Pharmacy</h2>

                <p class="location-text">
                    Experience professional care in person
                    at our flagship medical facility.
                </p>

                <!-- INFO -->
                <div class="location-info-wrapper">

                    <!-- ADDRESS -->
                    <div class="location-info">

                        <div class="location-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>

                        <div>
                            <h4>Address</h4>

                            <p>
                                263/117 Pratap Nager Sector-26,Jaipur Rajasthan 302033
                            </p>
                        </div>

                    </div>

                    <!-- HOURS -->
                    <div class="location-info">

                        <div class="location-icon">
                            <i class="bi bi-clock"></i>
                        </div>

                        <div>
                            <h4>Store Hours</h4>

                            <p>
                                Monday - Sunday
                                <br>
                                Open 24/7
                            </p>
                        </div>

                    </div>

                </div>

                <!-- BUTTON -->
                <button class="direction-btn">

                    <i class="bi bi-signpost-2"></i>

                    Get Directions

                </button>

            </div>

            <!-- MAP -->
            <div class="location-map">

                <img src="image/Gradient.png" alt="Map">

                <!-- CENTER PIN -->
                <div class="map-pin">
                    <i class="bi bi-briefcase-medical-fill"></i>
                </div>

            </div>

        </div>

    </section>

    <!--  -->

    <section class="contact-section">

        <div class="contact-container">

            <!-- LEFT CONTENT -->
            <div class="contact-content">

                <h2>Get in Touch</h2>

                <p class="contact-text">
                    Our team of medical experts and customer support
                    is ready to help you 24/7.
                </p>

                <!-- CONTACT ITEM -->
                <div class="contact-info">

                    <div class="contact-icon">
                        <i class="bi bi-telephone"></i>
                    </div>

                    <div class="contact-details">
                        <span>EMERGENCY LINE</span>
                        <h4>+91 73003 02627</h4>
                    </div>

                </div>

                <!-- CONTACT ITEM -->
                <div class="contact-info">

                    <div class="contact-icon">
                        <i class="bi bi-envelope"></i>
                    </div>

                    <div class="contact-details">
                        <span>EMAIL SUPPORT</span>
                        <h4>medic2pharmacy@gmail.com</h4>
                    </div>

                </div>

                <!-- CONTACT ITEM -->
                <div class="contact-info">

                    <div class="contact-icon">
                        <i class="bi bi-geo-alt"></i>
                    </div>

                    <div class="contact-details">
                        <span>MAIN PHARMACY</span>
                        <h4>263/117 Pratap Nager Sector-26,Jaipur Rajasthan 302033</h4>
                    </div>

                </div>

            </div>

            <!-- RIGHT FORM -->
            <div class="contact-form-box">

                <form class="contact-form">

                    <!-- INPUT -->
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" placeholder="">
                    </div>

                    <!-- INPUT -->
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" placeholder="">
                    </div>

                    <!-- TEXTAREA -->
                    <div class="form-group">
                        <label>Your Message</label>
                        <textarea></textarea>
                    </div>

                    <!-- BUTTON -->
                    <button type="submit" class="contact-btn">
                        Sand Message
                    </button>

                </form>

            </div>

        </div>

    </section>

    <!--  -->
    <footer class="footer-section">

        <div class="footer-container">

            <!-- COLUMN 1 -->
            <div class="footer-col footer-brand">

                <!-- LOGO -->
                <h2 class="footer-logo">
                    Vitality Care
                </h2>

                <!-- TEXT -->
                <p class="footer-text">
                    Your globally trusted partner
                    for pharmaceutical excellence
                    and professional healthcare.
                </p>

                <!-- SOCIAL -->
                <div class="footer-social">

                    <a href="#">
                        <i class="bi bi-share"></i>
                    </a>

                    <a href="#">
                        <i class="bi bi-globe"></i>
                    </a>

                </div>

            </div>

            <!-- COLUMN 2 -->
            <div class="footer-col">

                <h3 class="footer-heading">
                    Shop Collections
                </h3>

                <ul class="footer-links">

                    <li>
                        <a href="#">
                            Vitamins & Supplements
                        </a>
                    </li>

                    <li>
                        <a href="#">
                            First Aid Essentials
                        </a>
                    </li>

                    <li>
                        <a href="#">
                            Chronic Care
                        </a>
                    </li>

                    <li>
                        <a href="#">
                            Dermatology & Skin Care
                        </a>
                    </li>

                    <li>
                        <a href="#">
                            Mother & Baby Care
                        </a>
                    </li>

                    <li>
                        <a href="#">
                            Personal Hygiene
                        </a>
                    </li>

                </ul>

            </div>

            <!-- COLUMN 3 -->
            <div class="footer-col">

                <h3 class="footer-heading">
                    Support Hotline
                </h3>

                <!-- PHONE -->
                <div class="footer-hotline">

                    <i class="bi bi-headset"></i>

                    <span>
                        1-800-VITALITY
                    </span>

                </div>

                <!-- DESCRIPTION -->
                <p class="footer-small-text">
                    Available 24/7 for emergency
                    prescriptions and medical guidance.
                </p>

                <!-- HOURS -->
                <div class="footer-hours">

                    <span class="hours-label">
                        PHARMACY HOURS
                    </span>

                    <div class="hours-info">

                        <i class="bi bi-clock"></i>

                        <p>
                            Open 24/7, 365 Days
                        </p>

                    </div>

                </div>

            </div>

            <!-- COLUMN 4 -->
            <div class="footer-col">

                <h3 class="footer-heading">
                    Main Pharmacy
                </h3>

                <!-- ADDRESS -->
                <div class="footer-location">

                    <i class="bi bi-geo-alt"></i>

                    <p>
                        742 Medical Blvd,<br>
                        Innovation District,<br>
                        New York, NY 10012<br>
                        United States
                    </p>

                </div>

                <!-- BUTTON -->
                <a href="#" class="direction-link">

                    GET DIRECTIONS

                    <i class="bi bi-arrow-right"></i>

                </a>

            </div>

            <!-- COLUMN 5 -->
            <div class="footer-col">

                <h3 class="footer-heading">
                    Professional Certifications
                </h3>

                <!-- BADGES -->
                <div class="footer-badges">

                    <!-- BADGE -->
                    <div class="footer-badge">

                        <i class="bi bi-patch-check-fill"></i>

                        <span>
                            VERIFIED<br>
                            PHARMACY
                        </span>

                    </div>

                    <!-- BADGE -->
                    <div class="footer-badge">

                        <i class="bi bi-shield-fill-check"></i>

                        <span>
                            HIPAA<br>
                            COMPLIANT
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </footer>
</body>

<script>
    /* =========================
   MOBILE MENU
========================= */

    const menuToggle = document.getElementById("menuToggle");
    const mobileMenu = document.getElementById("mobileMenu");

    menuToggle.addEventListener("click", () => {

        mobileMenu.classList.toggle("active");

        /* ICON CHANGE */
        if (mobileMenu.classList.contains("active")) {
            menuToggle.innerHTML = `<i class="bi bi-x-lg"></i>`;
        } else {
            menuToggle.innerHTML = `<i class="bi bi-list"></i>`;
        }

    });

    document.addEventListener("DOMContentLoaded", () => {
        const gridContainer = document.querySelector(".pharmacy-grid");
        const btnLeft = document.getElementById("scrollLeft");
        const btnRight = document.getElementById("scrollRight");

        // Agar elements HTML me na milein to error se bachne ke liye safe-check
        if (!gridContainer || !btnLeft || !btnRight) return;

        // Har click par kitna scroll hona chahiye (Ek card + gap ki width automatic nikalega)
        const getScrollAmount = () => {
            const firstCard = gridContainer.querySelector(".medicine-card");
            if (firstCard) {
                // card ki width + uske bagal wale gap/margin ko jodkar scroll distance set hoga
                return firstCard.clientWidth + 24;
            }
            return 300; // Fallback value agar card na mile
        };

        // Right Arrow Button Click Functionality
        btnRight.addEventListener("click", () => {
            gridContainer.scrollBy({
                left: getScrollAmount(),
                behavior: "smooth" // Isse scroll jhatke se nahi balki smoothly hoga
            });
        });

        // Left Arrow Button Click Functionality
        btnLeft.addEventListener("click", () => {
            gridContainer.scrollBy({
                left: -getScrollAmount(), // Minus value se left side scroll hoga
                behavior: "smooth"
            });
        });

        /* ==========================================================================
           BONUS FEATURE: Scroll End Detection (Optional)
           Jab scroll shuruat me ya aakhir me ho, to buttons ko halka sa disable style dena
           ========================================================================== */
        const toggleButtonState = () => {
            // Left button ko disable/enable karna
            if (gridContainer.scrollLeft <= 5) {
                btnLeft.style.opacity = "0.5";
                btnLeft.style.pointerEvents = "none";
            } else {
                btnLeft.style.opacity = "1";
                btnLeft.style.pointerEvents = "auto";
            }

            // Right button ko disable/enable karna
            const maxScrollLeft = gridContainer.scrollWidth - gridContainer.clientWidth;
            if (gridContainer.scrollLeft >= maxScrollLeft - 5) {
                btnRight.style.opacity = "0.5";
                btnRight.style.pointerEvents = "none";
            } else {
                btnRight.style.opacity = "1";
                btnRight.style.pointerEvents = "auto";
            }
        };

        // Page load aur scrolling ke waqt buttons ki halat check karega
        gridContainer.addEventListener("scroll", toggleButtonState);
        window.addEventListener("resize", toggleButtonState); // Screen size badalne par recalculate
        toggleButtonState(); // Shuruat me run karne ke liye
    });
</script>

</html>