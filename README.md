# 💼 Business Consultation Application System

A **fully functional consultation request management system** built with **PHP, MySQL**, and **Bootstrap 5**, designed to help digital service providers collect, track, and manage business consultation applications professionally.

---

## 🚀 Live Demo (Optional)

👉 [View the Form](http://localhost/form/index.php)  
👉 [Admin Panel](http://localhost/form/admin.php) (Password: `admin123`)

---

## ✨ Key Features

- ✅ **Modern Design**: Clean UI with Bootstrap 5 styling, gradient header, and icons
- ✅ **Mobile Responsive**: Works seamlessly across devices
- ✅ **Full Form Structure**: Basic Info, Business Details, Online Presence, Goals, Preferences
- ✅ **Smart UX**: Interactive checkboxes (e.g., unchecking when "None yet" is selected)
- ✅ **Form Validation**: Client-side (HTML5) + server-side validation
- ✅ **MySQL Integration**: Securely stores all form data using prepared statements
- ✅ **Admin Panel**:
  - View all submissions
  - Track status: `New → Contacted → Scheduled → Completed`
  - Modal popup to view full applicant data
  - Password-protected area
- ✅ **Secure**: Basic XSS prevention and input sanitization
- ✅ **Extendable**: Easy to export as PDF/CSV, send email notifications, or add analytics

---

## 📁 File Structure

your-website/
├── index.php                # Main public consultation form
├── admin.php                # Admin dashboard to manage applications
├── config.php               # Database connection
├── database.sql             # SQL schema to import


---

## 🧰 Tech Stack

- PHP 8+
- MySQL
- HTML5
- CSS3 (Bootstrap 5)
- JavaScript
- Font Awesome (Icons)

---

## 🛠️ Setup Instructions

### 1. Clone the Project

```bash
git clone https://github.com/suvithan-lk/Business-Consultation-Application.git
cd business-consultation-app
2. Set Up the Database
Import the SQL schema using phpMyAdmin or terminal:

bash
mysql -u your_username -p < database.sql
DB Name: consultation_app (or your choice)

3. Configure the Database
Edit config.php with your database credentials:

php
$host = "localhost";
$dbname = "consultation_app";
$username = "root";
$password = "";
🔐 Admin Access
File: admin.php

# Default Password: admin123

You can change the password directly in admin.php (line 11)

📸 Screenshots (Optional)
Add UI screenshots of the consultation form and admin panel here.

🔄 Future Features (Ideas)
Email notifications after form submission

Export as CSV or PDF

Admin login system (session-based)

File uploads (e.g., logo, documents)

Google reCAPTCHA integration

👤 Author
Anantharasa Suvithan
Founder, DigitXCode Pvt Ltd
🌐 suvithan.com
📧 hello@suvithan.com

📃 License
This project is open-source and available under the MIT License.

✅ This system is production-ready and includes everything you need to start collecting and managing consultation applications immediately!

---
