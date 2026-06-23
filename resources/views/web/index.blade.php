<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicto Pharmacy | Online Pharmacy - Order Medicines, Vitamins & Health Essentials</title>

    <meta name="description"
        content="Order genuine medicines, vitamins & health essentials online with Medicto Pharmacy. Fast delivery, 100% verified products, secure payments & 24/7 pharmacist support in Jaipur.">
    <meta name="keywords"
        content="online pharmacy, order medicines online, Medicto Pharmacy, buy vitamins online, medicine delivery Jaipur, healthcare app, prescription medicines">
    <meta name="author" content="Medicto Pharmacy">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://medictopharmacy.com/">

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://medictopharmacy.com/">
    <meta property="og:title" content="Medicto Pharmacy | Online Pharmacy">
    <meta property="og:description"
        content="Order genuine medicines, vitamins & health essentials online. Fast delivery, 100% verified products & secure payments.">
    <meta property="og:image" content="https://medictopharmacy.com/image/hero-image.png">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://medictopharmacy.com/">
    <meta name="twitter:title" content="Medicto Pharmacy | Online Pharmacy">
    <meta name="twitter:description" content="Order genuine medicines, vitamins & health essentials online.">
    <meta name="twitter:image" content="https://medictopharmacy.com/image/hero-image.png">

 <link rel="icon" type="image/png" href="{{ asset('image/favicon.webp') }}" />
    <link rel="preload" href="image/hero-image.png" as="image">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">`
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body>

    <!-- PAGE LOADER -->
    <div class="page-loader" id="pageLoader">
        <div class="loader-logo">
            <div class="loader-pulse"></div>

        </div>
    </div>

    <!-- SCROLL TO TOP -->
    <button class="scroll-top" id="scrollTop" aria-label="Scroll to top">
        <i class="bi bi-arrow-up"></i>
    </button>

    <!-- ===== HEADER ===== -->
    <header class="header" id="siteHeader">
        <div class="header-inner main-container">

            <div class="header-logo">
                <a href="{{ route('home') }}">
                    <img src="image/medicto-logo.jpg.webp" alt="Medicto Logo" class="logo-image" loading="eager">
                </a>
            </div>

            <nav class="header-navbar" id="mobileMenu" role="navigation" aria-label="Main navigation">
                <ul class="navbar-menu">
                    <li class="navbar-item"><a href="{{ route('home') }}" class="navbar-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
                    <li class="navbar-item"><a href="#about-us" class="navbar-link">About</a></li>
                    <li class="navbar-item"><a href="{{ route('privacy') }}" class="navbar-link {{ request()->routeIs('privacy') ? 'active' : '' }}">Privacy Policy</a></li>
                    <li class="navbar-item"><a href="#contact-us" class="navbar-link">Contact Us</a></li>
                </ul>
            </nav>

            <div class="header-right">
                <button class="download-btn">
                    <i class="bi bi-download"></i>
                    Download App
                </button>
                <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="mobileMenu">
                    <i class="bi bi-list"></i>
                </button>
            </div>

        </div>
    </header>

    <!-- ===== HERO ===== -->
    <section class="hero-section">
        <div class="main-container">
            <div class="hero-container">

                <div class="hero-content animate-fade-up">

                    <h6 class="hero-tag">
                        <i class="bi bi-patch-check-fill"></i>
                        Trusted by 10,000+ users
                    </h6>

                    <h1 class="hero-title">
                        Order Medicines <span>Online</span>, Anytime.
                    </h1>

                    <p class="hero-description">
                        Fast delivery, genuine medicines, and secure payments.
                        Experience the clinical sanctuary of modern pharmacy right at your doorstep.
                    </p>

                    <div class="hero-buttons">
                        <button class="store-btn">
                            <i class="bi bi-apple"></i>
                            App Store
                        </button>
                        <button class="store-btn store-btn-outline">
                            <i class="bi bi-google-play"></i>
                            Play Store
                        </button>
                    </div>

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

                <div class="hero-image-area animate-fade-up">
                    <div class="hero-image-glow"></div>
                    <img src="image/hero-image.webp" alt="Vitality Care medicine delivery illustration" loading="eager"
                        class="hero-img">
                    <!-- Floating badges -->
                    <div class="float-badge badge-1">
                        <i class="bi bi-truck"></i>
                        <span>60 min delivery</span>
                    </div>
                    <div class="float-badge badge-2">
                        <i class="bi bi-patch-check-fill"></i>
                        <span>Verified Medicines</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ===== TRUST ===== -->
    <section class="trust-section">
        <div class="main-container">
            <div class="trust-heading animate-fade-up">
                <h3 class="trust-title">Trust & Certifications</h3>
                <p class="trust-subtitle">Verified by industry leaders.</p>
            </div>
            <div class="trust-card-wrapper">

                <div class="trust-card animate-scale" style="--delay:0s">
                    <div class="trust-icon"><i class="bi bi-award-fill"></i></div>
                    <h4 class="trust-card-title">PHARMACY LICENSE</h4>
                    <p class="trust-card-text">State Board Certified</p>
                </div>

                <div class="trust-card animate-scale" style="--delay:0.1s">
                    <div class="trust-icon"><i class="bi bi-file-earmark-lock-fill"></i></div>
                    <h4 class="trust-card-title">HIPAA COMPLIANCE</h4>
                    <p class="trust-card-text">Secure Data Handling</p>
                </div>

                <div class="trust-card animate-scale" style="--delay:0.2s">
                    <div class="trust-icon"><i class="bi bi-shield-fill-check"></i></div>
                    <h4 class="trust-card-title">FDA APPROVAL</h4>
                    <p class="trust-card-text">Quality Standards Met</p>
                </div>

                <div class="trust-card animate-scale" style="--delay:0.3s">
                    <div class="trust-icon"><i class="bi bi-patch-check-fill"></i></div>
                    <h4 class="trust-card-title">ISO CERTIFIED</h4>
                    <p class="trust-card-text">Safety Management</p>
                </div>

            </div>
        </div>
    </section>

    <!-- ===== STEPS ===== -->
    <section class="steps-section">
        <div class="main-container">

            <div class="steps-heading animate-fade-up">
                <h2 class="steps-title">Your Health in <span>4 Simple Steps</span></h2>
                <p class="steps-subtitle">
                    Skip the queue. Our platform integrates high-end technology with clinical expertise to bring wellness to your
                    phone.
                </p>
            </div>

            <div class="steps-grid">

                <div class="steps-card large-card animate-fade-up" style="--delay:0s">
                    <div class="steps-content">
                        <div class="steps-icon"><i class="bi bi-search"></i></div>
                        <h3>Easy Medicine Search</h3>
                        <p>Find your prescriptions in seconds with our AI-powered search and category filters. We house over 50k+
                            genuine medicines.</p>
                    </div>
                    <div class="steps-image">
                        <img src="image/Medicine-bottle.webp" alt="Medicine bottle" loading="lazy">
                    </div>
                </div>

                <div class="steps-card animate-fade-up" style="--delay:0.1s">
                    <div class="steps-icon"><i class="bi bi-geo-alt"></i></div>
                    <h3>Order Tracking</h3>
                    <p>Real-time GPS tracking for every delivery. Know exactly when your essentials will arrive.</p>
                </div>

                <div class="steps-card animate-fade-up" style="--delay:0.2s">
                    <div class="steps-icon"><i class="bi bi-credit-card"></i></div>
                    <h3>Secure Payments</h3>
                    <p>UPI, Credit Cards, or Cash on Delivery. All transactions are protected with bank-grade security.</p>
                </div>

                <div class="steps-card active-card animate-fade-up" style="--delay:0.3s">
                    <div class="steps-icon active-icon"><i class="bi bi-capsule-pill"></i></div>
                    <h3>Verified Medicines</h3>
                    <p>Every pill is sourced directly from certified pharmacies and licensed manufacturers.</p>
                </div>

                <div class="steps-card animate-fade-up" style="--delay:0.4s">
                    <div class="steps-icon"><i class="bi bi-lightning-charge"></i></div>
                    <h3>Fast Delivery</h3>
                    <p>Urgent needs deserve urgent speed. Hyper-local delivery network for express arrivals.</p>
                </div>

            </div>
        </div>
    </section>

    <!-- ===== HEALTHCARE ===== -->
    <section id="about-us" class="healthcare-section">
        <div class="main-container">
            <div class="healthcare-container">

                <div class="healthcare-image-box animate-fade-up">
                    <img src="image/Medical-Science.webp" alt="Medical science laboratory" class="healthcare-image" loading="lazy">
                    <div class="trust-badge">
                        <h4>15+</h4>
                        <p>YEARS OF TRUST</p>
                    </div>
                    <div class="hc-ring hc-ring-1"></div>
                    <div class="hc-ring hc-ring-2"></div>
                </div>

                <div class="healthcare-content animate-fade-up">
                    <span class="section-eyebrow">About Us</span>
                    <h2 class="healthcare-title">Redefining Healthcare <br>Through Precision</h2>
                    <p class="healthcare-text">
                        At Vitality Care, we believe that premium health services should be accessible to everyone. Our platform
                        integrates advanced pharmaceutical logistics with a human-centric approach, ensuring you get exactly what you
                        need, when you need it.
                    </p>
                    <ul class="healthcare-list">
                        <li><i class="bi bi-check-circle-fill"></i>Certified Pharmaceutical Standards</li>
                        <li><i class="bi bi-check-circle-fill"></i>Direct Manufacturer Partnerships</li>
                        <li><i class="bi bi-check-circle-fill"></i>Global Quality Certifications</li>
                    </ul>
                    <button class="healthcare-btn">
                        <a href="Privacy.html">Learn more about our standards</a>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>

            </div>
        </div>
    </section>

    <!-- ===== PHARMACY ===== -->
    <section class="pharmacy-section">
        <divclass="main-container>

            <div class="pharmacy-container">

                <div class="pharmacy-top">
                    <div class="pharmacy-heading animate-fade-up">
                        <h5>OUR PHARMACY</h5>
                        <h2>Essential Medications</h2>
                    </div>
                    <div class="pharmacy-tabs">
                        <button class="tab-btn">Trending</button>
                        <button class="tab-btn active">Antibiotics</button>
                        <button class="tab-btn">Vitamins</button>
                    </div>
                </div>

                <div class="scroll-buttons">
                    <button class="scroll-btn" id="scrollLeft" aria-label="Scroll left"><i class="bi bi-arrow-left"></i></button>
                    <button class="scroll-btn" id="scrollRight" aria-label="Scroll right"><i class="bi bi-arrow-right"></i></button>
                </div>

                <div class="pharmacy-grid">
                    @foreach ($medicines as $medicine)
                    <div class="medicine-card">
                        <div class="medicine-image">
                            <img src="{{ $medicine->image }}" alt="{{ $medicine->name }}" loading="lazy">
                        </div>
                        <div class="medicine-content">
                            <h4>{{ $medicine->name }}</h4>
                            <p>{{ $medicine->description }}</p>
                            <div class="medicine-bottom">

                                @php
                                $finalPrice = $medicine->price -
                                (($medicine->price * $medicine->discount) / 100);
                                @endphp

                                <div>
                                    <span class="old-price">
                                        ₹{{ number_format($medicine->price, 2) }}
                                    </span>

                                    <span class="medicine-price">
                                        ₹{{ number_format($finalPrice, 2) }}
                                    </span>

                                    <span class="discount-badge">
                                        {{ $medicine->discount }}% OFF
                                    </span>
                                </div>

                                <button class="cart-btn" aria-label="Add to cart">
                                    <i class="bi bi-cart-check-fill"></i>
                                </button>

                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>

            </div>
            </div>

    </section>

    <!-- ===== scroller APP ===== -->
    <section class="partners-section">
        <div class="main-container">
            <div class="partners-heading animate-fade-up">
                <span class="section-eyebrow">Trusted Brands</span>
                <h2 class="section-title">We Stock Products From <span>Leading Brands</span></h2>
                <p>All products sourced directly from certified manufacturers and authorised distributors.</p>
            </div>
        </div>
        <div class="partners-track-wrapper">
            <div class="partners-track" id="partnersTrack" aria-hidden="true">
                <span class="partner-logo"><img src="image/partner/Cipla.webp" alt="Brand 1"></span>
                <span class="partner-logo"><img src="image/partner/Dabur.webp" alt="Brand 2"></span>
                <span class="partner-logo"><img src="image/partner/patanjali.webp" alt="Brand 3"></span>
                <span class="partner-logo"><img src="image/partner/pfizer.webp" alt="Brand 4"></span>
                <span class="partner-logo"><img src="image/partner/Dabur.webp" alt="Brand 5"></span>
                <span class="partner-logo"><img src="image/partner/patanjali.webp" alt="Brand 6"></span>
            </div>
        </div>
    </section>


    <!-- ===== MEDICAL APP ===== -->
    <section class="medical-app-section">
        <div class="main-container">
            <div class="medical-app-container">

                <div class="medical-app-content-ap animate-fade-up">

                    <h2 class="medical-app-content-text">
                        A Medical Hub in <span>Your Pocket</span>
                    </h2>

                    <div class="medical-feature">
                        <div class="feature-icon"><i class="bi bi-file-earmark-medical"></i></div>
                        <div class="feature-text">
                            <h4>Clinical Records</h4>
                            <p>View your entire purchase history and medical bills in one place.</p>
                        </div>
                    </div>

                    <div class="medical-feature">
                        <div class="feature-icon"><i class="bi bi-bell"></i></div>
                        <div class="feature-text">
                            <h4>Dose Reminders</h4>
                            <p>Set smart alarms for your daily medication schedule.</p>
                        </div>
                    </div>

                    <div class="medical-feature">
                        <div class="feature-icon"><i class="bi bi-person-heart"></i></div>
                        <div class="feature-text">
                            <h4>Consult Experts</h4>
                            <p>Direct access to pharmacists for medication guidance.</p>
                        </div>
                    </div>

                </div>

                <div class="medical-mobile-wrapper animate-fade-up">
                    <div class="mobile-card">
                        <img src="image/mockup-ui-1.webp" alt="Vitality Care app screen" loading="lazy">
                    </div>
                    <div class="mobile-card center-card">
                        <img src="image/mockup-ui-2.webp" alt="Vitality Care app screen" loading="lazy">
                    </div>
                    <div class="mobile-card">
                        <img src="image/mokupe-ui-3.webp" alt="Vitality Care app screen" loading="lazy">
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ===== DOWNLOAD ===== -->
    <section class="download-app-section">
        <div class="main-container">
            <div class="download-app-container">
                <div class="download-bg-blob blob-1"></div>
                <div class="download-bg-blob blob-2"></div>

                <div class="download-app-content animate-fade-up">
                    <h2>Ready for a Healthier You?<br>Download the App Now.</h2>
                    <p>Join 10,000+ happy users and get your first medicine delivered in under 60 minutes.</p>

                    <div class="download-buttons">
                        <button class="store-btn">
                            <i class="bi bi-apple"></i>
                            <div>
                                <span>DOWNLOAD ON THE</span>
                                <h4>App Store</h4>
                            </div>
                        </button>
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
        </div>
    </section>

    <!-- ===== HEALTH BLOG (NEW 10) ===== -->
    <section class="blog-section" aria-labelledby="blog-heading">
        <div class="main-container">
            <div class="animate-fade-up" style="text-align:center">
                <span class="section-eyebrow">Health Tips</span>
                <h2 class="section-title" id="blog-heading">From Our <span>Health Blog</span></h2>
                <p class="section-subtitle">Expert-written articles to help you make better health decisions every day.</p>
            </div>
            <div class="blog-grid">
                <article class="blog-card animate-fade-up" style="--delay:.0s">
                    <div class="blog-thumb">
                        <img src="image/Evidence-Based.webp" alt="Article about seasonal immunity boosters" loading="lazy">
                    </div>
                    <div class="blog-body">
                        <span class="blog-tag">Immunity</span>
                        <h3>5 Evidence-Based Ways to Strengthen Your Immune System</h3>
                        <p>Discover clinically backed strategies to keep your immune system strong through monsoon and winter seasons.</p>
                        <div class="blog-meta">
                            <span><i class="bi bi-calendar3" aria-hidden="true"></i> June 2025</span>
                            <span><i class="bi bi-clock" aria-hidden="true"></i> 4 min read</span>
                        </div>
                    </div>
                </article>
                <article class="blog-card animate-fade-up" style="--delay:.1s">
                    <div class="blog-thumb">
                        <img src="image/Essential-Guide-to-Medicine-Labels.webp" alt="Guide to reading medicine labels correctly" loading="lazy">
                    </div>
                    <div class="blog-body">
                        <span class="blog-tag">Medication</span>
                        <h3>How to Read a Medicine Label Correctly</h3>
                        <p>Understanding expiry dates, dosage instructions, and storage guidelines can prevent serious medication errors.</p>
                        <div class="blog-meta">
                            <span><i class="bi bi-calendar3" aria-hidden="true"></i> May 2025</span>
                            <span><i class="bi bi-clock" aria-hidden="true"></i> 3 min read</span>
                        </div>
                    </div>
                </article>
                <article class="blog-card animate-fade-up" style="--delay:.2s">
                    <div class="blog-thumb">
                        <img src="image/Healthy-meal-plate.webp" alt="Article on diabetes management tips" loading="lazy">
                    </div>
                    <div class="blog-body">
                        <span class="blog-tag">Chronic Care</span>
                        <h3>Managing Diabetes Day-to-Day: A Practical Guide</h3>
                        <p>From diet adjustments to medication schedules — a comprehensive guide for patients living with Type 2 diabetes.</p>
                        <div class="blog-meta">
                            <span><i class="bi bi-calendar3" aria-hidden="true"></i> April 2025</span>
                            <span><i class="bi bi-clock" aria-hidden="true"></i> 6 min read</span>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- ===== CONTACT ===== -->
    <section id="contact-us" class="contact-section">
        <div class="main-container">
            <div class="contact-container">

                <div class="contact-content animate-fade-up">
                    <h2>Get in Touch</h2>
                    <p class="contact-text">Our team of medical experts and customer support is ready to help you 24/7.</p>

                    <div class="contact-info">
                        <div class="contact-icon"><i class="bi bi-telephone"></i></div>
                        <div class="contact-details">
                            <span>EMERGENCY LINE</span>
                            <h4>+91 73003 02627</h4>
                        </div>
                    </div>

                    <div class="contact-info">
                        <div class="contact-icon"><i class="bi bi-envelope"></i></div>
                        <div class="contact-details">
                            <span>EMAIL SUPPORT</span>
                            <h4>medic2pharmacy@gmail.com</h4>
                        </div>
                    </div>

                    <div class="contact-info">
                        <div class="contact-icon"><i class="bi bi-geo-alt"></i></div>
                        <div class="contact-details">
                            <span>MAIN PHARMACY</span>
                            <h4>263/117 Pratap Nager Sector-26, Jaipur Rajasthan 302033</h4>
                        </div>
                    </div>
                </div>

                <div class="contact-form-box animate-fade-up">
                    <form class="contact-form" id="contactForm">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" placeholder="Enter your full name">
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" placeholder="Enter your phone number">
                        </div>
                        <div class="form-group">
                            <label>Your Message</label>
                            <textarea placeholder="How can we help you?"></textarea>
                        </div>
                        <button type="submit" class="contact-btn">
                            <span>Send Message</span>
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>

    <!-- ===== LOCATION ===== -->
    <section class="location-section">
        <div class="main-container">
            <div class="location-container">

                <div class="location-content animate-fade-up">
                    <div class="location-st">




                        <h5>OUR LOCATIONS</h5>
                        <h2>Visit Our Pharmacy</h2>
                        <p class="location-text">Experience professional care in person at our flagship medical facility.</p>
                    </div>

                    <iframe src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3559.5231202873565!2d75.8108764754375!3d26.855116076681483!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMjbCsDUxJzE4LjQiTiA3NcKwNDgnNDguNCJF!5e0!3m2!1sen!2sin!4v1781343712573!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

                </div>
            </div>
    </section>

    <!-- ===== TESTIMONIALS — INFINITY SCROLLER ===== -->
    <section class="testimonials-section" aria-labelledby="reviews-heading">
        <div class="main-container" style="text-align:center">
            <span class="section-eyebrow">Customer Reviews</span>
            <h2 class="section-title" id="reviews-heading">What Our <span>Customers Say</span></h2>
            <p class="section-subtitle">Thousands of happy customers across Jaipur trust Vitality Care every day.</p>
        </div>

        <div class="testimonials-scroller" role="region" aria-label="Scrolling customer testimonials">
            <div class="testimonials-track">
                <!-- Card 1 -->
                <div class="testimonial-card">
                    <div class="testimonial-stars" aria-label="5 out of 5 stars">★★★★★</div>
                    <p class="testimonial-text">"Ordered medicines at midnight and they arrived within an hour. The pharmacist called to confirm my prescription. Absolutely brilliant service!"</p>
                    <div class="testimonial-author">
                        <div class="author-avatar" aria-hidden="true"><img src="image/review/review-image.webp" alt="review-image"></div>
                        <div>
                            <div class="author-name">Rajesh Kumar</div>
                            <div class="author-city">Jaipur, Rajasthan</div>
                        </div>
                    </div>
                    <div class="quote-mark" aria-hidden="true">"</div>
                </div>
                <!-- Card 2 -->
                <div class="testimonial-card">
                    <div class="testimonial-stars" aria-label="5 out of 5 stars">★★★★★</div>
                    <p class="testimonial-text">"The prescription upload feature saved me so much time. Medicines were exactly right. The app is intuitive and delivery was super fast."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar" aria-hidden="true"><img src="image/review/review-image-1.webp" alt="review-image"></div>
                        <div>
                            <div class="author-name">Sunita Agarwal</div>
                            <div class="author-city">Pratap Nagar, Jaipur</div>
                        </div>
                    </div>
                    <div class="quote-mark" aria-hidden="true"></div>
                </div>
                <!-- Card 3 -->
                <div class="testimonial-card">
                    <div class="testimonial-stars" aria-label="5 out of 5 stars">★★★★★</div>
                    <p class="testimonial-text">"Best online pharmacy in Jaipur. Their Family plan saved us over ₹2,000 last month. Genuine products and excellent customer care."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar" aria-hidden="true"><img src="image/review/review-image-2.webp" alt="review-image"></div>
                        <div>
                            <div class="author-name">Mohit Verma</div>
                            <div class="author-city">Malviya Nagar, Jaipur</div>
                        </div>
                    </div>
                    <div class="quote-mark" aria-hidden="true">"</div>
                </div>
                <!-- Card 4 -->
                <div class="testimonial-card">
                    <div class="testimonial-stars" aria-label="5 out of 5 stars">★★★★★</div>
                    <p class="testimonial-text">"Vitality Care is my go-to pharmacy now. Same-day delivery and the app is so easy to use. Never going back to the local store!"</p>
                    <div class="testimonial-author">
                        <div class="author-avatar" aria-hidden="true"><img src="image/review/review-image-3.webp" alt="review-image"></div>
                        <div>
                            <div class="author-name">Priya Sharma</div>
                            <div class="author-city">Vaishali Nagar, Jaipur</div>
                        </div>
                    </div>
                    <div class="quote-mark" aria-hidden="true">"</div>
                </div>
                <!-- Card 5 -->
                <div class="testimonial-card">
                    <div class="testimonial-stars" aria-label="5 out of 5 stars">★★★★★</div>
                    <p class="testimonial-text">"The refill reminder is a lifesaver for my father's daily medicines. The team is always polite and deliveries are always on time."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar" aria-hidden="true"><img src="image/review/review-image-4.webp" alt="review-image"></div>
                        <div>
                            <div class="author-name">Amit Joshi</div>
                            <div class="author-city">Mansarovar, Jaipur</div>
                        </div>
                    </div>
                    <div class="quote-mark" aria-hidden="true">"</div>
                </div>
                <!-- Card 6 -->
                <div class="testimonial-card">
                    <div class="testimonial-stars" aria-label="5 out of 5 stars">★★★★★</div>
                    <p class="testimonial-text">"Trusted source for genuine medicines. I appreciate the pharmacist consultation feature — got expert advice without leaving home."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar" aria-hidden="true"><img src="image/review/review-image-5.webp" alt="review-image"></div>
                        <div>
                            <div class="author-name">Neha Gupta</div>
                            <div class="author-city">C-Scheme, Jaipur</div>
                        </div>
                    </div>
                    <div class="quote-mark" aria-hidden="true">"</div>
                </div>

                <!-- ===== DUPLICATE SET for seamless loop ===== -->
                <div class="testimonial-card">
                    <div class="testimonial-stars" aria-label="5 out of 5 stars">★★★★★</div>
                    <p class="testimonial-text">"Ordered medicines at midnight and they arrived within an hour. The pharmacist called to confirm my prescription. Absolutely brilliant service!"</p>
                    <div class="testimonial-author">
                        <div class="author-avatar" aria-hidden="true"><img src="image/review/review-image-6.webp" alt="review-image"></div>
                        <div>
                            <div class="author-name">Rajesh Kumar</div>
                            <div class="author-city">Jaipur, Rajasthan</div>
                        </div>
                    </div>
                    <div class="quote-mark" aria-hidden="true">"</div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-stars" aria-label="5 out of 5 stars">★★★★★</div>
                    <p class="testimonial-text">"The prescription upload feature saved me so much time. Medicines were exactly right. The app is intuitive and delivery was super fast."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar" aria-hidden="true"><img src="assets/image/review/review-image-7.webp" alt="review-image"></div>
                        <div>
                            <div class="author-name">Sunita Agarwal</div>
                            <div class="author-city">Pratap Nagar, Jaipur</div>
                        </div>
                    </div>
                    <div class="quote-mark" aria-hidden="true">"</div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-stars" aria-label="5 out of 5 stars">★★★★★</div>
                    <p class="testimonial-text">"Best online pharmacy in Jaipur. Their Family plan saved us over ₹2,000 last month. Genuine products and excellent customer care."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar" aria-hidden="true"><img src="assets/image/review/review-image-8.webp" alt="review-image"></div>
                        <div>
                            <div class="author-name">Mohit Verma</div>
                            <div class="author-city">Malviya Nagar, Jaipur</div>
                        </div>
                    </div>
                    <div class="quote-mark" aria-hidden="true">"</div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-stars" aria-label="5 out of 5 stars">★★★★★</div>
                    <p class="testimonial-text">"Vitality Care is my go-to pharmacy now. Same-day delivery and the app is so easy to use. Never going back to the local store!"</p>
                    <div class="testimonial-author">
                        <div class="author-avatar" aria-hidden="true"><img src="assets/image/review/review-image-9.webp" alt="review-image"></div>
                        <div>
                            <div class="author-name">Priya Sharma</div>
                            <div class="author-city">Vaishali Nagar, Jaipur</div>
                        </div>
                    </div>
                    <div class="quote-mark" aria-hidden="true">"</div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-stars" aria-label="5 out of 5 stars">★★★★★</div>
                    <p class="testimonial-text">"The refill reminder is a lifesaver for my father's daily medicines. The team is always polite and deliveries are always on time."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar" aria-hidden="true"><img src="assets/image/review/review-image-4.webp" alt="review-image"></div>
                        <div>
                            <div class="author-name">Amit Joshi</div>
                            <div class="author-city">Mansarovar, Jaipur</div>
                        </div>
                    </div>
                    <div class="quote-mark" aria-hidden="true">"</div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-stars" aria-label="5 out of 5 stars">★★★★★</div>
                    <p class="testimonial-text">"Trusted source for genuine medicines. I appreciate the pharmacist consultation feature — got expert advice without leaving home."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar" aria-hidden="true"><img src="assets/image/review/review-image-5.webp" alt="review-image"></div>
                        <div>
                            <div class="author-name">Neha Gupta</div>
                            <div class="author-city">C-Scheme, Jaipur</div>
                        </div>
                    </div>
                    <div class="quote-mark" aria-hidden="true">"</div>
                </div>
            </div>
        </div>
    </section>


    <!-- ===== FOOTER ===== -->
    <footer class="footer-section" aria-label="Site footer">
        <div class="footer-container">
            <div class="footer-col footer-brand">
                <p class="footer-logo">Vitality Care</p>
                <p class="footer-text">Your trusted partner for pharmaceutical excellence and professional healthcare in Jaipur, Rajasthan.</p>
                <div class="footer-social" aria-label="Social media links">
                    <a href="#" aria-label="Follow Vitality Care on Instagram"><i class="bi bi-instagram" aria-hidden="true"></i></a>
                    <a href="#" aria-label="Follow Vitality Care on Facebook"><i class="bi bi-facebook" aria-hidden="true"></i></a>
                    <a href="#" aria-label="Follow Vitality Care on Twitter/X"><i class="bi bi-twitter-x" aria-hidden="true"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h3 class="footer-heading">Shop Collections</h3>
                <ul class="footer-links">
                    <li><a href="#categories">Vitamins &amp; Supplements</a></li>
                    <li><a href="#categories">First Aid Essentials</a></li>
                    <li><a href="#categories">Chronic Care</a></li>
                    <li><a href="#categories">Dermatology &amp; Skin Care</a></li>
                    <li><a href="#categories">Mother &amp; Baby Care</a></li>
                    <li><a href="#categories">Personal Hygiene</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3 class="footer-heading">Support Hotline</h3>
                <div class="footer-hotline">
                    <i class="bi bi-headset" aria-hidden="true"></i>
                    <span><a href="tel:+917300302627" style="color:inherit">+91 73003 02627</a></span>
                </div>
                <p class="footer-small-text">Available 24/7 for emergency prescriptions and medical guidance.</p>
                <div class="footer-hours">
                    <span class="hours-label">PHARMACY HOURS</span>
                    <div class="hours-info">
                        <i class="bi bi-clock" aria-hidden="true"></i>
                        <p>Open 24/7, 365 Days</p>
                    </div>
                </div>
            </div>
            <div class="footer-col">
                <h3 class="footer-heading">Main Pharmacy</h3>
                <div class="footer-location">
                    <i class="bi bi-geo-alt" aria-hidden="true"></i>
                    <address>
                        <p>263/117 Pratap Nager,<br>Sector-26, Jaipur,<br>Rajasthan 302033</p>
                    </address>
                </div>
                <a href="https://maps.google.com/?q=263+Pratap+Nager+Sector+26+Jaipur" class="direction-link" target="_blank" rel="noopener noreferrer" aria-label="Get directions on Google Maps">GET DIRECTIONS <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
            <div class="footer-col">
                <h3 class="footer-heading">Certifications</h3>
                <div class="footer-badges">
                    <div class="footer-badge">
                        <i class="bi bi-patch-check-fill" aria-hidden="true"></i>
                        <span>VERIFIED<br>PHARMACY</span>
                    </div>
                    <div class="footer-badge">
                        <i class="bi bi-shield-fill-check" aria-hidden="true"></i>
                        <span>HIPAA<br>COMPLIANT</span>
                    </div>
                    <div class="footer-badge">
                        <i class="bi bi-award-fill" aria-hidden="true"></i>
                        <span>ISO<br>CERTIFIED</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Vitality Care Pharmacy. All rights reserved. | <a href="Privacy.html" style="color:#64748b;text-decoration:none;">Privacy Policy</a></p>
        </div>
    </footer>

</body>
<script src="{{ asset('js/web.js') }}"></script>

</html>