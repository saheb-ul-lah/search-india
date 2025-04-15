<?php
$pageTitle = 'System Settings';
require_once '../../config/config.php'; // Ensure config is loaded first
require_once '../../config/functions.php';
require_once '../../includes/auth.php';   // For isAdmin() if not in functions.php

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user has permission (Admin Only)
if (!isAdmin()) {
    setFlashMessage('error', 'You do not have permission to access this page.');
    redirect('modules/dashboard/index.php');
    exit;
}

// --- Database Setup ---
$db = db();
$errors = []; // Initialize errors array

// --- FORM SUBMISSION HANDLING ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verify CSRF token first
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid security token. Please try again.');
        redirect('modules/settings/index.php');
        exit;
    }

    try {
        $db->beginTransaction();

        // --- Handle Logo Removal ---
        if (isset($_POST['action']) && $_POST['action'] === 'remove_logo') {
            // Get current logo path
            $db->query("SELECT setting_value FROM settings WHERE setting_key = 'logo'");
            $currentLogo = $db->single()['setting_value'] ?? null;

            if ($currentLogo) {
                // Delete the file
                deleteFile($currentLogo); // Assumes deleteFile handles path like 'logos/...'

                // Update database setting to NULL or empty string
                $db->query("UPDATE settings SET setting_value = NULL, updated_at = NOW() WHERE setting_key = 'logo'");
                if ($db->execute()) {
                    logActivity($_SESSION['user_id'], 'update', 'settings', null, 'Removed site logo');
                    setFlashMessage('success', 'Logo removed successfully.');
                } else {
                     setFlashMessage('error', 'Logo file deleted, but failed to update database setting.');
                }
            } else {
                 setFlashMessage('info', 'No logo was set to remove.');
            }

        }
        // --- Handle Settings Update (if not removing logo) ---
        elseif (isset($_POST['action']) && $_POST['action'] === 'save_settings') {

            // Loop through POST data to save settings
            foreach ($_POST as $key => $value) {
                // Skip non-setting fields
                if (in_array($key, ['csrf_token', 'action'])) {
                    continue;
                }

                // Sanitize value (basic sanitization, adjust if needed for specific fields)
                $sanitizedValue = sanitize($value);

                // Determine group (same logic as before, adjust if needed)
                $group = 'general'; // Default
                if (strpos($key, 'email_') === 0) $group = 'email';
                elseif (in_array($key, ['enable_registration', 'enable_email_verification'])) $group = 'users';
                elseif (in_array($key, ['primary_color', 'secondary_color', 'accent_color'])) $group = 'appearance'; // Logo handled separately
                elseif (in_array($key, ['enable_reviews', 'review_moderation', 'business_approval_required'])) $group = 'businesses';
                elseif (in_array($key, ['google_maps_api_key'])) $group = 'integrations';

                // Update setting in DB
                 $db->query("INSERT INTO settings (setting_key, setting_value, setting_group, updated_at)
                           VALUES (:key, :value, :group, NOW())
                           ON DUPLICATE KEY UPDATE
                           setting_value = VALUES(setting_value),
                           setting_group = VALUES(setting_group),
                           updated_at = NOW()"); // Update updated_at on change

                $db->bind(':key', $key);
                $db->bind(':value', $sanitizedValue);
                $db->bind(':group', $group);
                $db->execute();
            }

            // Handle logo upload (separate from the loop)
            $logoUpdateMessage = '';
            if (!empty($_FILES['logo_upload']['name'])) {
                $upload = uploadFile($_FILES['logo_upload'], 'logos', ['jpg', 'jpeg', 'png', 'svg', 'webp']); // Define allowed types
                if ($upload['success']) {
                     // Delete old logo file if upload is successful
                    $db->query("SELECT setting_value FROM settings WHERE setting_key = 'logo'");
                    $oldLogoPath = $db->single()['setting_value'] ?? null;
                    if ($oldLogoPath && $oldLogoPath !== $upload['path']) { // Check if path changed
                        deleteFile($oldLogoPath);
                    }

                    // Update 'logo' setting with the new path
                    $db->query("INSERT INTO settings (setting_key, setting_value, setting_group, updated_at)
                               VALUES ('logo', :path, 'appearance', NOW())
                               ON DUPLICATE KEY UPDATE
                               setting_value = VALUES(setting_value),
                               updated_at = NOW()");
                    $db->bind(':path', $upload['path']); // Path includes directory e.g., 'logos/filename.jpg'
                    $db->execute();
                    $logoUpdateMessage = ' New logo uploaded.';
                } else {
                    // Rollback transaction if logo upload fails but other settings might have been saved
                    $db->cancelTransaction();
                    setFlashMessage('error', 'Settings update failed. Logo upload error: ' . $upload['message']);
                    redirect('modules/settings/index.php');
                    exit;
                }
            }

            // If everything went well
            logActivity($_SESSION['user_id'], 'update', 'settings', null, 'Updated system settings');
            setFlashMessage('success', 'Settings updated successfully.' . $logoUpdateMessage);

        } // End save settings action

        $db->endTransaction();

    } catch (Exception $e) {
        $db->cancelTransaction();
        setFlashMessage('error', 'Error updating settings: ' . $e->getMessage());
        error_log("Settings update error: " . $e->getMessage());
        // No redirect here, let the page reload with the error
    }

    // Redirect after successful POST or logo removal to prevent resubmission
    if (empty($errors)) { // Only redirect if no errors were caught explicitly
        redirect('modules/settings/index.php');
        exit;
    }
    // If errors occurred within the try block and setFlashMessage was called, the page will reload below.

} // End POST handling


// --- Fetch Settings for Display ---
// Fetch fresh settings after potential updates or on initial load
$db->query("SELECT * FROM settings ORDER BY setting_group, setting_key");
$settingsResult = $db->resultSet();
$settings = [];
foreach ($settingsResult as $setting) {
    $settings[$setting['setting_group']][$setting['setting_key']] = $setting['setting_value'];
}


// --- Include Header & Sidebar ---
include_once '../../includes/header.php';
include_once '../../includes/sidebar.php';
?>

<!-- Apply layout change: Removed sm:ml-64 from outer div -->
<div class="container px-6 mx-auto">
    <div class="p-4 mt-14">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">System Settings</h2>
        </div>

        <!-- Flash Messages -->
        <?php $flash = getFlashMessage(); if ($flash): ?>
            <div id="flash-message" class="mb-6 p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' ?>" role="alert">
                <?= htmlspecialchars($flash['message']) ?>
                 <button type="button" class="float-right font-bold text-lg leading-none -mt-1" onclick="document.getElementById('flash-message').style.display='none'">×</button>
            </div>
        <?php endif; ?>

        <!-- Settings Form -->
        <form method="POST" enctype="multipart/form-data" action="settings/index.php">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

            <!-- Settings Cards -->
            <div class="space-y-6">

                <!-- General Settings Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">General Settings</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="site_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Site Name</label>
                                <input type="text" id="site_name" name="site_name" class="form-input" value="<?= htmlspecialchars($settings['general']['site_name'] ?? '') ?>" >
                            </div>
                            <div>
                                <label for="site_tagline" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Site Tagline</label>
                                <input type="text" id="site_tagline" name="site_tagline" class="form-input" value="<?= htmlspecialchars($settings['general']['site_tagline'] ?? '') ?>">
                            </div>
                            <div>
                                <label for="admin_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Admin Email</label>
                                <input type="email" id="admin_email" name="admin_email" class="form-input" value="<?= htmlspecialchars($settings['general']['admin_email'] ?? '') ?>" >
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">System notifications may be sent from this address.</p>
                            </div>
                            <div>
                                <label for="items_per_page" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Items Per Page</label>
                                <input type="number" id="items_per_page" name="items_per_page" class="form-input" value="<?= htmlspecialchars($settings['general']['items_per_page'] ?? '10') ?>" min="5" max="100" >
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Default number of items displayed on listing pages.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Settings Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                     <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">User Settings</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="hidden" name="enable_registration" value="0"> <!-- Hidden input for unchecked state -->
                                    <input id="enable_registration" name="enable_registration" type="checkbox" class="form-checkbox" <?= ($settings['users']['enable_registration'] ?? '1') == '1' ? 'checked' : '' ?> value="1">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="enable_registration" class="font-medium text-gray-900 dark:text-gray-300">Enable User Registration</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Allow new users to register on the website.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                 <div class="flex items-center h-5">
                                    <input type="hidden" name="enable_email_verification" value="0"> <!-- Hidden input -->
                                    <input id="enable_email_verification" name="enable_email_verification" type="checkbox" class="form-checkbox" <?= ($settings['users']['enable_email_verification'] ?? '1') == '1' ? 'checked' : '' ?> value="1">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="enable_email_verification" class="font-medium text-gray-900 dark:text-gray-300">Require Email Verification</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Users must verify their email address after registration.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                 <!-- Business Settings Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Business Settings</h3>
                    </div>
                    <div class="p-6">
                         <div class="space-y-4">
                             <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="hidden" name="enable_reviews" value="0">
                                    <input id="enable_reviews" name="enable_reviews" type="checkbox" class="form-checkbox" <?= ($settings['businesses']['enable_reviews'] ?? '1') == '1' ? 'checked' : '' ?> value="1">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="enable_reviews" class="font-medium text-gray-900 dark:text-gray-300">Enable Business Reviews</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Allow users to submit reviews for businesses.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="hidden" name="review_moderation" value="0">
                                    <input id="review_moderation" name="review_moderation" type="checkbox" class="form-checkbox" <?= ($settings['businesses']['review_moderation'] ?? '1') == '1' ? 'checked' : '' ?> value="1">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="review_moderation" class="font-medium text-gray-900 dark:text-gray-300">Require Review Moderation</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Reviews must be approved by an admin before appearing publicly.</p>
                                </div>
                            </div>
                             <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="hidden" name="business_approval_required" value="0">
                                    <input id="business_approval_required" name="business_approval_required" type="checkbox" class="form-checkbox" <?= ($settings['businesses']['business_approval_required'] ?? '1') == '1' ? 'checked' : '' ?> value="1">
                                </div>
                                <div class="ml-3 text-sm">
                                     <label for="business_approval_required" class="font-medium text-gray-900 dark:text-gray-300">Require Business Approval</label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">New business listings must be approved by an admin.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appearance Settings Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                     <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Appearance Settings</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                             <div>
                                <label for="primary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Primary Color</label>
                                <div class="flex items-center">
                                    <input type="color" id="primary_color_picker" class="h-10 w-10 p-0.5 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer" value="<?= htmlspecialchars($settings['appearance']['primary_color'] ?? '#3b82f6') ?>">
                                    <input type="text" id="primary_color" name="primary_color" class="form-input ml-2" value="<?= htmlspecialchars($settings['appearance']['primary_color'] ?? '#3b82f6') ?>" >
                                </div>
                            </div>
                            <div>
                                <label for="secondary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Secondary Color</label>
                                <div class="flex items-center">
                                    <input type="color" id="secondary_color_picker" class="h-10 w-10 p-0.5 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer" value="<?= htmlspecialchars($settings['appearance']['secondary_color'] ?? '#10b981') ?>">
                                    <input type="text" id="secondary_color" name="secondary_color" class="form-input ml-2" value="<?= htmlspecialchars($settings['appearance']['secondary_color'] ?? '#10b981') ?>" >
                                </div>
                            </div>
                            <div>
                                <label for="accent_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Accent Color</label>
                                <div class="flex items-center">
                                    <input type="color" id="accent_color_picker" class="h-10 w-10 p-0.5 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer" value="<?= htmlspecialchars($settings['appearance']['accent_color'] ?? '#f59e0b') ?>">
                                    <input type="text" id="accent_color" name="accent_color" class="form-input ml-2" value="<?= htmlspecialchars($settings['appearance']['accent_color'] ?? '#f59e0b') ?>" >
                                </div>
                            </div>
                        </div>
                        <div>
                             <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Site Logo</label>
                             <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                 <?php $currentLogo = $settings['appearance']['logo'] ?? null; ?>
                                 <?php if ($currentLogo): ?>
                                    <div class="flex-shrink-0">
                                        <img src="<?= UPLOADS_URL . '/' . htmlspecialchars($currentLogo) ?>" alt="Current Logo" class="h-12 max-w-xs object-contain bg-gray-100 dark:bg-gray-700 p-1 rounded border dark:border-gray-600">
                                    </div>
                                    <button type="submit" name="action" value="remove_logo" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-sm font-medium underline" onclick="return confirm('Are you sure you want to remove the current logo?')">Remove Logo</button>
                                <?php else: ?>
                                     <p class="text-sm text-gray-500 dark:text-gray-400">No logo uploaded.</p>
                                <?php endif; ?>
                             </div>
                             <div class="mt-4">
                                 <label for="logo_upload" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Upload New Logo</label>
                                 <input type="file" id="logo_upload" name="logo_upload" class="form-input-file" accept="image/png, image/jpeg, image/svg+xml, image/webp">
                                 <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Max 1MB. Recommended: Transparent PNG or SVG.</p>
                             </div>
                        </div>
                    </div>
                </div>

                <!-- Email Settings Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Email Settings (SMTP)</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure settings for sending emails (e.g., for verification, password resets).</p>
                    </div>
                    <div class="p-6">
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                             <div>
                                <label for="email_from_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Address</label>
                                <input type="email" id="email_from_address" name="email_from_address" class="form-input" value="<?= htmlspecialchars($settings['email']['from_address'] ?? '') ?>" placeholder="e.g., no-reply@yourdomain.com">
                            </div>
                            <div>
                                <label for="email_from_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Name</label>
                                <input type="text" id="email_from_name" name="email_from_name" class="form-input" value="<?= htmlspecialchars($settings['email']['from_name'] ?? ($settings['general']['site_name'] ?? '')) ?>" placeholder="e.g., Your Site Name">
                            </div>
                            <div>
                                <label for="email_smtp_host" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SMTP Host</label>
                                <input type="text" id="email_smtp_host" name="email_smtp_host" class="form-input" value="<?= htmlspecialchars($settings['email']['smtp_host'] ?? '') ?>" placeholder="e.g., smtp.mailgun.org">
                            </div>
                            <div>
                                <label for="email_smtp_port" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SMTP Port</label>
                                <input type="number" id="email_smtp_port" name="email_smtp_port" class="form-input" value="<?= htmlspecialchars($settings['email']['smtp_port'] ?? '587') ?>" placeholder="e.g., 587, 465, 25">
                            </div>
                             <div class="md:col-span-2"></div> <!-- Spacer -->
                            <div>
                                <label for="email_smtp_username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SMTP Username</label>
                                <input type="text" id="email_smtp_username" name="email_smtp_username" class="form-input" value="<?= htmlspecialchars($settings['email']['smtp_username'] ?? '') ?>" autocomplete="off">
                            </div>
                            <div>
                                <label for="email_smtp_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SMTP Password</label>
                                <input type="password" id="email_smtp_password" name="email_smtp_password" class="form-input" value="<?= htmlspecialchars($settings['email']['smtp_password'] ?? '') ?>" autocomplete="new-password">
                            </div>
                            <div>
                                <label for="email_smtp_encryption" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SMTP Encryption</label>
                                <select id="email_smtp_encryption" name="email_smtp_encryption" class="form-select">
                                    <option value="" <?= empty($settings['email']['smtp_encryption'] ?? null) ? 'selected' : '' ?>>None</option>
                                    <option value="tls" <?= ($settings['email']['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
                                    <option value="ssl" <?= ($settings['email']['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                 <!-- Integrations Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                     <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Integrations</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="google_maps_api_key" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Google Maps API Key</label>
                                <input type="text" id="google_maps_api_key" name="google_maps_api_key" class="form-input" value="<?= htmlspecialchars($settings['integrations']['google_maps_api_key'] ?? '') ?>">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Required for displaying maps on business pages. Get a key from Google Cloud Console.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- End space-y-6 -->

            <!-- Save Button -->
            <div class="mt-8 pt-5 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                 <button type="submit" name="action" value="save_settings" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                     Save All Settings
                 </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Simple JS to link color picker and text input
    document.querySelectorAll('input[type="color"]').forEach(picker => {
        const textInputId = picker.id.replace('_picker', '');
        const textInput = document.getElementById(textInputId);
        if (textInput) {
             // Update text input when color picker changes
            picker.addEventListener('input', (event) => {
                textInput.value = event.target.value;
            });
             // Update color picker when text input changes (optional, requires validation)
            textInput.addEventListener('change', (event) => {
                 // Basic check if it looks like a hex color
                 if (/^#[0-9A-F]{6}$/i.test(event.target.value)) {
                     picker.value = event.target.value;
                 }
            });
        }
    });
</script>

<?php require_once '../../includes/footer.php'; ?>

<!-- Add utility classes to your CSS if not already present -->
<style>
    /* Basic Form Input Styling (Tailwind utility classes used in HTML are preferred) */
    .form-input {
        @apply bg-gray-50 border border-gray-700 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500;
    }
    .form-select {
         @apply bg-gray-50 border border-gray-700 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500;
    }
    .form-checkbox {
        @apply w-4 h-4 text-blue-600 bg-gray-100 border-gray-700 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600;
    }
    .form-input-file {
         @apply block w-full text-sm text-gray-900 border border-gray-700 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400;
    }
</style>