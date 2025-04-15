/**
 * Main JavaScript file for the Justdial Admin Dashboard
 * Contains global functions and event handlers
 */

document.addEventListener('DOMContentLoaded', function () {
    // Initialize tooltips
    initTooltips();

    // Initialize dropdown menus
    initDropdowns();

    // Initialize mobile menu toggle
    initMobileMenu();

    // Initialize notification system
    initNotifications();

    // Initialize flash message auto-dismiss
    initFlashMessages();

    // Initialize form validation
    initFormValidation();

    // Initialize data tables (if present)
    initDataTables();

    // Initialize date pickers (if present)
    initDatePickers();

    // Initialize charts (if present)
    initCharts();

    // Initialize image previews
    initImagePreviews();

    // Initialize theme switcher
    initThemeSwitcher();

    // Initialize search functionality
    initGlobalSearch();
});

/**
 * Initialize tooltips
 */
function initTooltips() {
    const tooltipTriggers = document.querySelectorAll('[data-tooltip-target]');

    tooltipTriggers.forEach(trigger => {
        const tooltipId = trigger.getAttribute('data-tooltip-target');
        const tooltip = document.getElementById(tooltipId);

        if (!tooltip) return;

        trigger.addEventListener('mouseenter', () => {
            tooltip.classList.remove('hidden');

            // Position the tooltip
            const triggerRect = trigger.getBoundingClientRect();
            const tooltipRect = tooltip.getBoundingClientRect();

            tooltip.style.top = `${triggerRect.top - tooltipRect.height - 10}px`;
            tooltip.style.left = `${triggerRect.left + (triggerRect.width / 2) - (tooltipRect.width / 2)}px`;
        });

        trigger.addEventListener('mouseleave', () => {
            tooltip.classList.add('hidden');
        });
    });
}

/**
 * Initialize dropdown menus
 */
function initDropdowns() {
    const dropdownTriggers = document.querySelectorAll('[data-dropdown-toggle]');

    dropdownTriggers.forEach(trigger => {
        const dropdownId = trigger.getAttribute('data-dropdown-toggle');
        const dropdown = document.getElementById(dropdownId);

        if (!dropdown) return;

        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu.id !== dropdownId) {
                    menu.classList.add('hidden');
                }
            });

            // Toggle current dropdown
            dropdown.classList.toggle('hidden');

            // Position the dropdown
            const triggerRect = trigger.getBoundingClientRect();
            dropdown.style.top = `${triggerRect.bottom + 5}px`;
            dropdown.style.left = `${triggerRect.left}px`;
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
    });
}

/**
 * Initialize mobile menu toggle
 */
function initMobileMenu() {
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (!menuToggle || !sidebar || !overlay) return;

    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
        document.body.classList.toggle('overflow-hidden');
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    });
}

/**
 * Initialize notification system
 */
function initNotifications() {
    const notificationBell = document.getElementById('notification-bell');
    const notificationPanel = document.getElementById('notification-panel');

    if (!notificationBell || !notificationPanel) return;

    notificationBell.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();

        notificationPanel.classList.toggle('hidden');

        // Mark notifications as read
        if (!notificationPanel.classList.contains('hidden')) {
            const unreadBadge = document.getElementById('notification-badge');
            if (unreadBadge) {
                unreadBadge.classList.add('hidden');

                // Send AJAX request to mark notifications as read
                fetch('modules/notifications/mark-read.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
            }
        }
    });

    // Close notification panel when clicking outside
    document.addEventListener('click', (e) => {
        if (!notificationBell.contains(e.target) && !notificationPanel.contains(e.target)) {
            notificationPanel.classList.add('hidden');
        }
    });
}

/**
 * Initialize flash message auto-dismiss
 */
function initFlashMessages() {
    const flashMessages = document.querySelectorAll('.flash-message');

    flashMessages.forEach(message => {
        // Add close button functionality
        const closeBtn = message.querySelector('.close-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                message.remove();
            });
        }

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            message.classList.add('opacity-0');
            setTimeout(() => {
                message.remove();
            }, 300);
        }, 5000);
    });
}

/**
 * Initialize form validation
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate="true"]');

    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');

                    // Add error message if it doesn't exist
                    let errorMsg = field.nextElementSibling;
                    if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                        errorMsg = document.createElement('p');
                        errorMsg.classList.add('error-message', 'text-red-500', 'text-xs', 'mt-1');
                        errorMsg.textContent = 'This field is required';
                        field.parentNode.insertBefore(errorMsg, field.nextSibling);
                    }
                } else {
                    field.classList.remove('border-red-500');

                    // Remove error message if it exists
                    const errorMsg = field.nextElementSibling;
                    if (errorMsg && errorMsg.classList.contains('error-message')) {
                        errorMsg.remove();
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });

        // Remove error styling on input
        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('input', () => {
                field.classList.remove('border-red-500');

                // Remove error message if it exists
                const errorMsg = field.nextElementSibling;
                if (errorMsg && errorMsg.classList.contains('error-message')) {
                    errorMsg.remove();
                }
            });
        });
    });
}

/**
 * Initialize data tables
 */
