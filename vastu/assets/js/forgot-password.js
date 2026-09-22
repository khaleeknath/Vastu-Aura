(function () {
  const API_BASE = 'assets/api'; // adjust if your api/ folder lives elsewhere

  const state = {
    email: '',
    resetToken: '',
  };

  const alertBox = document.getElementById('forgotAlert');
  const steps = {
    1: document.getElementById('step1'),
    2: document.getElementById('step2'),
    3: document.getElementById('step3'),
    4: document.getElementById('step4'),
  };
  const dots = document.querySelectorAll('.step-dot');

  function showAlert(message, type) {
    alertBox.textContent = message;
    alertBox.className = 'alert mt-4 alert-' + (type === 'error' ? 'danger' : 'success');
    alertBox.classList.remove('d-none');
  }

  function hideAlert() {
    alertBox.classList.add('d-none');
  }

  function goToStep(stepNumber) {
    Object.values(steps).forEach((el) => el.classList.add('d-none'));
    steps[stepNumber].classList.remove('d-none');
    hideAlert();

    dots.forEach((dot) => {
      const n = Number(dot.dataset.step);
      dot.classList.toggle('active', n === stepNumber);
      dot.classList.toggle('done', n < stepNumber);
    });

    if (stepNumber === 4) {
      document.querySelector('.step-tracker').classList.add('d-none');
    }
  }

  function setLoading(button, isLoading) {
    const label = button.querySelector('.btn-label');
    const spinner = button.querySelector('.btn-spinner');
    button.disabled = isLoading;
    if (label) label.classList.toggle('d-none', isLoading);
    if (spinner) spinner.classList.toggle('d-none', !isLoading);
  }

  async function postJson(url, payload) {
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    let data;
    try {
      data = await res.json();
    } catch (e) {
      data = { success: false, message: 'Unexpected server response.' };
    }
    return { ok: res.ok, data };
  }

  // ---------- Step 1: send code ----------
  const emailForm = document.getElementById('emailForm');
  const sendCodeBtn = document.getElementById('sendCodeBtn');

  emailForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    hideAlert();

    const email = document.getElementById('forgotEmail').value.trim();
    if (!email) return;

    setLoading(sendCodeBtn, true);
    try {
      const { data } = await postJson(`${API_BASE}/forgot-password-request.php`, { email });
      if (data.success) {
        state.email = email;
        document.getElementById('otpEmailLabel').textContent = email;
        clearOtpBoxes();
        startResendCountdown(45);
        goToStep(2);
        focusFirstOtpBox();
      } else {
        showAlert(data.message || 'Something went wrong. Please try again.', 'error');
      }
    } catch (err) {
      showAlert('Network error. Please check your connection and try again.', 'error');
    } finally {
      setLoading(sendCodeBtn, false);
    }
  });

  // ---------- Step 2: OTP input handling ----------
  const otpBoxes = Array.from(document.querySelectorAll('.otp-box'));
  const otpForm = document.getElementById('otpForm');
  const verifyOtpBtn = document.getElementById('verifyOtpBtn');
  const resendBtn = document.getElementById('resendBtn');
  const resendTimerText = document.getElementById('resendTimerText');
  const resendCountdownEl = document.getElementById('resendCountdown');
  let resendTimer = null;

  function clearOtpBoxes() {
    otpBoxes.forEach((box) => {
      box.value = '';
      box.classList.remove('error');
    });
  }

  function focusFirstOtpBox() {
    if (otpBoxes[0]) otpBoxes[0].focus();
  }

  function getOtpValue() {
    return otpBoxes.map((box) => box.value).join('');
  }

  otpBoxes.forEach((box, index) => {
    box.addEventListener('input', () => {
      box.value = box.value.replace(/\D/g, '').slice(0, 1);
      box.classList.remove('error');
      if (box.value && index < otpBoxes.length - 1) {
        otpBoxes[index + 1].focus();
      }
    });

    box.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && !box.value && index > 0) {
        otpBoxes[index - 1].focus();
      }
    });

    box.addEventListener('paste', (e) => {
      e.preventDefault();
      const pasted = (e.clipboardData.getData('text') || '').replace(/\D/g, '').slice(0, 6);
      pasted.split('').forEach((digit, i) => {
        if (otpBoxes[i]) otpBoxes[i].value = digit;
      });
      const nextEmpty = otpBoxes.findIndex((b) => !b.value);
      (otpBoxes[nextEmpty === -1 ? otpBoxes.length - 1 : nextEmpty]).focus();
    });
  });

  function startResendCountdown(seconds) {
    clearInterval(resendTimer);
    let remaining = seconds;
    resendTimerText.classList.remove('d-none');
    resendBtn.classList.add('d-none');
    resendCountdownEl.textContent = remaining + 's';

    resendTimer = setInterval(() => {
      remaining -= 1;
      if (remaining <= 0) {
        clearInterval(resendTimer);
        resendTimerText.classList.add('d-none');
        resendBtn.classList.remove('d-none');
        return;
      }
      resendCountdownEl.textContent = remaining + 's';
    }, 1000);
  }

  resendBtn.addEventListener('click', async () => {
    hideAlert();
    resendBtn.disabled = true;
    try {
      const { data } = await postJson(`${API_BASE}/forgot-password-request.php`, { email: state.email });
      showAlert(data.message || 'A new code has been sent.', data.success ? 'success' : 'error');
      if (data.success) {
        clearOtpBoxes();
        focusFirstOtpBox();
        startResendCountdown(45);
      }
    } catch (err) {
      showAlert('Network error. Please try again.', 'error');
    } finally {
      resendBtn.disabled = false;
    }
  });

  otpForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    hideAlert();

    const otp = getOtpValue();
    if (otp.length !== 6) {
      showAlert('Please enter the full 6-digit code.', 'error');
      return;
    }

    setLoading(verifyOtpBtn, true);
    try {
      const { data } = await postJson(`${API_BASE}/forgot-password-verify-otp.php`, {
        email: state.email,
        otp,
      });

      if (data.success) {
        state.resetToken = data.reset_token;
        clearInterval(resendTimer);
        goToStep(3);
      } else {
        otpBoxes.forEach((box) => box.classList.add('error'));
        showAlert(data.message || 'Incorrect code. Please try again.', 'error');
      }
    } catch (err) {
      showAlert('Network error. Please try again.', 'error');
    } finally {
      setLoading(verifyOtpBtn, false);
    }
  });

  document.querySelectorAll('.back-step-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
      goToStep(Number(btn.dataset.target));
    });
  });

  // ---------- Step 3: set new password ----------
  const resetForm = document.getElementById('resetForm');
  const resetPasswordBtn = document.getElementById('resetPasswordBtn');

  resetForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    hideAlert();

    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (newPassword.length < 8) {
      showAlert('Password must be at least 8 characters.', 'error');
      return;
    }
    if (newPassword !== confirmPassword) {
      showAlert('Passwords do not match.', 'error');
      return;
    }

    setLoading(resetPasswordBtn, true);
    try {
      const { data } = await postJson(`${API_BASE}/forgot-password-reset.php`, {
        email: state.email,
        reset_token: state.resetToken,
        new_password: newPassword,
        confirm_password: confirmPassword,
      });

      if (data.success) {
        goToStep(4);
      } else {
        showAlert(data.message || 'Something went wrong. Please try again.', 'error');
      }
    } catch (err) {
      showAlert('Network error. Please try again.', 'error');
    } finally {
      setLoading(resetPasswordBtn, false);
    }
  });

  // ---------- Password visibility toggle ----------
  document.querySelectorAll('.password-toggle').forEach((btn) => {
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.target);
      const eyeIcon = btn.querySelector('.icon-eye');
      const eyeOffIcon = btn.querySelector('.icon-eye-off');
      const isHidden = input.type === 'password';

      input.type = isHidden ? 'text' : 'password';
      eyeIcon.classList.toggle('d-none', isHidden);
      eyeOffIcon.classList.toggle('d-none', !isHidden);
      btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
    });
  });

  // ---------- Back to top ----------
  const backToTopBtn = document.getElementById('backToTop');
  window.addEventListener('scroll', () => {
    backToTopBtn.classList.toggle('visible', window.scrollY > 200);
  });
  backToTopBtn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
})();