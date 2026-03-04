// Dark Mode Toggle
document.addEventListener('DOMContentLoaded', function() {
    const moonIcon = document.getElementById('moon-icon');
    const html = document.documentElement;
    
    // Load dark mode preference from localStorage
    if (localStorage.getItem('darkMode') === 'enabled') {
        html.classList.add('dark-mode');
        if (moonIcon) {
            moonIcon.src = moonIcon.getAttribute('data-dark-src');
        }
    }
    
    // Toggle dark mode on moon icon click
    if (moonIcon) {
        moonIcon.addEventListener('click', function(e) {
            e.preventDefault();
            html.classList.toggle('dark-mode');
            
            // Update moon icon based on dark mode state
            if (html.classList.contains('dark-mode')) {
                moonIcon.src = moonIcon.getAttribute('data-dark-src');
                localStorage.setItem('darkMode', 'enabled');
            } else {
                moonIcon.src = moonIcon.getAttribute('data-light-src');
                localStorage.setItem('darkMode', 'disabled');
            }
        });
    }
});
