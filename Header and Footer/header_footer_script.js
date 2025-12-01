document.addEventListener('DOMContentLoaded', () => { // Ensure the DOM is fully loaded
    
    const toggleBtn = document.getElementById('menu-toggle-btn');
    const dropdown = document.getElementById('dropdown-nav');
    const menuIconImg = document.getElementById('menu-icon-img');

    // Paths to the menu and close icons
    const menuIconPath = "header_footer_images/icon-menu.png";
    const closeIconPath = "header_footer_images/icon-close.png";

    // Toggle dropdown menu visibility and icon
    toggleBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('open');

        // Change icon based on dropdown state
        if (dropdown.classList.contains('open')) {
            menuIconImg.src = closeIconPath;
        } else {
            menuIconImg.src = menuIconPath;
        }
    });

    // Close dropdown if clicking outside of it

    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target) && dropdown.classList.contains('open')) {
            dropdown.classList.remove('open');
            menuIconImg.src = menuIconPath;
        }
    });
});