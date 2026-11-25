# RAKTA.bd — Campus Blood Donation Network

A complete DBMS project: **Blood Donation Network for Bangladeshi University Campuses**, built with **PHP + MySQL** and an editorial-style frontend.

---

## Project Idea

Emergency blood is hard to find quickly in Bangladesh. RAKTA solves this by letting students:
- Register their blood group on a campus-based platform
- Post emergency blood requests
- Find nearby eligible donors instantly
- Filter donors using the medical 90-day rule (donors must wait 90+ days between donations)

---

## Tech Stack

- **Backend**: PHP 7.4+ (uses `mysqli` with prepared statements)
- **Database**: MySQL 5.7+ / MariaDB
- **Frontend**: HTML5, CSS3 (custom design — Fraunces + Inter + JetBrains Mono fonts via Google Fonts), vanilla JavaScript
- **Server**: Any PHP-capable server (XAMPP, WAMP, MAMP, LAMP)

---

## Setup Instructions

### 1. Install XAMPP (or any PHP + MySQL stack)

Download XAMPP from https://www.apachefriends.org/ and install it. Start **Apache** and **MySQL** from the XAMPP control panel.

### 2. Place project files

Copy the entire `blood_donation` folder to your XAMPP `htdocs` directory:
```
C:\xampp\htdocs\blood_donation\
```

### 3. Create the database

Open phpMyAdmin: http://localhost/phpmyadmin

Click **Import** → choose `sql/database.sql` → click **Go**.

This will:
- Create the `blood_donation_db` database
- Create 4 tables: `donors`, `blood_requests`, `donations`, `responses`
- Create the `eligible_donors` VIEW
- Insert 12 sample donors and 5 sample requests

### 4. Configure database connection (if needed)

Open `php/db.php` and update credentials if your MySQL setup is different:
```php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'blood_donation_db';
```

### 5. Open the website

Visit: **http://localhost/blood_donation/**

### 6. Test login

Use demo credentials:
- **Email**: any seeded email (e.g. `rahim@university.edu.bd`)
- **Password**: `demo123`

Or register a new account from the Register page.

---

## Database Schema

### Tables

**donors** — student profiles with blood group, last donation, eligibility tracking
**blood_requests** — emergency requests with urgency, status, hospital info
**donations** — historical record of all donations
**responses** — donors responding to specific requests

### View

**eligible_donors** — automatically computes eligibility status:
- `Never Donated` (no record yet)
- `Eligible` (90+ days since last donation)
- `Not Eligible` (donated within last 90 days)

### Indexes

For fast filtering: `blood_group`, `campus`, `last_donation_date`, `request_status`, `request_blood`

### Foreign Keys

All cross-table references use `FOREIGN KEY` with `ON DELETE CASCADE` / `SET NULL` for proper referential integrity.

---

## Pages

| Page | File | Purpose |
|------|------|---------|
| Home | `index.php` | Hero + stats + how it works |
| Register | `register.php` | New donor signup |
| Login | `login.php` | Donor login |
| Find Donors | `donors.php` | Search/filter donors with eligibility |
| Requests | `requests.php` | View all blood requests, filterable |
| Post Request | `request.php` | Create a new emergency request |
| Respond | `respond.php` | Respond to a specific request |
| Dashboard | `dashboard.php` | Personal overview with matching requests, history, donation logging |
| About | `about.php` | DBMS schema explainer |

---

## Project Highlights for DBMS Submission

1. **4 normalized tables** with proper relationships
2. **VIEW** for computed eligibility (`eligible_donors`)
3. **ENUM constraints** for data integrity (blood groups, urgency, status)
4. **Indexes** on all common filter columns
5. **Foreign keys** with cascade rules
6. **Prepared statements** preventing SQL injection
7. **Multi-table JOINs** in dashboard queries
8. **Aggregate queries** (COUNT, SUM) for live stats on the homepage
9. **Date arithmetic** (`DATEDIFF`) for the 90-day eligibility rule

---

Built with ♥ for Bangladesh.
