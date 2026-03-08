document.addEventListener('DOMContentLoaded', function () {

    // 1. WhatsApp Click Tracking
    // Checks for specific class or any generic whatsapp link or data-wa attributes
    const waButtons = document.querySelectorAll('.vp-cotizar-btn, .js-whatsapp-link, a[href*="wa.me"]');

    waButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            // We do NOT prevent default, logic handling navigation is elsewhere or native.

            // Get URL from href OR data-wa
            let rawUrl = this.href;
            if (this.hasAttribute('data-wa')) {
                rawUrl = this.getAttribute('data-wa');
            }

            // If it's just "#", ignore unless we have other data
            if (rawUrl === '#' || !rawUrl) return;

            // Extract 'text' param
            let fullText = '';
            try {
                // Handle cases like "https://wa.me/123?text=..." or just "?text=..."
                if (rawUrl.includes('?')) {
                    const urlParts = rawUrl.split('?');
                    const params = new URLSearchParams(urlParts[1]);
                    fullText = params.get('text') || '';
                }
            } catch (err) { console.log('Error parsing WA URL', err); }

            // Try to parse model from text if possible, or use data attributes
            let model = this.getAttribute('data-modelo') || '';
            if (!model && fullText) {
                // Simple heuristic: "quisiera cotizar el [MODELO]"
                const match = fullText.match(/cotizar el (.*?)(\.| \()/);
                if (match && match[1]) model = match[1];
            }

            const data = new FormData();
            data.append('action', 'mm_submit_contact_form');
            data.append('type', 'whatsapp');
            data.append('name', 'Usuario Web'); // Can't know name before chat
            data.append('model', model);
            data.append('message', fullText);

            // Send Beacon (more reliable for clicks navigating away)
            if (navigator.sendBeacon) {
                // Must act as form data
                navigator.sendBeacon(mm_ajax.ajaxurl, data);
            } else {
                fetch(mm_ajax.ajaxurl, {
                    method: 'POST',
                    body: data
                });
            }
        });
    });

    // 2. Email Form Handling (Generic handler if form exists)
    const contactForm = document.getElementById('mm-contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = 'Enviando...';
            submitBtn.disabled = true;

            const formData = new FormData(contactForm);
            formData.append('action', 'mm_submit_contact_form');
            formData.append('type', 'email');

            fetch(mm_ajax.ajaxurl, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(res => {
                    if (res.success) {
                        alert('Gracias! Tu mensaje ha sido enviado.');
                        contactForm.reset();
                    } else {
                        alert('Error: ' + res.data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Hubo un error de conexión.');
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        });
    }
});
