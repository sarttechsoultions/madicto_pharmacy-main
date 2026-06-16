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

  <link rel="icon" type="image/png" href="assets/image/favicon.webp">
  <link rel="preload" href="image/hero-image.png" as="image">
  <link rel="stylesheet" href="{{ asset('css/main.css') }}">
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
          <li class="navbar-item"><a href="index.html#about-us" class="navbar-link">About</a></li>
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




  <!--  -->

  <section class="legal-section">

    <div class="legal-container">

      <!-- CONTENT -->
      <div class="legal-content">

        <div class="legal-tag">
          Compliance Dashboard
        </div>

        <h2 class="legal-title">
          Legal Documents &
          <span>Privacy Policy</span>
        </h2>

        <p class="legal-text">
          Verified and certified pharmacy compliance details.
          We operate with full transparency to ensure your
          healthcare journey is safe, legal, and private.
        </p>

      </div>

      <!-- BOTTOM -->
      <div class="legal-bottom">

        <!-- ITEM -->
        <div class="legal-item">
          <i class="bi bi-bank"></i>
          <p>Government Health Dept</p>
        </div>

        <!-- ITEM -->
        <div class="legal-item">
          <i class="bi bi-hospital"></i>
          <p>Pharmacy Council</p>
        </div>

        <!-- ITEM -->
        <div class="legal-item">
          <i class="bi bi-shield-check"></i>
          <p>ISO 27001 Certified</p>
        </div>

      </div>

    </div>

  </section>

  <!-- =========================
       REGULATORY SECTION
  ========================== -->

  <section class="regulatory-section">

    <div class="regulatory-container">

      <!-- HEADING -->
      <div class="regulatory-heading">

        <h2>
          Regulatory Compliance
        </h2>

        <div class="heading-line"></div>

      </div>

      <!-- GRID -->
      <div class="regulatory-grid">

        <!-- CARD 1 -->
        <div class="regulatory-card">

          <div class="verified-badge">
            Verified ✅
          </div>

          <div class="regulatory-icon pink-icon">
            <i class="bi bi-file-earmark-medical"></i>
          </div>

          <h3>
            Drug License
          </h3>

          <p>
            Form 20 & 21 authorized for retail distribution
            of pharmaceutical products.
          </p>

          <button class="document-btn">
            View Document
            <i class="bi bi-box-arrow-up-right"></i>
          </button>

        </div>

        <!-- CARD 2 -->
        <div class="regulatory-card">

          <div class="verified-badge">
            Verified ✅
          </div>

          <div class="regulatory-icon gray-icon">
            <i class="bi bi-receipt"></i>
          </div>

          <h3>
            GST Registration
          </h3>

          <p>
            Central and State Goods and Services Tax
            compliance certificate.
          </p>

          <button class="document-btn">
            View Document
            <i class="bi bi-box-arrow-up-right"></i>
          </button>

        </div>

        <!-- CARD 3 -->
        <div class="regulatory-card">

          <div class="verified-badge">
            Verified ✅
          </div>

          <div class="regulatory-icon blue-icon">
            <i class="bi bi-hospital"></i>
          </div>

          <h3>
            Pharmacy Registration
          </h3>

          <p>
            State Pharmacy Council registration for
            professional healthcare services.
          </p>

          <button class="document-btn">
            View Document
            <i class="bi bi-box-arrow-up-right"></i>
          </button>

        </div>

        <!-- CARD 4 -->
        <div class="regulatory-card">

          <div class="verified-badge">
            Verified ✅
          </div>

          <div class="regulatory-icon gray-icon">
            <i class="bi bi-building"></i>
          </div>

          <h3>
            Shop Act License
          </h3>

          <p>
            Establishment certificate for commercial
            pharmaceutical operations.
          </p>

          <button class="document-btn">
            View Document
            <i class="bi bi-box-arrow-up-right"></i>
          </button>

        </div>

      </div>

    </div>

  </section>

  <!-- PRIVACY SECTION -->

  <section class="privacy-section">

    <div class="privacy-container">

      <div class="privacy-heading">

        <h2>
          Privacy Policy
        </h2>

        <p>
          Your trust is our most valuable asset.
          Learn how we protect and manage your
          sensitive healthcare information.
        </p>

      </div>

      <div class="privacy-wrapper">

        <!-- ITEM -->

        <div class="privacy-item active">

          <div class="privacy-header">

            <div class="privacy-left">

              <div class="privacy-number">
                01
              </div>

              <h3 class="privacy-title">
                Introduction
              </h3>

            </div>

            <i class="bi bi-chevron-down privacy-icon"></i>

          </div>

          <div class="privacy-body">

            <p>
              Digital Apothecary is committed to protecting your personal and medical information. This policy
              describes how we collect, use, and share information when you use our platform for healthcare
              services.
            </p>

          </div>

        </div>

        <!-- ITEM -->

        <div class="privacy-item">

          <div class="privacy-header">

            <div class="privacy-left">

              <div class="privacy-number">
                02
              </div>

              <h3 class="privacy-title">
                Data Collection
              </h3>

            </div>

            <i class="bi bi-chevron-down privacy-icon"></i>

          </div>

          <div class="privacy-body">

            <p>
              We collect information you provide directly (identity, contact details, prescriptions) and automated
              data (device info, IP addresses). All medical data is handled under HIPAA-equivalent encryption
              standards.
            </p>

          </div>

        </div>
        <!--  -->
        <div class="privacy-item">

          <div class="privacy-header">

            <div class="privacy-left">

              <div class="privacy-number">
                03
              </div>

              <h3 class="privacy-title">
                Usage
              </h3>

            </div>

            <i class="bi bi-chevron-down privacy-icon"></i>

          </div>

          <div class="privacy-body">

            <p>
              Data is used primarily to fulfill your orders, provide pharmacist consultation, and improve our delivery
              logistics. We never sell your personal health records to third-party advertisers.
            </p>

          </div>

        </div>
        <!--  -->
        <div class="privacy-item">

          <div class="privacy-header">

            <div class="privacy-left">

              <div class="privacy-number">
                04
              </div>

              <h3 class="privacy-title">
                Protection
              </h3>

            </div>

            <i class="bi bi-chevron-down privacy-icon"></i>

          </div>

          <div class="privacy-body">

            <p>
              We employ multi-layer security including 256-bit SSL encryption, secure data centers, and rigorous
              internal access controls to prevent unauthorized data breaches.
            </p>

          </div>

        </div>
        <!--  -->
        <div class="privacy-item">

          <div class="privacy-header">

            <div class="privacy-left">

              <div class="privacy-number">
                05
              </div>

              <h3 class="privacy-title">
                User Rights
              </h3>

            </div>

            <i class="bi bi-chevron-down privacy-icon"></i>

          </div>

          <div class="privacy-body">

            <p>
              You have the right to access, rectify, or delete your data at any time. You can also opt-out of non-
              essential communications through your account dashboard settings.
            </p>

          </div>

        </div>
      </div>

    </div>

  </section>
  <!--  -->
  <!-- ============================================================
     REFUND POLICY SECTION
     Image: Unsplash pharmacy/medicine stock photo
