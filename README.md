# spsf-kiit-symposium-website
# Sun Pharma Science Foundation Symposium 2025 Website

This repository contains the full source code for the official website of the **"Autoimmunity and Autoimmune Diseases"** symposium. This is a collaborative event by the Sun Pharma Science Foundation (SPSF) and KIIT Deemed to be University, scheduled for November 15th, 2025, in Bhubaneswar.

## Live Demo
https://spsf-kiit-symposium-website.vercel.app/

## Actual product(Please don't interact in it's form)
https://spsf-symposium2025.org/

## 🌟 Features

-   **Fully Responsive Design:** A single-page layout that works seamlessly on desktop, tablet, and mobile devices.
-   **Dynamic Content Sections:** Includes sections for Home, About, Program Schedule, Invited Speakers, and Contact Information.
-   **Online Registration System:** A secure registration form that captures participant details.
-   **Registration Limit:** The system automatically closes online registration when the 250-participant limit is reached and displays a "limit reached" message.
-   **Live Form Reopening:** The registration form automatically reappears on the live site if a spot opens up (e.g., after an admin deletes an entry), checking every 30 seconds.
-   **Automated Confirmation Emails:** Sends a confirmation email to each participant with a unique Registration ID (e.g., `SPSF0001`).
-   **Secure Admin Panel:** A password-protected dashboard for administrators to manage the event.
-   **Admin-Only Functionality:**
    -   View all registered participants in a clean, filterable table.
    -   Delete participant entries.
    -   Export the complete registration list to a CSV file (compatible with Excel).

## 💻 Technology Stack

-   **Frontend:** HTML5, Tailwind CSS, JavaScript (ES6+)
-   **Backend:** PHP 8+
-   **Database:** MySQL
-   **Email:** PHPMailer Library (for reliable SMTP email sending)

## 🚀 Setup and Installation

Follow these steps to set up the project on a cPanel or similar hosting environment.

### 1. Prerequisites
- A web server with PHP support (e.g., Apache).
- A MySQL database.
- SMTP credentials for sending emails (e.g., a Gmail App Password or a cPanel email account).

### 2. Database Setup
1.  In your cPanel, create a new MySQL database and a database user. Assign the user to the database with all privileges.
2.  Open **phpMyAdmin**, select your new database, and run the following query in the **SQL** tab to create the `registrations` table:

```sql
CREATE TABLE `registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reg_id` varchar(10) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `affiliation` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `special_requirements` text DEFAULT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `reg_id` (`reg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
