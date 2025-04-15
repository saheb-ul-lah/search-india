/**
 * Sidebar functionality for the Justdial Admin Dashboard
 * Handles sidebar toggle, active menu items, and responsive behavior
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize sidebar
    initSidebar();
    
    // Set active menu item based on current URL
    setActiveMenuItem();
    
    // Initialize collapsible menu items
    initCollapsibleMenus();
    
    // Handle sidebar toggle for mobile
    initSidebarToggle();
    
    // Handle sidebar resize
    initSidebarResize();
    
    // Initialize sidebar search
    initSidebarSearch();
});

/**
 * Initialize sidebar functionality
 */
function initSidebar() {
    // Check for saved sidebar state
    const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const sidebarToggleBtn = document.getElementById('sidebar-toggle');
    
    if (!sidebar || !mainContent || !sidebarToggleBtn) return;
    
    // Apply saved state
    if (sidebarCollapsed) {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
        sidebarToggleBtn.querySelector('i').classList.replace('fa-chevron-left', 'fa-chevron-right');
    }
    
    // Add transition class after initial load to prevent animation on page load
    setTimeout(() => {
        sidebar.classList.add('transition-all');
        mainContent.classList.add('transition-all');
    }, 100);
}

/**
 * Set active menu item based on current URL
 */
function setActiveMenuItem() {
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll('.sidebar-menu a');
    
    menuItems.forEach(item => {
        const href = item.getAttribute('href');
        
        // Check if the current path includes the href (excluding root path)
        if (href !== '/' && currentPath.includes(href)) {
            // Mark the item as active
            item.classList.add('active', 'bg-primary-50', 'text-primary-600');
            item.classList.remove('text-gray-700', 'hover:bg-gray-100');
            
            // If the item is in a submenu, expand the parent menu
            const parentMenu = item.closest('.submenu');
            if (parentMenu) {
                const parentToggle = parentMenu.previousElementSibling;
                if (parentToggle && parentToggle.classList.contains('submenu-toggle')) {
                    parentToggle.classList.add('active');
                    parentToggle.setAttribute('aria-expanded', 'true');
                    parentMenu.classList.remove('hidden');
                }
            }
        }
    });
}

/**
 * Initialize collapsible menu items
 */
function initCollapsibleMenus() {
    const submenuToggles = document.querySelectorAll('.submenu-toggle');
    
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            
            const submenu = toggle.nextElementSibling;
            const icon = toggle.querySelector('.submenu-icon');
            
            // Toggle submenu visibility
            submenu.classList.toggle('hidden');
            
            // Update aria-expanded attribute
            const isExpanded = submenu.classList.contains('hidden') ? 'false' : 'true';
            toggle.setAttribute('aria-expanded', isExpanded);
            
            // Rotate icon
            if (icon) {
                icon.classList.toggle('rotate-90', isExpanded === 'true');
            }
        });
    });
}

/**
 * Handle sidebar toggle for mobile
 */
function initSidebarToggle() {
    const sidebarToggleBtn = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const overlay = document.getElementById('sidebar-overlay');
    
    if (!sidebarToggleBtn || !sidebar || !mainContent) return;
    
    sidebarToggleBtn.addEventListener('click', () => {
        const isCollapsed = sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded', isCollapsed);
        
        // Update toggle button icon
        const icon = sidebarToggleBtn.querySelector('i');
        if (icon) {
            icon.classList.toggle('fa-chevron-left', !isCollapsed);
            icon.classList.toggle('fa-chevron-right', isCollapsed);
        }
        
        // Save state to localStorage
        localStorage.setItem('sidebarCollapsed', isCollapsed);
    });
    
    // Mobile sidebar toggle
    const mobileSidebarToggle = document.getElementById('mobile-sidebar-toggle');
    
    if (mobileSidebarToggle && overlay) {
        mobileSidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        });
        
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('mobile-open');
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        });
    }
}

/**
 * Handle sidebar resize
 */
function initSidebarResize() {
    const resizer = document.getElementById('sidebar-resizer');
    const sidebar = document.getElementById('sidebar');
    
    if (!resizer || !sidebar) return;
    
    let isResizing = false;
    let startX, startWidth;
    
    resizer.addEventListener('mousedown', (e) => {
        isResizing = true;
        startX = e.clientX;
        startWidth = parseInt(document.defaultView.getComputedStyle(sidebar).width, 10);
        
        document.body.classList.add('select-none');
    });
    
    document.addEventListener('mousemove', (e) => {
        if (!isResizing) return;
        
        const width = startWidth + (e.clientX - startX);
        
        // Limit width between 200px and 400px
        if (width >= 200 && width <= 400) {
            sidebar.style.width = `${width}px`;
        }
    });
    
    document.addEventListener('mouseup', () => {
        if (isResizing) {
            isResizing = false;
            document.body.classList.remove('select-none');
        }
    });
}

/**
 * Initialize sidebar search
 */
function initSidebarSearch() {
    const searchInput = document.getElementById('sidebar-search');
    const menuItems = document.querySelectorAll('.sidebar-menu li:not(.menu-header)');
    
    if (!searchInput) return;
    
    searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase().trim();
        
        if (searchTerm === '') {
            // Show all menu items
            menuItems.forEach(item => {
                item.classList.remove('hidden');
            });
            
            // Reset submenu visibility
            document.querySelectorAll('.submenu').forEach(submenu => {
                submenu.classList.add('hidden');
            });
            
            document.querySelectorAll('.submenu-toggle').forEach(toggle => {
                toggle.setAttribute('aria-expanded', 'false');
                const icon = toggle.querySelector('.submenu-icon');
                if (icon) {
                    icon.classList.remove('rotate-90');
                }
            });
            
            return;
        }
        
        // Search menu items
        menuItems.forEach(item => {
            const link = item.querySelector('a');
            if (!link) return;
            
            const text = link.textContent.toLowerCase();
            const isMatch = text.includes(searchTerm);
            
            item.classList.toggle('hidden', !isMatch);
            
            // If item is in a submenu and matches, show the parent submenu
            if (isMatch) {
                const submenu = item.closest('.submenu');
                if (submenu) {
                    submenu.classList.remove('hidden');
                    
                    const toggle = submenu.previousElementSibling;
                    if (toggle && toggle.classList.contains('submenu-toggle')) {
                        toggle.setAttribute('aria-expanded', 'true');
                        const icon = toggle.querySelector('.submenu-icon');
                        if (icon) {
                            icon.classList.add('rotate-90');
                        }
                    }
                }
            }
        });
    });
}

/**
 * Handle sidebar menu item click for mobile
 */
function handleMenuItemClick() {
    // Close sidebar on mobile when a menu item is clicked
    if (window.innerWidth < 768) {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        
        if (sidebar && overlay) {
            sidebar.classList.remove('mobile-open');
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }
}

// Add click event listener to all menu items
document.addEventListener('DOMContentLoaded', () => {
    const menuItems = document.querySelectorAll('.sidebar-menu a:not(.submenu-toggle)');
    
    menuItems.forEach(item => {
        item.addEventListener('click', handleMenuItemClick);
    });
});