============================================================ -->
  <section class="refund-section">

    <div class="refund-container">

      <!-- LEFT — IMAGE BLOCK -->
      <div class="refund-image-block">

        <div class="refund-img-wrapper">
          <img
            src="image/refund-img.webp"
            alt="Pharmacy refund assurance"
            class="refund-img"
            loading="lazy" />
          <!-- Floating pill badge -->
          <div class="refund-float-badge">
            <i class="bi bi-arrow-counterclockwise"></i>
            <div>
              <span>Easy Returns</span>
              <p>Within 7 Days</p>
            </div>
          </div>
        </div>

        <!-- Decorative ring -->
        <div class="refund-ring refund-ring-1"></div>
        <div class="refund-ring refund-ring-2"></div>

      </div>

      <!-- RIGHT — CONTENT -->
      <div class="refund-content">

        <div class="refund-eyebrow">
          <i class="bi bi-arrow-repeat"></i>
          Refund & Returns
        </div>

        <h2 class="refund-title">
          Hassle-Free <span>Refund</span> Policy
        </h2>

        <p class="refund-desc">
          Your satisfaction is our commitment. We offer a transparent,
          simple refund process so you can shop with complete confidence.
        </p>

        <!-- Refund steps -->
        <div class="refund-steps">

          <div class="refund-step">
            <div class="refund-step-icon">
              <i class="bi bi-box-seam"></i>
            </div>
            <div class="refund-step-text">
              <h4>Damaged or Wrong Item</h4>
              <p>Report within 48 hours of delivery with photo proof. Full replacement or refund guaranteed.</p>
            </div>
          </div>

          <div class="refund-step">
            <div class="refund-step-icon">
              <i class="bi bi-calendar-check"></i>
            </div>
            <div class="refund-step-text">
              <h4>Expiry / Quality Issues</h4>
              <p>Expired or substandard medicines are eligible for immediate full refund, no questions asked.</p>
            </div>
          </div>

          <div class="refund-step">
            <div class="refund-step-icon">
              <i class="bi bi-x-circle"></i>
            </div>
            <div class="refund-step-text">
              <h4>Order Cancellation</h4>
              <p>Cancel before dispatch for a 100% refund. Post-dispatch cancellations follow our return window policy.</p>
            </div>
          </div>

          <div class="refund-step">
            <div class="refund-step-icon">
              <i class="bi bi-credit-card"></i>
            </div>
            <div class="refund-step-text">
              <h4>Refund Timeline</h4>
              <p>Approved refunds are credited within 5–7 business days to your original payment method.</p>
            </div>
          </div>

        </div>

        <!-- Note -->
        <div class="refund-note">
          <i class="bi bi-info-circle-fill"></i>
          <p>Prescription medicines, opened packs & temperature-sensitive items are non-refundable as per pharmacy regulations.</p>
        </div>

      </div>

    </div>

  </section>


  <!-- ============================================================
     FAQ SECTION
