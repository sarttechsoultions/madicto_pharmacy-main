@extends('admin.layouts.layout')

@section('content')
<div class="st-content" style="padding: 24px; max-width: 1200px; margin: 0 auto;">

    <!-- TOP HEADER -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                <a href="{{ route('admin.payments.index') }}" style="color: #64748b; text-decoration: none; font-size: 13px;">
                    <i class="fa-solid fa-arrow-left"></i> Back to Payments
                </a>
            </div>
            <h1 style="font-size: 26px; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-sliders" style="color: #0284c7;"></i>
                Razorpay Payment Gateway Settings
            </h1>
            <p style="font-size: 14px; color: #64748b; margin-top: 4px;">
                Configure Razorpay API credentials for mobile app checkout and test live connectivity.
            </p>
        </div>
        <div>
            <button id="btnSettingsTestConn" onclick="runTestConnection()" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-bolt" style="color: #eab308;"></i> Test Connection Live
            </button>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
    @endif

    <!-- CONNECTION TEST ALERT BANNER (HIDDEN INITIALLY) -->
    <div id="connAlert" style="display: none; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; align-items: center; justify-content: space-between;">
        <span id="connAlertText"></span>
        <button onclick="document.getElementById('connAlert').style.display='none'" style="background: none; border: none; font-size: 16px; cursor: pointer; color: inherit;">&times;</button>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">

        <!-- LEFT: CREDENTIALS FORM -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h2 style="font-size: 18px; font-weight: 700; color: #0f172a; margin-top: 0; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-key" style="color: #0284c7;"></i> API Credentials
            </h2>
            <p style="font-size: 13px; color: #64748b; margin-bottom: 20px;">
                Enter the Razorpay Key ID and Secret Key from your Razorpay Dashboard.
            </p>

            <form method="POST" action="{{ route('admin.payments.update_settings') }}">
                @csrf

                <!-- GATEWAY STATUS -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-weight: 600; font-size: 14px; color: #1e293b;">Enable Razorpay Gateway</div>
                        <div style="font-size: 12px; color: #64748b;">Allow customers to pay via Razorpay in the Mobile App</div>
                    </div>
                    <label style="position: relative; display: inline-block; width: 44px; height: 24px;">
                        <input type="checkbox" name="is_active" value="1" {{ $setting->is_active ? 'checked' : '' }} style="opacity: 0; width: 0; height: 0;" id="toggleActive">
                        <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ $setting->is_active ? '#0284c7' : '#cbd5e1' }}; transition: .4s; border-radius: 24px;" id="toggleSlider"></span>
                    </label>
                </div>

                <!-- ENVIRONMENT MODE -->
                <div style="margin-bottom: 18px;">
                    <label style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">Environment Mode</label>
                    <div style="display: flex; gap: 16px;">
                        <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; cursor: pointer;">
                            <input type="radio" name="mode" value="test" {{ $setting->mode === 'test' ? 'checked' : '' }}>
                            <span style="background: #fffbeb; color: #d97706; padding: 2px 8px; border-radius: 4px; font-weight: 600; font-size: 12px;">Test Mode</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; cursor: pointer;">
                            <input type="radio" name="mode" value="live" {{ $setting->mode === 'live' ? 'checked' : '' }}>
                            <span style="background: #ecfdf5; color: #059669; padding: 2px 8px; border-radius: 4px; font-weight: 600; font-size: 12px;">Live Production</span>
                        </label>
                    </div>
                </div>

                <!-- KEY ID -->
                <div style="margin-bottom: 18px;">
                    <label style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">
                        Razorpay Key ID <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="key_id" value="{{ old('key_id', $setting->key_id) }}" required placeholder="rzp_test_..." style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-family: monospace;">
                    <span style="font-size: 11px; color: #64748b;">Public Key used by the mobile app Razorpay SDK.</span>
                </div>

                <!-- KEY SECRET -->
                <div style="margin-bottom: 18px;">
                    <label style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">
                        Razorpay Key Secret <span style="color: #ef4444;">*</span>
                    </label>
                    <div style="position: relative;">
                        <input type="password" id="inputSecret" name="key_secret" value="{{ old('key_secret', $setting->key_secret) }}" required placeholder="Enter Secret Key" style="width: 100%; padding: 10px 40px 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-family: monospace;">
                        <button type="button" onclick="toggleSecretVisibility()" style="position: absolute; right: 10px; top: 10px; background: none; border: none; cursor: pointer; color: #64748b;">
                            <i class="fa-solid fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    <span style="font-size: 11px; color: #64748b;">Private Key kept safe on the backend for signature verification.</span>
                </div>

                <!-- CURRENCY -->
                <div style="margin-bottom: 18px;">
                    <label style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">Currency</label>
                    <select name="currency" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; background: #ffffff;">
                        <option value="INR" {{ $setting->currency === 'INR' ? 'selected' : '' }}>INR (Indian Rupee - ₹)</option>
                        <option value="USD" {{ $setting->currency === 'USD' ? 'selected' : '' }}>USD (US Dollar - $)</option>
                    </select>
                </div>

                <!-- DISPLAY NAME & DESCRIPTION -->
                <div style="margin-bottom: 18px;">
                    <label style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">Gateway Display Name</label>
                    <input type="text" name="display_name" value="{{ old('display_name', $setting->display_name) }}" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">Customer Description</label>
                    <input type="text" name="description" value="{{ old('description', $setting->description) }}" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
                </div>

                <button type="submit" style="background: #0284c7; color: #ffffff; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 2px 4px rgba(2, 132, 199, 0.2);">
                    <i class="fa-solid fa-floppy-disk"></i> Save Gateway Configuration
                </button>
            </form>
        </div>

        <!-- RIGHT: DEVELOPER API REFERENCE -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h2 style="font-size: 18px; font-weight: 700; color: #0f172a; margin-top: 0; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-code" style="color: #0284c7;"></i> Mobile App Integration Guide
            </h2>
            <p style="font-size: 13px; color: #64748b; margin-bottom: 18px;">
                Complete REST API endpoints for Flutter, Android, iOS and React Native apps.
            </p>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; margin-bottom: 16px;">
                <div style="font-weight: 700; font-size: 12px; color: #475569; text-transform: uppercase; margin-bottom: 8px;">1. Client Config API (Public)</div>
                <div style="display: flex; align-items: center; gap: 8px; font-family: monospace; font-size: 12px; background: #0f172a; color: #38bdf8; padding: 8px 12px; border-radius: 6px;">
                    <span style="color: #a3e635; font-weight: 700;">GET</span> /api/razorpay/config
                </div>
                <div style="font-size: 11px; color: #64748b; margin-top: 4px;">Fetches the active Key ID, currency, and enabled status before opening the checkout sheet.</div>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; margin-bottom: 16px;">
                <div style="font-weight: 700; font-size: 12px; color: #475569; text-transform: uppercase; margin-bottom: 8px;">2. Create Order API (Auth: Sanctum)</div>
                <div style="display: flex; align-items: center; gap: 8px; font-family: monospace; font-size: 12px; background: #0f172a; color: #38bdf8; padding: 8px 12px; border-radius: 6px;">
                    <span style="color: #fbbf24; font-weight: 700;">POST</span> /api/razorpay/create-order
                </div>
                <div style="font-size: 11px; color: #64748b; margin-top: 6px;"><strong>Payload:</strong></div>
                <pre style="background: #0f172a; color: #e2e8f0; padding: 8px 12px; border-radius: 6px; font-size: 11px; overflow-x: auto; margin-top: 4px;">{
  "order_id": "ORD-00007", // or amount: 450.00
  "payment_type": "order"
}</pre>
                <div style="font-size: 11px; color: #64748b; margin-top: 4px;">Returns <code>razorpay_order_id</code> (order_XXXX) and amount in paise to pass into Razorpay Checkout SDK.</div>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; margin-bottom: 16px;">
                <div style="font-weight: 700; font-size: 12px; color: #475569; text-transform: uppercase; margin-bottom: 8px;">3. Verify Payment Signature (Auth: Sanctum)</div>
                <div style="display: flex; align-items: center; gap: 8px; font-family: monospace; font-size: 12px; background: #0f172a; color: #38bdf8; padding: 8px 12px; border-radius: 6px;">
                    <span style="color: #fbbf24; font-weight: 700;">POST</span> /api/razorpay/verify-payment
                </div>
                <div style="font-size: 11px; color: #64748b; margin-top: 6px;"><strong>Payload from Mobile SDK:</strong></div>
                <pre style="background: #0f172a; color: #e2e8f0; padding: 8px 12px; border-radius: 6px; font-size: 11px; overflow-x: auto; margin-top: 4px;">{
  "razorpay_order_id": "order_XXXX",
  "razorpay_payment_id": "pay_XXXX",
  "razorpay_signature": "XXXXX",
  "order_id": "ORD-00007"
}</pre>
                <div style="font-size: 11px; color: #64748b; margin-top: 4px;">Validates HMAC-SHA256 signature, updates order to <strong>Paid</strong>, and triggers rich push notifications.</div>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px;">
                <div style="font-weight: 700; font-size: 12px; color: #475569; text-transform: uppercase; margin-bottom: 8px;">4. Payment Failure Logging</div>
                <div style="display: flex; align-items: center; gap: 8px; font-family: monospace; font-size: 12px; background: #0f172a; color: #38bdf8; padding: 8px 12px; border-radius: 6px;">
                    <span style="color: #fbbf24; font-weight: 700;">POST</span> /api/razorpay/payment-failed
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function toggleSecretVisibility() {
    const input = document.getElementById('inputSecret');
    const icon = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

document.getElementById('toggleActive').addEventListener('change', function() {
    document.getElementById('toggleSlider').style.backgroundColor = this.checked ? '#0284c7' : '#cbd5e1';
});

function runTestConnection() {
    const btn = document.getElementById('btnSettingsTestConn');
    const alertBox = document.getElementById('connAlert');
    const alertText = document.getElementById('connAlertText');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Testing Connection...';

    fetch('{{ route("admin.payments.test_connection") }}')
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-bolt" style="color: #eab308;"></i> Test Connection Live';
            alertBox.style.display = 'flex';
            if (data.success) {
                alertBox.style.background = '#ecfdf5';
                alertBox.style.color = '#065f46';
                alertBox.style.border = '1px solid #a7f3d0';
                alertText.innerHTML = '<i class="fa-solid fa-circle-check"></i> ' + data.message + ' (Active Key: ' + data.key_id + ')';
            } else {
                alertBox.style.background = '#fef2f2';
                alertBox.style.color = '#991b1b';
                alertBox.style.border = '1px solid #fecaca';
                alertText.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> ' + data.message;
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-bolt" style="color: #eab308;"></i> Test Connection Live';
            alertBox.style.display = 'flex';
            alertBox.style.background = '#fef2f2';
            alertBox.style.color = '#991b1b';
            alertBox.style.border = '1px solid #fecaca';
            alertText.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Connection error: ' + err.message;
        });
}
</script>
@endsection
