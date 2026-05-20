<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Medicto Pharmacy – Legal Documents & Privacy Policy</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <style>
        :root {
            --pink: #e91e8c;
            --pink-light: #fce4f3;
            --pink-mid: #f48cc0;
            --dark: #1a1a2e;
            --text: #333344;
            --muted: #6b7280;
            --border: #e5e7eb;
            --bg: #f9fafb;
            --white: #ffffff;
            --green: #22c55e;
            --red: #ef4444;
            --radius: 12px;
            --shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            color: var(--text);
            background: var(--white);
            line-height: 1.6;
            font-size: 15px;
        }

        /* ─── NAV ─── */
        nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 60px;
            border-bottom: 1px solid var(--border);
            background: var(--white);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: 17px;
            color: var(--dark);
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: var(--pink);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 16px;
        }

        .nav-links {
            display: flex;
            gap: 32px;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text);
            font-size: 14px;
            font-weight: 500;
            transition: color .2s;
        }

        .nav-links a:hover {
            color: var(--pink);
        }

        .btn-download {
            background: var(--pink);
            color: #fff;
            border: none;
            border-radius: 24px;
            padding: 10px 22px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, transform .15s;
        }

        .btn-download:hover {
            background: #c4177a;
            transform: translateY(-1px);
        }

        /* ─── HERO ─── */
        .hero {
            text-align: center;
            padding: 64px 60px 40px;
            background: var(--white);
        }

        .badge-compliance {
            display: inline-block;
            background: var(--pink-light);
            color: var(--pink);
            border-radius: 20px;
            padding: 5px 18px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 24px;
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            font-weight: 700;
            color: var(--dark);
            line-height: 1.15;
            margin-bottom: 16px;
        }

        .hero h1 span {
            color: var(--pink);
        }

        .hero p {
            max-width: 500px;
            margin: 0 auto 36px;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.7;
        }

        /* ─── TRUST BADGES ─── */
        .trust-bar {
            display: flex;
            justify-content: center;
            gap: 0;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            max-width: 680px;
            margin: 0 auto 60px;
        }

        .trust-item {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 18px 24px;
            border-right: 1px solid var(--border);
            background: var(--white);
            font-size: 13px;
            font-weight: 500;
            color: var(--text);
        }

        .trust-item:last-child {
            border-right: none;
        }

        .trust-item svg {
            width: 20px;
            height: 20px;
            color: var(--muted);
            flex-shrink: 0;
        }

        /* ─── SECTION ─── */
        section {
            padding: 56px 60px;
        }

        .section-label {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--pink);
            margin-bottom: 4px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: var(--dark);
            margin-bottom: 8px;
            padding-bottom: 12px;
            border-bottom: 3px solid var(--pink);
            display: inline-block;
        }

        .section-sub {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 36px;
        }

        /* ─── REGULATORY CARDS ─── */
        .reg-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            max-width: 780px;
        }

        .reg-card {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            background: var(--white);
            box-shadow: var(--shadow);
            transition: box-shadow .2s;
        }

        .reg-card:hover {
            box-shadow: 0 6px 24px rgba(233, 30, 140, .1);
        }

        .reg-card-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .reg-icon {
            width: 44px;
            height: 44px;
            background: var(--pink-light);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .reg-icon svg {
            width: 22px;
            height: 22px;
            color: var(--pink);
        }

        .verified-badge {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            color: var(--green);
            background: #dcfce7;
            border-radius: 12px;
            padding: 3px 10px;
        }

        .verified-badge::before {
            content: '✓';
            font-weight: 700;
        }

        .reg-card h3 {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 4px;
            color: var(--dark);
        }

        .reg-card p {
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 16px;
            line-height: 1.5;
        }

        .btn-view {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text);
            cursor: pointer;
            transition: background .2s;
            text-decoration: none;
        }

        .btn-view:hover {
            background: var(--pink-light);
            color: var(--pink);
            border-color: var(--pink-mid);
        }

        /* ─── PRIVACY ─── */
        .privacy-section {
            background: var(--bg);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .privacy-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .privacy-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .privacy-header p {
            color: var(--muted);
            font-size: 14px;
            max-width: 440px;
            margin: 0 auto;
            line-height: 1.7;
        }

        .accordion {
            max-width: 760px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .accordion-item {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            transition: box-shadow .2s;
        }

        .accordion-item.open {
            box-shadow: 0 4px 16px rgba(0, 0, 0, .07);
        }

        .accordion-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 24px;
            cursor: pointer;
            user-select: none;
        }

        .accordion-header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .acc-num {
            font-size: 12px;
            font-weight: 700;
            color: var(--muted);
            min-width: 24px;
        }

        .accordion-header h4 {
            font-size: 15px;
            font-weight: 600;
            color: var(--dark);
        }

        .acc-chevron {
            width: 20px;
            height: 20px;
            color: var(--muted);
            transition: transform .25s;
            flex-shrink: 0;
        }

        .accordion-item.open .acc-chevron {
            transform: rotate(180deg);
        }

        .accordion-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height .3s ease, padding .3s;
            padding: 0 24px;
        }

        .accordion-item.open .accordion-body {
            max-height: 200px;
            padding: 0 24px 20px;
        }

        .accordion-body p {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.75;
        }

        /* ─── TERMS ─── */
        .terms-section {
            padding: 60px;
        }

        .terms-box {
            max-width: 760px;
            margin: 0 auto;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 40px;
            box-shadow: var(--shadow);
        }

        .terms-box h2 {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            color: var(--dark);
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }

        .terms-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .terms-list li {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            font-size: 13.5px;
            color: var(--text);
            line-height: 1.65;
        }

        .terms-list li .check {
            width: 18px;
            height: 18px;
            background: var(--pink);
            border-radius: 50%;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .terms-updated {
            font-size: 11px;
            color: var(--muted);
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid var(--border);
        }

        /* ─── MAP ─── */
        .map-section {
            background: var(--dark);
            color: var(--white);
            padding: 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 340px;
            overflow: hidden;
        }

        .map-info {
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .map-info .label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .15em;
            text-transform: uppercase;
            color: var(--pink);
            margin-bottom: 8px;
        }

        .map-info h2 {
            font-family: 'Playfair Display', serif;
            font-size: 34px;
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .map-info p {
            font-size: 13px;
            color: #aab;
            margin-bottom: 28px;
            line-height: 1.65;
        }

        .map-details {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-bottom: 28px;
        }

        .map-detail {
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .map-detail-icon {
            color: var(--pink);
            font-size: 16px;
            margin-top: 1px;
        }

        .map-detail-label {
            font-size: 11px;
            color: #889;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 2px;
        }

        .map-detail-val {
            font-size: 13px;
            color: var(--white);
            line-height: 1.5;
        }

        .btn-directions {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--pink);
            color: #fff;
            border: none;
            border-radius: 24px;
            padding: 12px 24px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s;
            width: fit-content;
            text-decoration: none;
        }

        .btn-directions:hover {
            background: #c4177a;
        }

        .map-visual {
            background: #d1dbe6;
            position: relative;
            overflow: hidden;
        }

        .map-placeholder {
            width: 100%;
            height: 100%;
            min-height: 320px;
            background: linear-gradient(135deg, #c9d8e8 0%, #b8cdd8 50%, #a8becc 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .map-grid-lines {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, .3) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .3) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .map-road-h {
            position: absolute;
            left: 0;
            right: 0;
            height: 10px;
            background: rgba(255, 255, 255, .7);
            border-radius: 2px;
        }

        .map-road-v {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 10px;
            background: rgba(255, 255, 255, .7);
            border-radius: 2px;
        }

        .map-pin {
            position: absolute;
            width: 32px;
            height: 32px;
            background: var(--pink);
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            top: 44%;
            left: 52%;
            box-shadow: 0 4px 12px rgba(233, 30, 140, .5);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .map-pin::after {
            content: '';
            width: 12px;
            height: 12px;
            background: #fff;
            border-radius: 50%;
            transform: rotate(45deg);
        }

        /* ─── CONTACT ─── */
        .contact-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            background: var(--white);
            border-top: 1px solid var(--border);
        }

        .contact-info {
            padding: 60px;
            background: var(--dark);
            color: var(--white);
        }

        .contact-info h2 {
            font-family: 'Playfair Display', serif;
            font-size: 30px;
            margin-bottom: 10px;
        }

        .contact-info .sub {
            font-size: 13px;
            color: #aab;
            margin-bottom: 32px;
            line-height: 1.65;
        }

        .contact-details {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .contact-detail {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .contact-detail-icon {
            width: 36px;
            height: 36px;
            background: rgba(233, 30, 140, .2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--pink);
            font-size: 15px;
            flex-shrink: 0;
        }

        .contact-detail-label {
            font-size: 10px;
            color: #778;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 2px;
        }

        .contact-detail-val {
            font-size: 13px;
            color: var(--white);
        }

        .contact-form {
            padding: 60px;
            background: var(--bg);
        }

        .contact-form h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 24px;
            color: var(--dark);
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 13px 16px;
            font-family: inherit;
            font-size: 13px;
            color: var(--text);
            background: var(--white);
            outline: none;
            transition: border-color .2s;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #bbb;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--pink);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn-send {
            width: 100%;
            background: var(--pink);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s;
            font-family: inherit;
        }

        .btn-send:hover {
            background: #c4177a;
        }

        /* ─── FOOTER ─── */
        footer {
            background: var(--white);
            border-top: 1px solid var(--border);
            padding: 48px 60px 28px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-brand h3 {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .footer-brand p {
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 18px;
            line-height: 1.6;
        }

        .footer-social {
            display: flex;
            gap: 10px;
        }

        .social-btn {
            width: 32px;
            height: 32px;
            border: 1px solid var(--border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            font-size: 13px;
            cursor: pointer;
            transition: background .2s, color .2s;
        }

        .social-btn:hover {
            background: var(--pink);
            color: #fff;
            border-color: var(--pink);
        }

        .footer-col h4 {
            font-size: 13px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 14px;
        }

        .footer-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .footer-col ul li a {
            font-size: 12px;
            color: var(--muted);
            text-decoration: none;
            transition: color .2s;
        }

        .footer-col ul li a:hover {
            color: var(--pink);
        }

        .hotline-num {
            font-size: 16px;
            font-weight: 700;
            color: var(--pink);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 6px;
        }

        .hotline-sub {
            font-size: 11px;
            color: var(--muted);
            line-height: 1.5;
        }

        .open-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #dcfce7;
            color: var(--green);
            border-radius: 12px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 600;
            margin-top: 8px;
        }

        .open-dot {
            width: 6px;
            height: 6px;
            background: var(--green);
            border-radius: 50%;
        }

        .footer-certs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .cert-badge {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 10px;
            font-weight: 600;
            color: var(--text);
            text-align: center;
            line-height: 1.4;
        }

        .footer-bottom {
            border-top: 1px solid var(--border);
            padding-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-bottom p {
            font-size: 11px;
            color: var(--muted);
        }

        .footer-bottom-links {
            display: flex;
            gap: 20px;
        }

        .footer-bottom-links a {
            font-size: 11px;
            color: var(--muted);
            text-decoration: none;
        }

        .footer-bottom-links a:hover {
            color: var(--pink);
        }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 900px) {
            nav {
                padding: 14px 24px;
            }

            section {
                padding: 40px 24px;
            }

            .hero {
                padding: 48px 24px 32px;
            }

            .hero h1 {
                font-size: 34px;
            }

            .reg-grid {
                grid-template-columns: 1fr;
            }

            .map-section {
                grid-template-columns: 1fr;
            }

            .contact-section {
                grid-template-columns: 1fr;
            }

            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }

            .terms-section {
                padding: 40px 24px;
            }

            .map-visual {
                min-height: 220px;
            }

            .footer {
                padding: 36px 24px 20px;
            }
        }
    </style>
</head>

<body>

    <!-- NAV -->
    <nav>
        <div class="logo">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
                </svg>
            </div>
            <div>
                <div style="font-family:'Playfair Display',serif;font-size:15px;line-height:1">MEDICTO</div>
                <div style="font-size:10px;font-weight:400;color:var(--muted);letter-spacing:.08em">PHARMACY</div>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="#">Home</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Contact us</a></li>
        </ul>
        <button class="btn-download">Download App</button>
    </nav>

    <!-- HERO -->
    <div class="hero">
        <div class="badge-compliance">Compliance Dashboard</div>
        <h1>Legal Documents &amp; <span>Privacy Policy</span></h1>
        <p>Verified and certified pharmacy compliance details. We operate with full transparency to ensure your healthcare journey is safe, legal, and private.</p>
        <div class="trust-bar">
            <div class="trust-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 21h18M9 8h1m4 0h1M9 12h1m4 0h1M5 21V7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v14" />
                </svg>
                Government Health Dept
            </div>
            <div class="trust-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <polyline points="9 22 9 12 15 12 15 22" />
                </svg>
                Pharmacy Council
            </div>
            <div class="trust-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
                ISO 27001 Certified
            </div>
        </div>
    </div>

    <!-- REGULATORY -->
    <section>
        <div class="section-label">Regulatory Compliance</div>
        <div class="section-title">Regulatory Compliance</div>
        <p class="section-sub" style="margin-top:10px">All certifications verified and up-to-date</p>
        <div class="reg-grid">

            <div class="reg-card">
                <div class="reg-card-head">
                    <div class="reg-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                            <polyline points="10 9 9 9 8 9" />
                        </svg>
                    </div>
                    <span class="verified-badge">Verified</span>
                </div>
                <h3>Drug License</h3>
                <p>Form 20 &amp; 21 authorises for retail distribution of pharmaceutical products.</p>
                <a href="#" class="btn-view">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    View Document
                </a>
            </div>

            <div class="reg-card">
                <div class="reg-card-head">
                    <div class="reg-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="3" width="20" height="14" rx="2" />
                            <line x1="8" y1="21" x2="16" y2="21" />
                            <line x1="12" y1="17" x2="12" y2="21" />
                        </svg>
                    </div>
                    <span class="verified-badge">Verified</span>
                </div>
                <h3>GST Registration</h3>
                <p>Central and State Goods and Services Tax compliance certificate.</p>
                <a href="#" class="btn-view">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    View Document
                </a>
            </div>

            <div class="reg-card">
                <div class="reg-card-head">
                    <div class="reg-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                    </div>
                    <span class="verified-badge">Verified</span>
                </div>
                <h3>Pharmacy Registration</h3>
                <p>State Pharmacy Council registration for professional healthcare services.</p>
                <a href="#" class="btn-view">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    View Document
                </a>
            </div>

            <div class="reg-card">
                <div class="reg-card-head">
                    <div class="reg-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 21h18M9 8h1m4 0h1M9 12h1m4 0h1M5 21V7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v14" />
                        </svg>
                    </div>
                    <span class="verified-badge">Verified</span>
                </div>
                <h3>Shop Act License</h3>
                <p>Establishment certificate for commercial pharmaceutical operations.</p>
                <a href="#" class="btn-view">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    View Document
                </a>
            </div>

        </div>
    </section>

    <!-- PRIVACY POLICY -->
    <section class="privacy-section">
        <div class="privacy-header">
            <h2>Privacy Policy</h2>
            <p>Your trust is our most valuable asset. Learn how we handle your sensitive health data with clinical precision.</p>
        </div>
        <div class="accordion">

            <div class="accordion-item open" onclick="this.classList.toggle('open')">
                <div class="accordion-header">
                    <div class="accordion-header-left">
                        <span class="acc-num">01</span>
                        <h4>Introduction</h4>
                    </div>
                    <svg class="acc-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9" />
                    </svg>
                </div>
                <div class="accordion-body">
                    <p>Digital Apothecary is committed to protecting your personal and medical information. This policy describes how we collect, use, and share information when you use our platform for healthcare services.</p>
                </div>
            </div>

            <div class="accordion-item open" onclick="this.classList.toggle('open')">
                <div class="accordion-header">
                    <div class="accordion-header-left">
                        <span class="acc-num">02</span>
                        <h4>Data Collection</h4>
                    </div>
                    <svg class="acc-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9" />
                    </svg>
                </div>
                <div class="accordion-body">
                    <p>We collect information you provide directly (identity, contact details, prescriptions) and automated data (device info, IP addresses). All medical data is handled under HIPAA-equivalent encryption standards.</p>
                </div>
            </div>

            <div class="accordion-item" onclick="this.classList.toggle('open')">
                <div class="accordion-header">
                    <div class="accordion-header-left">
                        <span class="acc-num">03</span>
                        <h4>Usage</h4>
                    </div>
                    <svg class="acc-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9" />
                    </svg>
                </div>
                <div class="accordion-body">
                    <p>Data is used primarily to fulfil your orders, provide pharmacist consultation, and improve our delivery logistics. We never sell your personal health records to third-party advertisers.</p>
                </div>
            </div>

            <div class="accordion-item" onclick="this.classList.toggle('open')">
                <div class="accordion-header">
                    <div class="accordion-header-left">
                        <span class="acc-num">04</span>
                        <h4>Protection</h4>
                    </div>
                    <svg class="acc-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9" />
                    </svg>
                </div>
                <div class="accordion-body">
                    <p>We employ multi-layer security including 256-bit SSL encryption, secure data centers, and rigorous internal access controls to prevent unauthorized data breaches.</p>
                </div>
            </div>

            <div class="accordion-item" onclick="this.classList.toggle('open')">
                <div class="accordion-header">
                    <div class="accordion-header-left">
                        <span class="acc-num">05</span>
                        <h4>User Rights</h4>
                    </div>
                    <svg class="acc-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9" />
                    </svg>
                </div>
                <div class="accordion-body">
                    <p>You have the right to access, rectify, or delete your data at any time. You can also opt out of non-essential communications through your account dashboard settings.</p>
                </div>
            </div>

        </div>
    </section>

    <!-- TERMS -->
    <div class="terms-section">
        <div class="terms-box">
            <h2>Terms &amp; Conditions</h2>
            <ul class="terms-list">
                <li><span class="check">✓</span> Prescription medication can only be dispensed upon submission of a valid medical practitioner's prescription.</li>
                <li><span class="check">✓</span> Orders are subject to availability and verification by our lead pharmacists.</li>
                <li><span class="check">✓</span> Delivery timelines provided are estimates and may vary based on regulatory protocols and regional logistics.</li>
                <li><span class="check">✓</span> You must be 18 years or older to create an account and place orders for healthcare products.</li>
                <li><span class="check">✓</span> All returns and cancellations are governed by our specific Refund &amp; Replacement Policy.</li>
            </ul>
            <p class="terms-updated">Last updated: June 15, 2025. Digital Apothecary reserves the right to modify terms at any time.</p>
        </div>
    </div>

    <!-- MAP -->
    <div class="map-section">
        <div class="map-info">
            <div class="label">Our Location</div>
            <h2>Visit Our Pharmacy</h2>
            <p>Experience professional care in person at our flagship medical facility.</p>
            <div class="map-details">
                <div class="map-detail">
                    <div class="map-detail-icon">📍</div>
                    <div>
                        <div class="map-detail-label">Address</div>
                        <div class="map-detail-val">742 Medical Blvd,<br>New York, NY 10012</div>
                    </div>
                </div>
                <div class="map-detail">
                    <div class="map-detail-icon">🕐</div>
                    <div>
                        <div class="map-detail-label">Store Hours</div>
                        <div class="map-detail-val">Monday – Sunday<br>Open 24/7</div>
                    </div>
                </div>
            </div>
            <a href="#" class="btn-directions">📍 Get Directions</a>
        </div>
        <div class="map-visual">
            <div class="map-placeholder">
                <div class="map-grid-lines"></div>
                <div class="map-road-h" style="top:40%"></div>
                <div class="map-road-h" style="top:65%;height:6px;opacity:.5"></div>
                <div class="map-road-v" style="left:48%"></div>
                <div class="map-road-v" style="left:70%;width:6px;opacity:.5"></div>
                <div class="map-pin"></div>
                <div style="position:absolute;bottom:12px;right:12px;background:rgba(255,255,255,.85);border-radius:8px;padding:6px 10px;font-size:11px;font-weight:600;color:var(--dark)">Model Town</div>
            </div>
        </div>
    </div>

    <!-- CONTACT -->
    <div class="contact-section">
        <div class="contact-info">
            <h2>Get in Touch</h2>
            <p class="sub">Our team of medical experts and customer support is ready to help you 24/7.</p>
            <div class="contact-details">
                <div class="contact-detail">
                    <div class="contact-detail-icon">📞</div>
                    <div>
                        <div class="contact-detail-label">Emergency Line</div>
                        <div class="contact-detail-val">+1 (555) 000-1234</div>
                    </div>
                </div>
                <div class="contact-detail">
                    <div class="contact-detail-icon">✉️</div>
                    <div>
                        <div class="contact-detail-label">Email Address</div>
                        <div class="contact-detail-val">care@vitality.com</div>
                    </div>
                </div>
                <div class="contact-detail">
                    <div class="contact-detail-icon">📍</div>
                    <div>
                        <div class="contact-detail-label">Main Pharmacy</div>
                        <div class="contact-detail-val">742 Medical Blvd, NY 10012</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="contact-form">
            <h3>Send us a message</h3>
            <div class="form-group"><input type="text" placeholder="Full Name" /></div>
            <div class="form-group"><input type="tel" placeholder="Phone Number" /></div>
            <div class="form-group"><textarea placeholder="Your Message"></textarea></div>
            <button class="btn-send">Send Message</button>
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <div class="footer-grid">
            <div class="footer-brand">
                <h3>Vitality Care</h3>
                <p>Your trusted partner for pre-professional enrollment and professional healthcare.</p>
                <div class="footer-social">
                    <div class="social-btn">🔗</div>
                    <div class="social-btn">📘</div>
                </div>
            </div>
            <div class="footer-col">
                <h4>Shop Collections</h4>
                <ul>
                    <li><a href="#">Vitamins &amp; Supplements</a></li>
                    <li><a href="#">First Aid Essentials</a></li>
                    <li><a href="#">Obesity Care</a></li>
                    <li><a href="#">Dermatology &amp; Skin Care</a></li>
                    <li><a href="#">Mother &amp; Baby Care</a></li>
                    <li><a href="#">Personal Hygiene</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Support Hotline</h4>
                <div class="hotline-num">📞 1-800-VITALITY</div>
                <p class="hotline-sub">Available 24/7 for emergencies, prescriptions and medical queries</p>
                <div class="open-badge"><span class="open-dot"></span> Open 24/7, 365 Days</div>
            </div>
            <div class="footer-col">
                <h4>Main Pharmacy</h4>
                <ul>
                    <li><a href="#">742 Medical Blvd, Ste 101b, New York, NY 10012, United States</a></li>
                    <li><a href="#">GET DIRECTIONS →</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Professional Certifications</h4>
                <div class="footer-certs">
                    <div class="cert-badge">Licensed<br>Certified</div>
                    <div class="cert-badge">ISO<br>Compliant</div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2025 Medicto Pharmacy. All rights reserved.</p>
            <div class="footer-bottom-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Cookie Policy</a>
            </div>
        </div>
    </footer>

</body>

</html>