============================================================ -->


  <section class="terms-section">

    <div class="terms-card">

      <!-- TITLE -->
      <h2 class="terms-title">
        Terms & Conditions
      </h2>

      <!-- LIST -->
      <div class="terms-list">

        <!-- ITEM -->
        <div class="terms-item">
          <div class="terms-icon">
            <i class="bi bi-check-circle"></i>
          </div>

          <p>
            Prescription medication can only be dispensed
            upon submission of a valid medical practitioner's
            prescription.
          </p>
        </div>

        <!-- ITEM -->
        <div class="terms-item">
          <div class="terms-icon">
            <i class="bi bi-check-circle"></i>
          </div>

          <p>
            Orders are subject to availability and verification
            by our lead pharmacists.
          </p>
        </div>

        <!-- ITEM -->
        <div class="terms-item">
          <div class="terms-icon">
            <i class="bi bi-check-circle"></i>
          </div>

          <p>
            Delivery timelines provided are estimates and may
            vary based on regulatory protocols and regional logistics.
          </p>
        </div>

        <!-- ITEM -->
        <div class="terms-item">
          <div class="terms-icon">
            <i class="bi bi-check-circle"></i>
          </div>

          <p>
            Users must be 18 years or older to create an account
            and place orders for healthcare products.
          </p>
        </div>

        <!-- ITEM -->
        <div class="terms-item">
          <div class="terms-icon">
            <i class="bi bi-check-circle"></i>
          </div>

          <p>
            All returns and cancellations are governed by our
            specific Refund & Replacement Policy.
          </p>
        </div>

      </div>

      <!-- FOOTER -->
      <div class="terms-footer">

        <div class="terms-line"></div>

        <p>
          Last Updated: June 15, 2024. Digital Apothecary
          reserves the right to modify these terms at any time.
        </p>

      </div>

    </div>

  </section>


  <section class="faq-section">

    <div class="faq-container">

      <!-- TOP HEADING -->
      <div class="faq-heading">

        <div class="faq-eyebrow">
          <i class="bi bi-question-circle"></i>
          FAQs
        </div>

        <h2 class="faq-title">
          Frequently Asked <span>Questions</span>
        </h2>

        <p class="faq-subtitle">
          Everything you need to know about orders, prescriptions,
          privacy, and delivery — answered clearly.
        </p>

      </div>

      <!-- MAIN GRID: image left, accordion right -->
      <div class="faq-grid">

        <!-- LEFT IMAGE -->
        <div class="faq-image-block">

          <img
            src="image/faq-image.webp"
            alt="Pharmacist answering questions"
            class="faq-img"
            loading="lazy" />

          <!-- Stat cards overlaid -->
          <div class="faq-stat faq-stat-1">
            <i class="bi bi-chat-dots-fill"></i>
            <div>
              <strong>24 / 7</strong>
              <span>Support Available</span>
            </div>
          </div>

          <div class="faq-stat faq-stat-2">
            <i class="bi bi-patch-check-fill"></i>
            <div>
              <strong>100%</strong>
              <span>Verified Answers</span>
            </div>
          </div>

        </div>

        <!-- RIGHT ACCORDION -->
        <div class="faq-accordion">

          <div class="faq-item active">
            <div class="faq-question">
              <span>Do I need a prescription to order medicines?</span>
              <div class="faq-toggle"><i class="bi bi-plus-lg"></i></div>
            </div>
            <div class="faq-answer">
              <p>Yes. Schedule H, H1, and X drugs require a valid prescription from a registered medical practitioner. You can upload it during checkout. OTC medicines do not require a prescription.</p>
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question">
              <span>How do I track my order?</span>
              <div class="faq-toggle"><i class="bi bi-plus-lg"></i></div>
            </div>
            <div class="faq-answer">
              <p>Once your order is dispatched, you'll receive an SMS and email with a tracking link. You can also check real-time status in the "My Orders" section of the app.</p>
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question">
              <span>Is my personal health data safe?</span>
              <div class="faq-toggle"><i class="bi bi-plus-lg"></i></div>
            </div>
            <div class="faq-answer">
              <p>Absolutely. All data is encrypted with 256-bit SSL and stored in ISO 27001 certified servers. We never sell your data to third parties. Refer to our Privacy Policy for full details.</p>
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question">
              <span>What if I receive the wrong medicine?</span>
              <div class="faq-toggle"><i class="bi bi-plus-lg"></i></div>
            </div>
            <div class="faq-answer">
              <p>Contact our support within 48 hours with a photo of the wrong item. We will arrange an immediate replacement or full refund — whichever you prefer.</p>
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question">
              <span>Can I cancel my order after placing it?</span>
              <div class="faq-toggle"><i class="bi bi-plus-lg"></i></div>
            </div>
            <div class="faq-answer">
              <p>Yes, orders can be cancelled before they are dispatched for a full refund. Once dispatched, our return policy applies. Prescription orders may have restrictions as per drug regulations.</p>
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question">
              <span>How long does delivery take?</span>
              <div class="faq-toggle"><i class="bi bi-plus-lg"></i></div>
            </div>
            <div class="faq-answer">
              <p>Standard delivery takes 2–4 business days. Express delivery (same day or next day) is available in select pin codes in Jaipur. Delivery times may vary during peak periods or regulatory checks.</p>
            </div>
          </div>

        </div>

      </div>

      <!-- BOTTOM CTA -->
      <div class="faq-cta">
        <p>Still have questions?</p>
        <a href="tel:+917300302627" class="faq-cta-btn">
          <i class="bi bi-telephone-fill"></i>
          Call Us Now
        </a>
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