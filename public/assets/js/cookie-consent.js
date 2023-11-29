// assets/js/cookie-consent.js

import Cookies from 'js-cookie';

const cookieConsentKey = 'cookie_consent';

function showCookieConsent() {
    const consentBanner = document.getElementById('cookie-consent-banner');
    const acceptButton = document.getElementById('accept-cookies');

    if (Cookies.get(cookieConsentKey) !== 'accepted') {
        consentBanner.style.display = 'block';

        acceptButton.addEventListener('click', () => {
            Cookies.set(cookieConsentKey, 'accepted', { expires: 365 });
            consentBanner.style.display = 'none';
        });
    }
}

showCookieConsent();
