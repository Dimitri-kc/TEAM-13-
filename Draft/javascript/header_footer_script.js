document.addEventListener('DOMContentLoaded', () => {

    // left menu dropdown
    const toggleBtn = document.getElementById('menu-toggle-btn');
    const dropdown = document.getElementById('dropdown-nav');
    const menuIconImg = document.getElementById('menu-icon-img');

    const menuIconPath = "../images/header_footer_images/icon-menu.png";
    const closeIconPath = "../images/header_footer_images/icon-closed.png";

    // profile dropdown
    const profileToggleBtn = document.getElementById('profile-toggle-btn');
    const profileDropdown = document.getElementById('profile-dropdown');
    const profileWrapper = document.getElementById('profile-wrapper');

    // Check if elements exist before adding event listeners
    const hasLeftMenu = !!(toggleBtn && dropdown && menuIconImg);
    const hasProfile = !!(profileToggleBtn && profileDropdown && profileWrapper);

    // Toggle left menu dropdown
    if (hasLeftMenu) {
        toggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            dropdown.classList.toggle('open');

            // Close profile dropdown if open
            if (hasProfile && profileDropdown.classList.contains('open')) {
                profileDropdown.classList.remove('open');
                profileToggleBtn.setAttribute('aria-expanded', 'false');
            }

            // Swap icon
            if (dropdown.classList.contains('open')) {
                menuIconImg.src = closeIconPath;
            } else {
                menuIconImg.src = menuIconPath;
            }
        });
    }

    // Toggle profile dropdown
    if (hasProfile) {
        profileToggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            profileDropdown.classList.toggle('open');
            const isOpen = profileDropdown.classList.contains('open');
            profileToggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

            // Close left menu if open
            if (hasLeftMenu && dropdown.classList.contains('open')) {
                dropdown.classList.remove('open');
                menuIconImg.src = menuIconPath;
            }
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', (e) => {

        // Close left dropdown
        if (hasLeftMenu && dropdown.classList.contains('open')) {
            const clickedInsideLeft = dropdown.contains(e.target) || toggleBtn.contains(e.target);
            if (!clickedInsideLeft) {
                dropdown.classList.remove('open');
                menuIconImg.src = menuIconPath;
            }
        }

        // Close profile dropdown
        if (hasProfile && profileDropdown.classList.contains('open')) {
            const clickedInsideProfile = profileWrapper.contains(e.target);
            if (!clickedInsideProfile) {
                profileDropdown.classList.remove('open');
                profileToggleBtn.setAttribute('aria-expanded', 'false');
            }
        }
    });

    // Close on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {

            if (hasLeftMenu && dropdown.classList.contains('open')) {
                dropdown.classList.remove('open');
                menuIconImg.src = menuIconPath;
            }

            if (hasProfile && profileDropdown.classList.contains('open')) {
                profileDropdown.classList.remove('open');
                profileToggleBtn.setAttribute('aria-expanded', 'false');
            }
        }
    });
});