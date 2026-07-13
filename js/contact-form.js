document.addEventListener('DOMContentLoaded', function () {
    // 1. WhatsApp Click Tracking removed for security (no longer creating phantom leads)

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
            
            // Add Nonce for security
            if (typeof mm_ajax !== 'undefined' && mm_ajax.nonce) {
                formData.append('nonce', mm_ajax.nonce);
            }

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
