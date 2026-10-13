document.addEventListener("DOMContentLoaded", () => {
    const menuToggle = document.getElementById("menuToggle");
    const closeSidebar = document.getElementById("closeSidebar");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");

    const openMenu = () => {
        sidebar.classList.add("active");
        overlay.classList.add("active");
    };

    const closeMenu = () => {
        sidebar.classList.remove("active");
        overlay.classList.remove("active");
    };

    if (menuToggle) menuToggle.addEventListener("click", openMenu);
    if (closeSidebar) closeSidebar.addEventListener("click", closeMenu);
    if (overlay) overlay.addEventListener("click", closeMenu);
});