function initDataTables() {
    const tables = document.querySelectorAll('table.data-table');

    tables.forEach(table => {
        const searchInput = document.querySelector(`#${table.id}-search`);
        const rows = table.querySelectorAll('tbody tr');

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                const searchTerm = searchInput.value.toLowerCase();

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        // Add sorting functionality
        const headers = table.querySelectorAll('th[data-sort]');

        headers.forEach(header => {
            header.addEventListener('click', () => {
                const column = header.getAttribute('data-sort');
                const direction = header.getAttribute('data-direction') === 'asc' ? 'desc' : 'asc';

                // Update direction attribute
                headers.forEach(h => h.setAttribute('data-direction', ''));
                header.setAttribute('data-direction', direction);

                // Update sort icons
                headers.forEach(h => {
                    h.querySelectorAll('.sort-icon').forEach(icon => icon.classList.add('hidden'));
                });

                const iconToShow = direction === 'asc' ? header.querySelector('.sort-asc') : header.querySelector('.sort-desc');
                if (iconToShow) {
                    iconToShow.classList.remove('hidden');
                }

                // Sort rows
                const sortedRows = Array.from(rows).sort((a, b) => {
                    const aValue = a.querySelector(`td[data-column="${column}"]`).textContent.trim();
                    const bValue = b.querySelector(`td[data-column="${column}"]`).textContent.trim();

                    if (direction === 'asc') {
                        return aValue.localeCompare(bValue);
                    } else {
                        return bValue.localeCompare(aValue);
                    }
                });

                // Append sorted rows
                const tbody = table.querySelector('tbody');
                sortedRows.forEach(row => tbody.appendChild(row));
            });
        });
    });
}

/**
 * Initialize date pickers
 */
function initDatePickers() {
    const datePickers = document.querySelectorAll('.date-picker');

    if (datePickers.length > 0 && typeof flatpickr !== 'undefined') {
        datePickers.forEach(input => {
            flatpickr(input, {
                dateFormat: 'Y-m-d',
                allowInput: true,
                disableMobile: true
            });
        });
    }
}

/**
 * Initialize charts
 */
function initCharts() {
    // Charts are initialized in chart-config.js
    if (typeof initDashboardCharts === 'function') {
        initDashboardCharts();
    }
}

/**
 * Initialize image previews
 */
function initImagePreviews() {
    const imageInputs = document.querySelectorAll('input[type="file"][data-preview]');

    imageInputs.forEach(input => {
        const previewId = input.getAttribute('data-preview');
        const preview = document.getElementById(previewId);

        if (!preview) return;

        input.addEventListener('change', () => {
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = (e) => {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                };

                reader.readAsDataURL(input.files[0]);
            }
        });
    });
}

/**
 * Initialize theme switcher
 */
function initThemeSwitcher() {
    const themeToggle = document.getElementById('theme-toggle');
    const htmlElement = document.documentElement;

    if (!themeToggle) return;

    // Check for saved theme preference or use system preference
    const savedTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
        htmlElement.classList.add('dark');
        themeToggle.checked = true;
    }

    // Toggle theme on change
    themeToggle.addEventListener('change', () => {
        if (themeToggle.checked) {
            htmlElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            htmlElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    });
}

/**
 * Initialize global search functionality
 */
function initGlobalSearch() {
    const searchInput = document.getElementById('global-search');
    const searchResults = document.getElementById('global-search-results');

    if (!searchInput || !searchResults) return;

    let searchTimeout;

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.trim();

        // Clear previous timeout
        clearTimeout(searchTimeout);

        if (query.length < 2) {
            searchResults.classList.add('hidden');
            return;
        }

        // Set new timeout to prevent too many requests
        searchTimeout = setTimeout(() => {
            // Show loading indicator
            searchResults.innerHTML = '<div class="p-4 text-center"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';
            searchResults.classList.remove('hidden');

            // Send AJAX request
            fetch(`modules/search/global-search.php?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.results.length === 0) {
                        searchResults.innerHTML = '<div class="p-4 text-center text-gray-500">No results found</div>';
                        return;
                    }

                    // Build results HTML
                    let resultsHtml = '';

                    // Group results by type
                    const groupedResults = data.results.reduce((acc, result) => {
                        if (!acc[result.type]) {
                            acc[result.type] = [];
                        }
                        acc[result.type].push(result);
                        return acc;
                    }, {});

                    // Generate HTML for each group
                    for (const [type, results] of Object.entries(groupedResults)) {
                        resultsHtml += `
                        <div class="px-4 py-2 bg-gray-100 font-medium text-sm text-gray-600">${type.charAt(0).toUpperCase() + type.slice(1)}s</div>
                        <div class="divide-y divide-gray-100">
                    `;

                        results.forEach(result => {
                            resultsHtml += `
                            <a href="${result.url}" class="flex items-center px-4 py-3 hover:bg-gray-50">
                                <div class="flex-shrink-0 mr-3">
                                    ${result.icon ? `<i class="${result.icon} text-gray-400"></i>` : ''}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">${result.title}</p>
                                    <p class="text-sm text-gray-500 truncate">${result.description || ''}</p>
                                </div>
                            </a>
                        `;
                        });

                        resultsHtml += '</div>';
                    }

                    searchResults.innerHTML = resultsHtml;
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchResults.innerHTML = '<div class="p-4 text-center text-red-500">An error occurred while searching</div>';
                });
        }, 300);
    });

    // Close search results when clicking outside
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });

    // Show results when focusing on search input
    searchInput.addEventListener('focus', () => {
        if (searchInput.value.trim().length >= 2) {
            searchResults.classList.remove('hidden');
        }
    });
}