</div>
            </main>
            
            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 p-4">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            &copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.
                        </div>
                        <div class="text-sm text-gray-500">
                            Version 1.0.0
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="<?= ASSETS_PATH ?>/js/main.js"></script>
    <script src="<?= ASSETS_PATH ?>/js/sidebar.js"></script>
    
    <?php if (isset($useCharts) && $useCharts): ?>
    <script src="<?= ASSETS_PATH ?>/js/chart-config.js"></script>
    <?php endif; ?>
    
    <script>
        // User menu toggle
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenu = document.getElementById('user-menu');
        
        if (userMenuButton && userMenu) {
            userMenuButton.addEventListener('click', () => {
                userMenu.classList.toggle('hidden');
            });
            
            // Close the menu when clicking outside
            document.addEventListener('click', (event) => {
                if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                    userMenu.classList.add('hidden');
                }
            });
        }
        
        // Flash message close button
        const flashMessage = document.getElementById('flash-message');
        const closeFlashButtons = document.querySelectorAll('.close-flash');
        
        if (flashMessage && closeFlashButtons.length > 0) {
            closeFlashButtons.forEach(button => {
                button.addEventListener('click', () => {
                    flashMessage.classList.add('hidden');
                });
            });
            
            // Auto-hide flash message after 5 seconds
            setTimeout(() => {
                flashMessage.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => {
                    flashMessage.classList.add('hidden');
                }, 500);
            }, 5000);
        }
    </script>
</body>
</html>