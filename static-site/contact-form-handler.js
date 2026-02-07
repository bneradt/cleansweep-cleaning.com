/**
 * Contact Form Handler
 * Intercepts Gravity Forms submission and sends to our PHP backend
 */

document.addEventListener('DOMContentLoaded', function() {
    // Find the Gravity Form
    const form = document.querySelector('.gform_wrapper form');

    if (!form) {
        console.log('Contact form not found');
        return;
    }

    // Prevent default Gravity Forms submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        // Get form values from Gravity Forms fields
        const nameField = document.querySelector('input[name="input_2"]');
        const emailField = document.querySelector('input[name="input_3"]');
        const phoneField = document.querySelector('input[name="input_4"]');
        const serviceField = document.querySelector('input[name="input_7"]:checked');
        const messageField = document.querySelector('textarea[name="input_5"]');

        if (!nameField || !emailField || !phoneField) {
            alert('Please fill out all required fields');
            return;
        }

        // Build form data
        const formData = new FormData();
        formData.append('name', nameField.value);
        formData.append('email', emailField.value);
        formData.append('phone', phoneField.value);
        formData.append('service', serviceField ? serviceField.value.toLowerCase() : 'other');
        formData.append('message', messageField ? messageField.value : '');

        // Show loading state
        const submitButton = form.querySelector('input[type="submit"]');
        const originalText = submitButton.value;
        submitButton.value = 'Sending...';
        submitButton.disabled = true;

        // Send to our PHP backend
        fetch('/api/send-email.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                form.reset();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('There was an error sending your message. Please call us directly at (217) 714-7408.');
            console.error('Form submission error:', error);
        })
        .finally(() => {
            submitButton.value = originalText;
            submitButton.disabled = false;
        });

        return false;
    }, true);
});
