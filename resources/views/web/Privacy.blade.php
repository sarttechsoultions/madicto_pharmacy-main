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

  /* =========================
  PRIVACY ACCORDION JS
========================= */

  const privacyItems = document.querySelectorAll(".privacy-item");

  privacyItems.forEach((item) => {

    const header = item.querySelector(".privacy-header");

    header.addEventListener("click", () => {

      // CLOSE ALL ITEMS
      privacyItems.forEach((accordionItem) => {

        if (accordionItem !== item) {
          accordionItem.classList.remove("active");
        }

      });

      // OPEN CURRENT ITEM
      item.classList.toggle("active");

    });

  });
</script>

</html>