<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password | Vastu Shakti Rahasya</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/forgot-password.css">
</head>
<body>
  <button id="backToTop" class="back-to-top" aria-label="Back to top">↑</button>

  <main class="auth-shell">
    <div class="auth-card single-card">
      <a class="logo-mark" href="index.php">Vastu Shakti Rahasya</a>
      <span class="eyebrow">Password Recovery</span>

      <div class="step-tracker" id="stepTracker">
        <span class="step-dot active" data-step="1"></span>
        <span class="step-line"></span>
        <span class="step-dot" data-step="2"></span>
        <span class="step-line"></span>
        <span class="step-dot" data-step="3"></span>
      </div>

      <!-- STEP 1: Email -->
      <section class="auth-step" id="step1" data-step="1">
        <h1>Recover your account access.</h1>
        <p>Enter the email address on your account and we'll send you a 6-digit code.</p>
        <form id="emailForm" class="row g-3 mt-2" novalidate>
          <div class="col-12">
            <label class="form-label" for="forgotEmail">Email Address</label>
            <input id="forgotEmail" name="email" type="email" class="form-control" placeholder="you@example.com" required autocomplete="email">
          </div>
          <div class="col-12">
            <button class="btn btn-brand w-100" type="submit" id="sendCodeBtn">
              <span class="btn-label">Send Code</span>
              <span class="btn-spinner d-none"></span>
            </button>
          </div>
        </form>
      </section>

      <!-- STEP 2: OTP -->
      <section class="auth-step d-none" id="step2" data-step="2">
        <h1>Enter your code.</h1>
        <p>We sent a 6-digit code to <strong id="otpEmailLabel">your email</strong></p>
        <form id="otpForm" class="mt-2" novalidate>
          <div class="otp-inputs" id="otpInputs">
            <input type="text" inputmode="numeric" maxlength="1" class="otp-box" data-index="0" autocomplete="one-time-code">
            <input type="text" inputmode="numeric" maxlength="1" class="otp-box" data-index="1">
            <input type="text" inputmode="numeric" maxlength="1" class="otp-box" data-index="2">
            <input type="text" inputmode="numeric" maxlength="1" class="otp-box" data-index="3">
            <input type="text" inputmode="numeric" maxlength="1" class="otp-box" data-index="4">
            <input type="text" inputmode="numeric" maxlength="1" class="otp-box" data-index="5">
          </div>
          <button class="btn btn-brand w-100 mt-3" type="submit" id="verifyOtpBtn">
            <span class="btn-label">Verify Code</span>
            <span class="btn-spinner d-none"></span>
          </button>
          <div class="resend-row">
            <span id="resendTimerText">Resend code in <strong id="resendCountdown">45s</strong></span>
            <button type="button" id="resendBtn" class="link-btn d-none">Resend code</button>
          </div>
          <button type="button" class="back-step-btn" data-target="1">← Use a different email</button>
        </form>
      </section>

      <!-- STEP 3: New password -->
      <section class="auth-step d-none" id="step3" data-step="3">
        <h1>Set a new password.</h1>
        <p>Choose a strong password you haven't used before.</p>
        <form id="resetForm" class="row g-3 mt-2" novalidate>
          <div class="col-12">
            <label class="form-label" for="newPassword">New Password</label>
            <div class="password-field">
              <input id="newPassword" name="new_password" type="password" class="form-control" minlength="8" required autocomplete="new-password">
              <button type="button" class="password-toggle" data-target="newPassword" aria-label="Show password">
                <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
                <svg class="icon-eye-off d-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.6 21.6 0 0 1 5.06-6.06M9.9 4.24A10.4 10.4 0 0 1 12 4c7 0 11 7 11 7a21.6 21.6 0 0 1-2.61 3.68M14.12 14.12a3 3 0 1 1-4.24-4.24"></path>
                  <line x1="1" y1="1" x2="23" y2="23"></line>
                </svg>
              </button>
            </div>
          </div>
          <div class="col-12">
            <label class="form-label" for="confirmPassword">Confirm New Password</label>
            <div class="password-field">
              <input id="confirmPassword" name="confirm_password" type="password" class="form-control" minlength="8" required autocomplete="new-password">
              <button type="button" class="password-toggle" data-target="confirmPassword" aria-label="Show password">
                <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
                <svg class="icon-eye-off d-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.6 21.6 0 0 1 5.06-6.06M9.9 4.24A10.4 10.4 0 0 1 12 4c7 0 11 7 11 7a21.6 21.6 0 0 1-2.61 3.68M14.12 14.12a3 3 0 1 1-4.24-4.24"></path>
                  <line x1="1" y1="1" x2="23" y2="23"></line>
                </svg>
              </button>
            </div>
          </div>
          <div class="col-12">
            <button class="btn btn-brand w-100" type="submit" id="resetPasswordBtn">
              <span class="btn-label">Update Password</span>
              <span class="btn-spinner d-none"></span>
            </button>
          </div>
        </form>
      </section>

      <!-- STEP 4: Success -->
      <section class="auth-step d-none text-center" id="step4" data-step="4">
        <div class="success-check">✓</div>
        <h1>Password updated.</h1>
        <p>Your password has been changed successfully. You can now log in with your new password.</p>
        <a href="login.php" class="btn btn-brand w-100 mt-2">Back to Login</a>
      </section>

      <div id="forgotAlert" class="alert d-none mt-4" role="alert"></div>

      <div class="auth-links">
        <a href="login.php">Back to Login</a>
        <a href="register.php">Create an Account</a>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>
  <script src="assets/js/forgot-password.js"></script>
</body>
</html>