<?php
// Database configuration
$host = 'localhost';
$dbname = 'consultation_db';
$username = 'root';
$password = '';

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Prepare SQL statement
        $stmt = $pdo->prepare("INSERT INTO consultation_applications (
            full_name, email, phone, business_name, website, location, business_stage, 
            business_type, has_logo, online_presence, website_type, consultation_goals, 
            budget_range, support_needs, preferred_datetime, contact_method, additional_details, 
            consent_agreed, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        // Execute with form data
        $stmt->execute([
            $_POST['full_name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['business_name'] ?? '',
            $_POST['website'] ?? '',
            $_POST['location'],
            $_POST['business_stage'],
            $_POST['business_type'],
            $_POST['has_logo'],
            implode(', ', $_POST['online_presence'] ?? []),
            $_POST['website_type'],
            $_POST['consultation_goals'],
            $_POST['budget_range'] ?? '',
            implode(', ', $_POST['support_needs'] ?? []),
            $_POST['preferred_datetime'],
            $_POST['contact_method'],
            $_POST['additional_details'] ?? '',
            isset($_POST['consent']) ? 1 : 0
        ]);
        
        $message = 'Thank you! Your consultation request has been submitted successfully. We will contact you soon.';
        $messageType = 'success';
        
    } catch (PDOException $e) {
        $message = 'Error: ' . $e->getMessage();
        $messageType = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Consultation Application Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .form-section {
            background: #f8f9fa;
            border-left: 4px solid #0d6efd;
            margin-bottom: 2rem;
            padding: 1.5rem;
            border-radius: 0.5rem;
        }
        .form-section h4 {
            color: #0d6efd;
            margin-bottom: 1rem;
        }
        .required {
            color: #dc3545;
        }
        body {
            background-color: #f5f5f5;
        }
        .form-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 2rem;
            border-radius: 10px 10px 0 0;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="form-container">
                    <div class="header-section">
                        <h1><i class="bi bi-briefcase"></i> Business Consultation Application</h1>
                        <p class="mb-0">Let's discuss how we can help grow your business</p>
                    </div>
                    
                    <div class="p-4">
                        <?php if ($message): ?>
                            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                                <i class="bi bi-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                                <?= htmlspecialchars($message) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" id="consultationForm">
                            <!-- Basic Information -->
                            <div class="form-section">
                                <h4><i class="bi bi-person-circle"></i> Basic Information</h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="full_name" class="form-label">Full Name <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address <span class="required">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone Number (WhatsApp preferred)</label>
                                        <input type="tel" class="form-control" id="phone" name="phone">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="business_name" class="form-label">Business Name</label>
                                        <input type="text" class="form-control" id="business_name" name="business_name">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="website" class="form-label">Business Website</label>
                                        <input type="url" class="form-control" id="website" name="website" placeholder="https://">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="location" class="form-label">Location / Country</label>
                                        <input type="text" class="form-control" id="location" name="location">
                                    </div>
                                </div>
                            </div>

                            <!-- Business Details -->
                            <div class="form-section">
                                <h4><i class="bi bi-building"></i> Business Details</h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="business_stage" class="form-label">Business Stage <span class="required">*</span></label>
                                        <select class="form-select" id="business_stage" name="business_stage" required>
                                            <option value="">Select stage...</option>
                                            <option value="just_idea">Just an idea</option>
                                            <option value="newly_launched">Newly launched</option>
                                            <option value="few_months">Running for a few months</option>
                                            <option value="over_1_year">Running for over 1 year</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="has_logo" class="form-label">Do you have a logo/brand identity?</label>
                                        <select class="form-select" id="has_logo" name="has_logo">
                                            <option value="">Select...</option>
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                            <option value="need_help">Need help with this</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="business_type" class="form-label">Type of Business <span class="required">*</span></label>
                                        <textarea class="form-control" id="business_type" name="business_type" rows="2" placeholder="e.g., clothing brand, food service, freelancing, digital products, etc." required></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Online Presence -->
                            <div class="form-section">
                                <h4><i class="bi bi-globe"></i> Online Presence</h4>
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Current Online Presence (Check all that apply)</label>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="online_presence[]" value="facebook" id="facebook">
                                                    <label class="form-check-label" for="facebook">
                                                        <i class="bi bi-facebook text-primary"></i> Facebook Page
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="online_presence[]" value="instagram" id="instagram">
                                                    <label class="form-check-label" for="instagram">
                                                        <i class="bi bi-instagram text-danger"></i> Instagram Page
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="online_presence[]" value="google_business" id="google_business">
                                                    <label class="form-check-label" for="google_business">
                                                        <i class="bi bi-google text-warning"></i> Google My Business
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="online_presence[]" value="youtube" id="youtube">
                                                    <label class="form-check-label" for="youtube">
                                                        <i class="bi bi-youtube text-danger"></i> YouTube Channel
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="online_presence[]" value="website" id="has_website">
                                                    <label class="form-check-label" for="has_website">
                                                        <i class="bi bi-globe text-success"></i> Business Website
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="online_presence[]" value="none" id="none_yet">
                                                    <label class="form-check-label" for="none_yet">
                                                        None yet
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="website_type" class="form-label">Website Type Interest</label>
                                        <select class="form-select" id="website_type" name="website_type">
                                            <option value="">Select type...</option>
                                            <option value="portfolio">Portfolio website</option>
                                            <option value="business_info">Business informational website</option>
                                            <option value="ecommerce">Online store (eCommerce)</option>
                                            <option value="blog">Blog</option>
                                            <option value="not_sure">Not sure yet</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Consultation Goals -->
                            <div class="form-section">
                                <h4><i class="bi bi-target"></i> Consultation Goals</h4>
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label for="consultation_goals" class="form-label">Main Goals for Consultation <span class="required">*</span></label>
                                        <textarea class="form-control" id="consultation_goals" name="consultation_goals" rows="3" placeholder="e.g., increase sales, create a website, grow social media, brand awareness, etc." required></textarea>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="budget_range" class="form-label">Budget Range (Optional)</label>
                                        <select class="form-select" id="budget_range" name="budget_range">
                                            <option value="">Select range...</option>
                                            <option value="under_500">Under $500</option>
                                            <option value="500_1000">$500 - $1,000</option>
                                            <option value="1000_2500">$1,000 - $2,500</option>
                                            <option value="2500_5000">$2,500 - $5,000</option>
                                            <option value="5000_plus">$5,000+</option>
                                            <option value="discuss">Prefer to discuss</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Support Needs -->
                            <div class="form-section">
                                <h4><i class="bi bi-tools"></i> Support Needs</h4>
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Type of Support Needed (Check all that apply)</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="support_needs[]" value="website_development" id="web_dev">
                                                    <label class="form-check-label" for="web_dev">
                                                        <i class="bi bi-code-slash"></i> Website development
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="support_needs[]" value="branding_logo" id="branding">
                                                    <label class="form-check-label" for="branding">
                                                        <i class="bi bi-palette"></i> Branding & logo
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="support_needs[]" value="digital_marketing" id="marketing">
                                                    <label class="form-check-label" for="marketing">
                                                        <i class="bi bi-megaphone"></i> Digital marketing
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="support_needs[]" value="business_strategy" id="strategy">
                                                    <label class="form-check-label" for="strategy">
                                                        <i class="bi bi-graph-up"></i> Business strategy
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="support_needs[]" value="ads_setup" id="ads">
                                                    <label class="form-check-label" for="ads">
                                                        <i class="bi bi-bullseye"></i> Ads setup (Facebook/Instagram/Google)
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="support_needs[]" value="all_above" id="all_support">
                                                    <label class="form-check-label" for="all_support">
                                                        <i class="bi bi-check-all"></i> All of the above
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Schedule & Contact -->
                            <div class="form-section">
                                <h4><i class="bi bi-calendar-check"></i> Schedule & Contact</h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="preferred_datetime" class="form-label">Preferred Date/Time</label>
                                        <input type="datetime-local" class="form-control" id="preferred_datetime" name="preferred_datetime">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_method" class="form-label">Preferred Contact Method <span class="required">*</span></label>
                                        <select class="form-select" id="contact_method" name="contact_method" required>
                                            <option value="">Select method...</option>
                                            <option value="whatsapp">WhatsApp Call</option>
                                            <option value="zoom">Zoom/Google Meet</option>
                                            <option value="phone">Phone Call</option>
                                            <option value="email">Email only</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="additional_details" class="form-label">Additional Details</label>
                                        <textarea class="form-control" id="additional_details" name="additional_details" rows="3" placeholder="Any other information you'd like to share..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Consent -->
                            <div class="form-section">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="consent" id="consent" required>
                                    <label class="form-check-label" for="consent">
                                        <strong>I agree to be contacted by Your Company Name for the purpose of this consultation.</strong> <span class="required">*</span>
                                    </label>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="bi bi-send"></i> Submit Application
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle "None yet" checkbox - uncheck others when selected
        document.getElementById('none_yet').addEventListener('change', function() {
            if (this.checked) {
                const checkboxes = document.querySelectorAll('input[name="online_presence[]"]:not(#none_yet)');
                checkboxes.forEach(cb => cb.checked = false);
            }
        });

        // Handle other checkboxes - uncheck "None yet" when others are selected
        document.querySelectorAll('input[name="online_presence[]"]:not(#none_yet)').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    document.getElementById('none_yet').checked = false;
                }
            });
        });

        // Handle "All of the above" checkbox for support needs
        document.getElementById('all_support').addEventListener('change', function() {
            if (this.checked) {
                const checkboxes = document.querySelectorAll('input[name="support_needs[]"]:not(#all_support)');
                checkboxes.forEach(cb => cb.checked = false);
            }
        });

        // Handle other support checkboxes - uncheck "All of the above" when others are selected
        document.querySelectorAll('input[name="support_needs[]"]:not(#all_support)').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    document.getElementById('all_support').checked = false;
                }
            });
        });
    </script>
</body>
</html>