<?php
// justdial-public/includes/footer.php

// You might fetch some footer settings here if needed
// $admin_email = get_setting('admin_email', 'info@findit.com');
?>
    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <!-- Company Info -->
                <div>
                     <div class="flex items-center mb-6">
                        <!-- Use same logo logic as header -->
                        <?php
                         $site_name = get_setting('site_name', 'FindIt');
                         $site_logo_filename = get_setting('logo');
                         $site_logo_url = $site_logo_filename ? get_image_url('logos', $site_logo_filename, 'assets/img/logo.png') : 'assets/img/logo.png';
                        ?>
                        <?php if ($site_logo_filename): ?>
                            <img src="<?php echo $site_logo_url; ?>" alt="<?php safe_echo($site_name); ?> Logo" class="h-10 mr-2 filter brightness-0 invert"> <!-- Invert for dark background -->
                        <?php else: ?>
                        <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-primary-500 to-secondary-500 flex items-center justify-center text-white font-bold text-xl mr-2">
                            <?php echo strtoupper(substr($site_name, 0, 1)); ?>
                        </div>
                        <?php endif; ?>
                        <span class="ml-2 text-2xl font-bold font-montserrat"><?php echo preg_replace('/(It)$/i', '<span class="text-primary-400">$1</span>', $site_name); ?></span>
                    </div>
                    <p class="text-gray-400 mb-4"><?php safe_echo(get_setting('site_tagline', 'Your trusted source...')); ?></p>
                    <div class="flex space-x-4">
                        <!-- Add actual social links from settings if available -->
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-6">Quick Links</h3>
                    <ul class="space-y-3">
                         <!-- Update links to point to actual pages when created -->
                        <li><a href="index.php#home" class="text-gray-400 hover:text-white transition-colors duration-200">Home</a></li>
                        <li><a href="index.php#categories" class="text-gray-400 hover:text-white transition-colors duration-200">Categories</a></li>
                        <li><a href="index.php#businesses" class="text-gray-400 hover:text-white transition-colors duration-200">Businesses</a></li>
                        <li><a href="index.php#how-it-works" class="text-gray-400 hover:text-white transition-colors duration-200">How It Works</a></li>
                        <li><a href="index.php#testimonials" class="text-gray-400 hover:text-white transition-colors duration-200">Testimonials</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">FAQ</a></li>
                    </ul>
                </div>

                <!-- For Business -->
                <div>
                    <h3 class="text-lg font-semibold mb-6">For Business</h3>
                    <ul class="space-y-3">
                        <!-- Update links -->
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">List Your Business</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Pricing Plans</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Business Dashboard</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Resources</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Success Stories</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-6">Contact Us</h3>
                    <ul class="space-y-3">
                        <!-- TODO: Fetch contact details from settings if available -->
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-primary-400"></i>
                            <span class="text-gray-400">123 Business Avenue, Suite 100<br>New York, NY 10001</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-3 text-primary-400"></i>
                            <span class="text-gray-400">(555) 123-4567</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-primary-400"></i>
                             <a href="mailto:<?php safe_echo(get_setting('admin_email', 'info@findit.com')); ?>" class="text-gray-400 hover:text-white"><?php safe_echo(get_setting('admin_email', 'info@findit.com')); ?></a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock mr-3 text-primary-400"></i>
                            <span class="text-gray-400">Mon-Fri: 9AM - 5PM</span>
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="border-gray-800 mb-8">

            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-500 text-sm mb-4 md:mb-0">
                    © <?php echo date('Y'); ?> <?php safe_echo(get_setting('site_name', 'FindIt')); ?>. All rights reserved.
                </div>
                <div class="flex space-x-6">
                    <!-- TODO: Add links to actual policy pages -->
                    <a href="#" class="text-gray-500 hover:text-white text-sm transition-colors duration-200">Terms of Service</a>
                    <a href="#" class="text-gray-500 hover:text-white text-sm transition-colors duration-200">Privacy Policy</a>
                    <a href="#" class="text-gray-500 hover:text-white text-sm transition-colors duration-200">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-6 right-6 bg-primary-600 text-white rounded-full p-3 shadow-lg hidden hover:bg-primary-700 transition-all duration-300 z-50 transform hover:scale-110">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Modals -->
    <!-- Login Modal -->
    <div id="login-modal" class="modal fixed inset-0 flex items-center justify-center z-50 hidden opacity-0">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="modal-content relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform scale-90">
            <button class="modal-close absolute top-4 right-4 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
            <div class="p-8">
                <h2 class="text-2xl font-bold mb-6 text-center">Login to Your Account</h2>
                <!-- TODO: Point form action to actual login handler -->
                <form id="login-form" class="space-y-4" method="POST" action="login_handler.php">
                    <div>
                        <label for="login-email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="login-email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                    </div>
                    <div>
                        <label for="login-password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="login-password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember-me" name="remember" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <label for="remember-me" class="ml-2 block text-sm text-gray-700">Remember me</label>
                        </div>
                        <a href="#" class="text-sm text-primary-600 hover:text-primary-700">Forgot password?</a>
                    </div>
                    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">Login</button>
                </form>
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">Don't have an account? <button id="switch-to-signup" class="text-primary-600 hover:text-primary-700 font-medium">Sign up</button></p>
                </div>
                <!-- Social Login (Keep static for now) -->
                <!-- ... -->
            </div>
        </div>
    </div>

    <!-- Signup Modal -->
     <div id="signup-modal" class="modal fixed inset-0 flex items-center justify-center z-50 hidden opacity-0">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="modal-content relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform scale-90">
            <button class="modal-close absolute top-4 right-4 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
            <div class="p-8">
                <h2 class="text-2xl font-bold mb-6 text-center">Create an Account</h2>
                 <!-- TODO: Point form action to actual signup handler -->
                <form id="signup-form" class="space-y-4" method="POST" action="signup_handler.php">
                     <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="first-name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" id="first-name" name="first_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                        </div>
                        <div>
                            <label for="last-name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" id="last-name" name="last_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                        </div>
                    </div>
                    <div>
                        <label for="signup-email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="signup-email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                    </div>
                    <div>
                        <label for="signup-password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="signup-password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                    </div>
                    <div>
                        <label for="confirm-password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" id="confirm-password" name="confirm_password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="terms" name="terms" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded" required>
                        <label for="terms" class="ml-2 block text-sm text-gray-700">I agree to the <a href="#" class="text-primary-600 hover:text-primary-700">Terms</a> and <a href="#" class="text-primary-600 hover:text-primary-700">Privacy Policy</a></label>
                    </div>
                    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">Sign Up</button>
                </form>
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">Already have an account? <button id="switch-to-login" class="text-primary-600 hover:text-primary-700 font-medium">Login</button></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Details Modal (Keep static structure for now) -->
    <div id="business-modal" class="modal fixed inset-0 flex items-center justify-center z-50 hidden opacity-0">
        <!-- Content will be loaded via JS or separate page later -->
         <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="modal-content relative bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 transform scale-90 max-h-[90vh] overflow-y-auto">
             <button class="modal-close absolute top-4 right-4 text-gray-500 hover:text-gray-700 z-10">
                <i class="fas fa-times"></i>
            </button>
            <div id="business-modal-content" class="p-8">
                <!-- Placeholder: Content will be loaded dynamically -->
                Loading business details...
             </div>
         </div>
    </div>

    <!-- Success Modal -->
    <div id="success-modal" class="modal fixed inset-0 flex items-center justify-center z-50 hidden opacity-0">
         <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="modal-content relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform scale-90">
            <div class="p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                    <i class="fas fa-check text-2xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Success!</h3>
                <p class="text-gray-600 mb-6" id="success-message">Your action was completed successfully.</p>
                <button class="modal-close bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200 mx-auto">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

    <!-- Custom Scripts -->
    <script>
        // Initialize AOS animations
        AOS.init({
            once: true,
            duration: 800,
            offset: 100,
        });

        // --- Mobile Menu ---
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // --- Smooth Scroll ---
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href.length > 1 && document.querySelector(href)) { // Ensure it's an internal anchor
                    e.preventDefault();
                    const targetElement = document.querySelector(href);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 80, // Adjust offset for sticky header
                            behavior: 'smooth'
                        });
                        if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                            mobileMenu.classList.add('hidden');
                        }
                    }
                }
            });
        });

        // --- Back to Top Button ---
        const backToTopButton = document.getElementById('back-to-top');
        if (backToTopButton) {
            window.addEventListener('scroll', () => {
                if (window.pageYOffset > 300) {
                    backToTopButton.classList.remove('hidden');
                } else {
                    backToTopButton.classList.add('hidden');
                }
            });
            backToTopButton.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

         // --- Scroll Indicator ---
        const scrollIndicator = document.querySelector('.scroll-indicator');
        if (scrollIndicator) {
            window.addEventListener('scroll', () => {
                const windowHeight = window.innerHeight;
                const documentHeight = document.documentElement.scrollHeight;
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop; // More robust scroll detection
                const scrollPercentage = (scrollTop / (documentHeight - windowHeight)) * 100;
                scrollIndicator.style.width = Math.min(scrollPercentage, 100) + '%'; // Cap at 100%
            });
        }

        // --- Testimonial Slider ---
        const testimonialSliderEl = document.querySelector('.testimonial-slider .swiper-container');
        if (testimonialSliderEl) {
            const testimonialSlider = new Swiper(testimonialSliderEl, {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                autoplay: { delay: 5000, disableOnInteraction: false },
                pagination: { el: '.swiper-pagination', clickable: true },
                navigation: { nextEl: '.testimonial-next', prevEl: '.testimonial-prev' }
            });
        }

        // --- FAQ Accordion ---
        const faqItems = document.querySelectorAll('.faq-item');
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            const answer = item.querySelector('.faq-answer');
            const icon = question.querySelector('i');

            question.addEventListener('click', () => {
                const isOpening = answer.classList.contains('hidden');

                // Close all other answers first
                faqItems.forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.querySelector('.faq-answer').classList.add('hidden');
                        otherItem.querySelector('.faq-question i').classList.remove('rotate-180');
                    }
                });

                // Toggle the clicked one
                if (isOpening) {
                    answer.classList.remove('hidden');
                    icon.classList.add('rotate-180');
                } else {
                    answer.classList.add('hidden');
                    icon.classList.remove('rotate-180');
                }
            });
        });

        // --- Tabs ---
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        if (tabButtons.length > 0 && tabContents.length > 0) {
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const tabId = button.getAttribute('data-tab');
                    const targetContent = document.getElementById(tabId);

                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'bg-primary-600', 'text-white');
                        btn.classList.add('bg-gray-200', 'text-gray-700');
                    });
                    tabContents.forEach(content => content.classList.remove('active'));

                    button.classList.add('active', 'bg-primary-600', 'text-white');
                    button.classList.remove('bg-gray-200', 'text-gray-700');
                    if(targetContent) {
                        targetContent.classList.add('active');
                    }
                });
            });
        }

        // --- Counter Animation ---
        const counterElements = document.querySelectorAll('.counter-value');
        let countersStarted = false;
        function startCounters() {
             if (countersStarted || counterElements.length === 0) return;
             countersStarted = true; // Prevent re-triggering

            counterElements.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-count')) || 0;
                const duration = 2000;
                let startTimestamp = null;

                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    const currentValue = Math.floor(progress * target);
                    counter.textContent = currentValue.toLocaleString();
                    if (progress < 1) {
                        requestAnimationFrame(step);
                    }
                };
                requestAnimationFrame(step);
            });
        }

        const statsSection = document.querySelector('.parallax'); // Target the stats section
        if (statsSection && typeof IntersectionObserver !== 'undefined') {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        startCounters();
                        observer.unobserve(entry.target); // Stop observing once triggered
                    }
                });
            }, { threshold: 0.1 }); // Trigger when 10% visible
            observer.observe(statsSection);
        } else {
             // Fallback for older browsers or if section not found
             startCounters();
        }


        // --- Modal Functionality ---
        const modals = document.querySelectorAll('.modal');
        const modalTriggers = {
            'login-button': 'login-modal',
            'mobile-login-button': 'login-modal',
            'signup-button': 'signup-modal',
            'mobile-signup-button': 'signup-modal',
            'switch-to-signup': 'signup-modal',
            'switch-to-login': 'login-modal',
             // Removing direct success triggers, handle after form submission instead
             // 'learn-more-button': 'success-modal',
             // 'about-button': 'success-modal',
             // 'list-business-button': 'success-modal',
             // 'view-plans-button': 'success-modal'
        };
        const businessLinks = document.querySelectorAll('.business-link'); // Trigger for business modal

        function openModal(modalId) {
             const modal = document.getElementById(modalId);
             if (!modal) return;
             // Close any currently open modals
             closeAllModals();
             modal.classList.remove('hidden');
             // Use setTimeout to allow display:flex to take effect before transition
             setTimeout(() => {
                 modal.classList.add('active', 'opacity-100');
                 modal.querySelector('.modal-content')?.classList.remove('scale-90'); // Animate content in
             }, 10);
        }

        function closeModal(modal) {
            if (!modal) return;
            modal.classList.remove('active', 'opacity-100');
            modal.querySelector('.modal-content')?.classList.add('scale-90'); // Animate content out
            // Wait for animation to finish before hiding
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

         function closeAllModals() {
             modals.forEach(m => closeModal(m));
         }

        // Attach triggers
        Object.keys(modalTriggers).forEach(triggerId => {
            const trigger = document.getElementById(triggerId);
            if (trigger) {
                trigger.addEventListener('click', (e) => {
                    e.preventDefault(); // Prevent default button behavior if any
                    const modalId = modalTriggers[triggerId];
                    openModal(modalId);
                });
            }
        });

         // Business Modal Trigger
        businessLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const businessId = link.getAttribute('data-business-id'); // Assume link has data-business-id attribute
                const modalId = 'business-modal';
                const modalContent = document.getElementById('business-modal-content');

                if (modalContent) {
                     modalContent.innerHTML = 'Loading business details...'; // Placeholder
                     openModal(modalId);

                     // --- AJAX to load business details ---
                     // TODO: Implement actual AJAX call here
                     // fetch('get_business_details.php?id=' + businessId)
                     // .then(response => response.text()) // or response.json()
                     // .then(data => {
                     //     modalContent.innerHTML = data; // Populate modal
                     // })
                     // .catch(error => {
                     //     modalContent.innerHTML = 'Error loading details.';
                     //     console.error('Error:', error);
                     // });

                     // --- Dummy data for now ---
                     setTimeout(() => {
                         modalContent.innerHTML = `
                             <h2 class="text-xl font-bold mb-4">Business Name (ID: ${businessId || 'N/A'})</h2>
                             <p>Details loaded for this business...</p>
                             <p>Replace this with actual content fetched via AJAX.</p>
                         `;
                     }, 1000); // Simulate loading time
                }
            });
        });

        // Close modal buttons
        document.querySelectorAll('.modal-close').forEach(button => {
            button.addEventListener('click', () => {
                closeModal(button.closest('.modal'));
            });
        });

        // Close modal on outside click
        modals.forEach(modal => {
            modal.addEventListener('click', (e) => {
                // Check if the click is directly on the modal backdrop
                if (e.target === modal) {
                    closeModal(modal);
                }
            });
        });

        // --- Form Submissions (Example for Newsletter) ---
        const newsletterForm = document.getElementById('newsletter-form');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', (e) => {
                e.preventDefault();
                // TODO: Add actual AJAX submission logic here
                console.log('Newsletter form submitted');

                // Show success message
                const successModal = document.getElementById('success-modal');
                const successMessage = document.getElementById('success-message');
                if (successModal && successMessage) {
                     successMessage.textContent = 'Thank you for subscribing!';
                     openModal('success-modal');
                }
                newsletterForm.reset();
            });
        }
        // NOTE: Login/Signup forms currently have static actions (login_handler.php, signup_handler.php)
        // You would typically handle these server-side and redirect or return JSON responses.
        // Using the success modal for them might require JS interception and AJAX.

    </script>

</body>
</html>