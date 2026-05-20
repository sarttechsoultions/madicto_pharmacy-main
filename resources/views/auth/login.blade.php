<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Medicto Pharmacy – Admin Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --pink: #e0176a;
            --pink-light: #f4d6e5;
            --pink-mid: #f48cb8;
            --pink-pale: #fce9f3;
            --pink-btn: #d01260;
            --gray-bg: #f7f4f8;
            --text-dark: #1a1a2e;
            --text-muted: #6b7280;
            --border: #e8dded;
            --white: #ffffff;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--gray-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background-image:
                radial-gradient(ellipse at 80% 0%, rgba(224, 23, 106, 0.10) 0%, transparent 55%),
                radial-gradient(ellipse at 20% 100%, rgba(244, 140, 184, 0.10) 0%, transparent 50%);
        }

        .card {
            background: var(--white);
            border-radius: 28px;
            box-shadow: 0 8px 48px rgba(80, 20, 60, 0.10), 0 2px 8px rgba(0, 0, 0, 0.04);
            display: grid;
            grid-template-columns: 1fr 1.15fr;
            max-width: 960px;
            width: 100%;
            min-height: 600px;
            overflow: hidden;
            animation: fadeUp 0.55s cubic-bezier(.22, .68, 0, 1.2) both;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(28px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ── LEFT PANEL ── */
        .left {
            padding: 44px 48px 36px;
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        /* Logo */
        .logo {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 36px;
        }

        .logo-icon {
            width: 44px;
            height: 38px;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1;
        }

        .logo-text span:first-child {
            font-family: 'Sora', sans-serif;
            font-weight: 800;
            font-size: 17px;
            letter-spacing: 0.04em;
            color: var(--pink);
        }

        .logo-text span:last-child {
            font-family: 'Sora', sans-serif;
            font-weight: 500;
            font-size: 10px;
            letter-spacing: 0.18em;
            color: var(--pink);
            margin-top: 1px;
        }

        /* Heading */
        .heading {
            font-family: 'Sora', sans-serif;
            font-size: 32px;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1.15;
            margin-bottom: 8px;
        }

        .subheading {
            font-size: 14.5px;
            color: var(--text-muted);
            line-height: 1.55;
            margin-bottom: 32px;
            max-width: 300px;
        }

        /* Form */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .forgot-link {
            font-size: 13px;
            font-weight: 600;
            color: var(--pink);
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .forgot-link:hover {
            opacity: 0.75;
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            color: #a89bb5;
            display: flex;
            align-items: center;
        }

        .input-wrap input {
            width: 100%;
            padding: 13px 44px 13px 42px;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14.5px;
            color: var(--text-dark);
            background: #fafafa;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .input-wrap input::placeholder {
            color: #b4acbf;
        }

        .input-wrap input:focus {
            border-color: var(--pink);
            box-shadow: 0 0 0 3px rgba(224, 23, 106, 0.08);
            background: #fff;
        }

        .eye-btn {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            cursor: pointer;
            color: #a89bb5;
            display: flex;
            align-items: center;
            padding: 0;
            transition: color 0.2s;
        }

        .eye-btn:hover {
            color: var(--pink);
        }

        /* Checkbox */
        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 24px;
        }

        .checkbox-row input[type="checkbox"] {
            width: 17px;
            height: 17px;
            border: 1.5px solid var(--border);
            border-radius: 5px;
            accent-color: var(--pink);
            cursor: pointer;
        }

        .checkbox-row label {
            font-size: 13.5px;
            color: var(--text-muted);
            cursor: pointer;
            user-select: none;
        }

        /* Buttons */
        .btn-primary {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #e0176a 0%, #c0125a 100%);
            color: #fff;
            border: none;
            border-radius: 13px;
            font-family: 'Sora', sans-serif;
            font-size: 15.5px;
            font-weight: 700;
            letter-spacing: 0.01em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 20px rgba(224, 23, 106, 0.30);
            transition: box-shadow 0.2s, transform 0.15s;
            margin-bottom: 18px;
        }

        .btn-primary:hover {
            box-shadow: 0 6px 28px rgba(224, 23, 106, 0.42);
            transform: translateY(-1px);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .or-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            color: #bab2c4;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.08em;
        }

        .or-divider::before,
        .or-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .btn-otp {
            width: 100%;
            padding: 14px;
            background: var(--pink-pale);
            color: var(--pink);
            border: none;
            border-radius: 13px;
            font-family: 'Sora', sans-serif;
            font-size: 14.5px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            transition: background 0.2s, transform 0.15s;
            margin-bottom: 0;
        }

        .btn-otp:hover {
            background: #f4d0e8;
            transform: translateY(-1px);
        }

        /* Footer links */
        .left-footer {
            margin-top: auto;
            padding-top: 28px;
            text-align: center;
            font-size: 12.5px;
            color: #b0a8bc;
            line-height: 2;
        }

        .left-footer .footer-links {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .left-footer a {
            color: #9a90a8;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .left-footer a:hover {
            color: var(--pink);
        }

        /* ── RIGHT PANEL ── */
        .right {
            background: linear-gradient(135deg, #f8eef5 0%, #fce4f0 50%, #f0e0f8 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 32px;
        }

        /* Background decoration */
        .right::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse at 60% 20%, rgba(224, 23, 106, 0.13) 0%, transparent 55%),
                radial-gradient(ellipse at 20% 80%, rgba(180, 100, 220, 0.09) 0%, transparent 50%);
            pointer-events: none;
        }

        /* Big photo card */
        .photo-card {
            width: 100%;
            max-width: 420px;
            border-radius: 22px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 16px 56px rgba(100, 20, 80, 0.18);
            animation: floatIn 0.7s 0.15s cubic-bezier(.22, .68, 0, 1.1) both;
        }

        @keyframes floatIn {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.97);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .photo-placeholder {
            width: 100%;
            aspect-ratio: 4/3.2;
            background: linear-gradient(145deg, #7fb8d4 0%, #5a9ab8 40%, #3d7a98 70%, #8a5fa0 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: flex-end;
        }

        /* Doctor silhouette via CSS */
        .doctor-figure {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 56%;
            height: 88%;
            background: linear-gradient(180deg, #d4ecf7 0%, #b8dce8 60%, #8bbdd0 100%);
            border-radius: 50% 50% 0 0 / 30% 30% 0 0;
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }

        .doctor-head {
            width: 42%;
            aspect-ratio: 1;
            border-radius: 50%;
            background: #e8c9a8;
            margin-top: -24px;
            position: relative;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.10);
        }

        .stethoscope {
            position: absolute;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            width: 70%;
            height: 30px;
            border: 3px solid #5a8a9a;
            border-radius: 50%;
            border-bottom: none;
        }

        /* Abstract grid overlay for the photo */
        .grid-overlay {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.06) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
        }

        /* Floating stat card – top right */
        .stat-card {
            position: absolute;
            top: -16px;
            right: -10px;
            background: #fff;
            border-radius: 16px;
            padding: 14px 18px;
            box-shadow: 0 8px 32px rgba(80, 20, 60, 0.14);
            min-width: 190px;
            animation: slideInRight 0.6s 0.35s cubic-bezier(.22, .68, 0, 1.1) both;
            z-index: 10;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .stat-label {
            font-family: 'Sora', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: #1a1a2e;
        }

        .dot-green {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
            }

            50% {
                box-shadow: 0 0 0 6px rgba(34, 197, 94, 0.10);
            }
        }

        .stat-bar-track {
            height: 6px;
            background: #f0e8f4;
            border-radius: 99px;
            overflow: hidden;
            margin-bottom: 6px;
        }

        .stat-bar-fill {
            height: 100%;
            width: 72%;
            background: linear-gradient(90deg, var(--pink), #ff6eb4);
            border-radius: 99px;
            animation: growBar 1s 0.7s both;
        }

        @keyframes growBar {
            from {
                width: 0;
            }

            to {
                width: 72%;
            }
        }

        .stat-caption {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.10em;
            color: #a89bb5;
        }

        /* Floating encryption card – bottom */
        .encrypt-card {
            position: absolute;
            bottom: -16px;
            left: -10px;
            right: 0;
            background: rgba(255, 255, 255, 0.97);
            border-radius: 18px;
            padding: 16px 20px;
            box-shadow: 0 8px 32px rgba(80, 20, 60, 0.14);
            display: flex;
            align-items: flex-start;
            gap: 14px;
            animation: slideInLeft 0.6s 0.45s cubic-bezier(.22, .68, 0, 1.1) both;
            z-index: 10;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-16px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .enc-icon {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            background: var(--pink-pale);
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .enc-title {
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 5px;
        }

        .enc-desc {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.55;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 720px) {
            .card {
                grid-template-columns: 1fr;
            }

            .right {
                display: none;
            }

            .left {
                padding: 36px 28px 28px;
            }
        }
    </style>
</head>

<body>

    <div class="card">

        <!-- LEFT: Login Form -->
        <div class="left">

            <!-- Logo -->
            <div class="logo">
                <svg class="logo-icon" viewBox="0 0 44 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Cart body -->
                    <rect x="6" y="8" width="28" height="18" rx="4" fill="#e0176a" opacity="0.15" />
                    <path d="M4 6h4l4 16h20l4-14H10" stroke="#e0176a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                    <!-- Plus cross -->
                    <rect x="18" y="10" width="8" height="2.5" rx="1.25" fill="#e0176a" />
                    <rect x="20.75" y="7.5" width="2.5" height="8" rx="1.25" fill="#e0176a" />
                    <!-- Wheels -->
                    <circle cx="16" cy="34" r="3" fill="#e0176a" />
                    <circle cx="28" cy="34" r="3" fill="#e0176a" />
                    <circle cx="16" cy="34" r="1.3" fill="#fff" />
                    <circle cx="28" cy="34" r="1.3" fill="#fff" />
                </svg>
                <div class="logo-text">
                    <span>MEDICTO</span>
                    <span>PHARMACY</span>
                </div>
            </div>

            <h1 class="heading">Admin Portal</h1>
            <p class="subheading">Welcome back. Please enter your credentials to manage the clinical workspace.</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="form-group">
                    <div class="form-label">Email or Mobile Number</div>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="4" />
                                <path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10c1.657 0 3-.134 4-.5" />
                                <path d="M16 12c0-2.209 1.343-4 3-4s3 1.791 3 4-1.343 4-3 4" />
                            </svg>
                        </span>
                        <input type="email" name="email" placeholder="admin@medconcierge.com" autocomplete="username" />
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <div class="form-label">
                        Password
                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                    </div>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </span>
                        <input type="password" name="password" id="pwdInput" placeholder="••••••••" autocomplete="current-password" />
                        <button class="eye-btn" onclick="togglePwd()" type="button" aria-label="Toggle password">
                            <svg id="eyeIcon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Keep me signed in -->
                <div class="checkbox-row">
                    <input type="checkbox" name="remember" id="keepme" />
                    <label for="keepme">Keep me signed in</label>
                </div>

                <!-- Secure Login btn -->
                <button class="btn-primary" type="submit">
                    Secure Login
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                    </svg>
                </button>

            </form>

            <div class="or-divider">OR</div>

            <!-- OTP btn -->
            <button class="btn-otp" type="button">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="5" y="2" width="14" height="20" rx="2" ry="2" />
                    <line x1="12" y1="18" x2="12.01" y2="18" />
                </svg>
                OTP Verification
            </button>

            <!-- Footer -->
            <div class="left-footer">
                <div>© 2026 Medicto Admin Infrastructure</div>
                <div class="footer-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Security Audit</a>
                    <a href="#">Support</a>
                </div>
            </div>

        </div>

        <!-- RIGHT: Visual Panel -->
        <div class="right">

            <div class="photo-card">

                <!-- Photo area (CSS illustrated) -->
                <div class="photo-placeholder">
                    <div class="grid-overlay"></div>

                    <!-- Abstract medical visual background shapes -->
                    <svg style="position:absolute;inset:0;width:100%;height:100%" viewBox="0 0 420 340" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="bg1" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#6baed6" />
                                <stop offset="50%" stop-color="#4a90b8" />
                                <stop offset="100%" stop-color="#7b5da8" />
                            </linearGradient>
                            <linearGradient id="skin" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#f0c890" />
                                <stop offset="100%" stop-color="#d4a870" />
                            </linearGradient>
                            <linearGradient id="scrub" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#88ccdd" />
                                <stop offset="100%" stop-color="#5aabb8" />
                            </linearGradient>
                        </defs>
                        <rect width="420" height="340" fill="url(#bg1)" />

                        <!-- Lab / background blobs -->
                        <ellipse cx="340" cy="80" rx="100" ry="90" fill="rgba(255,255,255,0.06)" />
                        <ellipse cx="60" cy="260" rx="80" ry="70" fill="rgba(255,255,255,0.05)" />
                        <ellipse cx="200" cy="350" rx="200" ry="60" fill="rgba(0,0,0,0.12)" />

                        <!-- White coat / body -->
                        <path d="M130 340 Q140 220 180 200 Q210 192 240 200 Q280 220 290 340Z" fill="rgba(255,255,255,0.92)" />
                        <!-- Scrubs / top -->
                        <path d="M148 280 Q160 235 180 220 Q210 208 240 220 Q260 235 272 280Z" fill="url(#scrub)" />
                        <!-- Stethoscope -->
                        <path d="M190 230 Q185 260 196 272 Q208 284 220 272 Q231 260 226 230" stroke="#3d7a90" stroke-width="3.5" fill="none" stroke-linecap="round" />
                        <circle cx="208" cy="276" r="7" fill="#3d7a90" opacity="0.8" />

                        <!-- Neck -->
                        <rect x="200" y="175" width="20" height="30" rx="10" fill="url(#skin)" />
                        <!-- Head -->
                        <ellipse cx="210" cy="155" rx="36" ry="42" fill="url(#skin)" />
                        <!-- Hair -->
                        <path d="M174 148 Q178 108 210 106 Q242 108 246 148 Q240 130 210 128 Q180 130 174 148Z" fill="#2a1a0a" />
                        <!-- Hair bun -->
                        <ellipse cx="210" cy="108" rx="22" ry="14" fill="#2a1a0a" />
                        <!-- Eyes -->
                        <ellipse cx="198" cy="152" rx="5" ry="6" fill="#3d2a18" />
                        <ellipse cx="222" cy="152" rx="5" ry="6" fill="#3d2a18" />
                        <circle cx="199.5" cy="150.5" r="2" fill="#fff" opacity="0.5" />
                        <circle cx="223.5" cy="150.5" r="2" fill="#fff" opacity="0.5" />
                        <!-- Nose -->
                        <path d="M208 160 Q210 165 212 160" stroke="#c09070" stroke-width="1.5" fill="none" stroke-linecap="round" />
                        <!-- Lips -->
                        <path d="M203 170 Q210 175 217 170" stroke="#c07878" stroke-width="2" fill="none" stroke-linecap="round" />

                        <!-- Medical UI lines (floating charts) -->
                        <rect x="290" y="120" width="80" height="50" rx="8" fill="rgba(255,255,255,0.18)" />
                        <rect x="298" y="130" width="30" height="4" rx="2" fill="rgba(255,255,255,0.7)" />
                        <rect x="298" y="138" width="20" height="4" rx="2" fill="rgba(255,255,255,0.4)" />
                        <path d="M298 155 l8-6 l8 8 l8-10 l8 6" stroke="rgba(255,255,255,0.8)" stroke-width="1.5" fill="none" />

                        <!-- Bar chart corner -->
                        <rect x="295" y="165" width="4" height="12" rx="2" fill="rgba(255,255,255,0.6)" />
                        <rect x="303" y="160" width="4" height="17" rx="2" fill="rgba(255,255,255,0.7)" />
                        <rect x="311" y="157" width="4" height="20" rx="2" fill="rgba(255,255,255,0.5)" />
                    </svg>

                    <!-- Floating stat card (top right, inside) -->
                    <div class="stat-card">
                        <div class="stat-top">
                            <span class="stat-label">Active Nodes</span>
                            <span class="dot-green"></span>
                        </div>
                        <div class="stat-bar-track">
                            <div class="stat-bar-fill"></div>
                        </div>
                        <div class="stat-caption">CLINICAL CAPACITY: OPTIMAL</div>
                    </div>

                    <!-- Encryption card (bottom) -->
                    <div class="encrypt-card">
                        <div class="enc-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L4 6v6c0 5.25 3.5 10.15 8 11.5C16.5 22.15 20 17.25 20 12V6l-8-4z" fill="#e0176a" opacity="0.2" />
                                <path d="M12 2L4 6v6c0 5.25 3.5 10.15 8 11.5C16.5 22.15 20 17.25 20 12V6l-8-4z" stroke="#e0176a" stroke-width="1.8" stroke-linejoin="round" />
                                <path d="M9 12l2 2 4-4" stroke="#e0176a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div>
                            <div class="enc-title">End-to-End Encryption</div>
                            <div class="enc-desc">Your administrative session is protected by military-grade encryption and real-time threat monitoring to ensure patient data remains confidential.</div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

    <script>
        function togglePwd() {
            const input = document.getElementById('pwdInput');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `
        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
        <line x1="1" y1="1" x2="23" y2="23"/>
      `;
            } else {
                input.type = 'password';
                icon.innerHTML = `
        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
        <circle cx="12" cy="12" r="3"/>
      `;
            }
        }
    </script>

</body>

</html>