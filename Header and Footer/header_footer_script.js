document.addEventListener('DOMContentLoaded', () => {
    
    const toggleBtn = document.getElementById('menu-toggle-btn');
    const dropdown = document.getElementById('dropdown-nav');
    const menuIconImg = document.getElementById('menu-icon-img');

    
    const menuIconPath = "images/icon-menu.png";
    const closeIconPath = "images/icon-close.png";

    toggleBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('open');

        
        if (dropdown.classList.contains('open')) {
            menuIconImg.src = closeIconPath;
        } else {
            menuIconImg.src = menuIconPath;
        }
    });

    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target) && dropdown.classList.contains('open')) {
            dropdown.classList.remove('open');
            menuIconImg.src = menuIconPath;
        }
    });
});