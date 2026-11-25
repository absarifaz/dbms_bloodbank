<?php require_once 'php/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About — RAKTA.bd</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav>
        <div class="container">
            <a href="index.php" class="logo">
                <div class="logo-mark"></div>
                <div class="logo-text">RAKTA<span>.bd</span></div>
            </a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="donors.php">Find Donors</a></li>
                <li><a href="requests.php">Requests</a></li>
                <li><a href="about.php">About</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                <?php else: ?>
                    <li><a href="register.php" class="btn btn-primary" style="padding: 10px 20px;">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <section class="hero" style="padding: 80px 0;">
        <div class="container">
            <span class="section-tag">/ ABOUT THE PROJECT</span>
            <h1 style="font-size: clamp(48px, 7vw, 92px); max-width: 900px; margin-bottom: 32px;">A DBMS project, but really a <em style="color: var(--crimson); font-style: italic;">love letter</em> to the campus.</h1>
            <p style="font-size: 19px; color: var(--ink-soft); max-width: 720px;">RAKTA is a campus-based blood donation network designed for Bangladeshi universities. It is built around four interconnected database tables and a simple promise: when emergencies hit, finding blood should not be the hardest part.</p>
        </div>
    </section>

    <section class="section section-cream">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">/ DATABASE SCHEMA</span>
                <h2>How the <em>data</em> is wired.</h2>
            </div>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;">
                <div class="feature">
                    <div class="feature-num">TABLE</div>
                    <h3 style="font-family: 'JetBrains Mono', monospace; font-size: 20px;">donors</h3>
                    <p style="margin-top: 12px; font-family: 'JetBrains Mono', monospace; font-size: 13px; line-height: 1.8;">
                        donor_id · student_id · full_name<br>
                        email · password · phone · blood_group<br>
                        department · campus · address<br>
                        last_donation_date · is_available<br>
                        total_donations · registered_at
                    </p>
                </div>
                <div class="feature">
                    <div class="feature-num">TABLE</div>
                    <h3 style="font-family: 'JetBrains Mono', monospace; font-size: 20px;">blood_requests</h3>
                    <p style="margin-top: 12px; font-family: 'JetBrains Mono', monospace; font-size: 13px; line-height: 1.8;">
                        request_id · requester_id (FK)<br>
                        patient_name · blood_group · units_needed<br>
                        hospital_name · location · contact_phone<br>
                        urgency · status · notes<br>
                        needed_by · created_at
                    </p>
                </div>
                <div class="feature">
                    <div class="feature-num">TABLE</div>
                    <h3 style="font-family: 'JetBrains Mono', monospace; font-size: 20px;">donations</h3>
                    <p style="margin-top: 12px; font-family: 'JetBrains Mono', monospace; font-size: 13px; line-height: 1.8;">
                        donation_id · donor_id (FK)<br>
                        request_id (FK, nullable)<br>
                        donation_date · location · notes
                    </p>
                </div>
                <div class="feature">
                    <div class="feature-num">TABLE</div>
                    <h3 style="font-family: 'JetBrains Mono', monospace; font-size: 20px;">responses</h3>
                    <p style="margin-top: 12px; font-family: 'JetBrains Mono', monospace; font-size: 13px; line-height: 1.8;">
                        response_id · request_id (FK)<br>
                        donor_id (FK) · message<br>
                        status · responded_at
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">/ KEY DBMS FEATURES</span>
                <h2>Real database <em>thinking</em>.</h2>
            </div>
            <div class="features-grid">
                <div class="feature">
                    <div class="feature-num">VIEW</div>
                    <h3>eligible_donors</h3>
                    <p>A computed VIEW that calculates eligibility using DATEDIFF on last_donation_date — donors who have given in the last 90 days are flagged automatically. No business logic required at query time.</p>
                </div>
                <div class="feature">
                    <div class="feature-num">FK</div>
                    <h3>Foreign Keys</h3>
                    <p>Every relationship is enforced — requests link to donors, donations link to both, responses cascade. ON DELETE rules ensure referential integrity across the entire schema.</p>
                </div>
                <div class="feature">
                    <div class="feature-num">IDX</div>
                    <h3>Indexed Columns</h3>
                    <p>Indexes on blood_group, campus, last_donation_date, and request status — every common filter your users perform hits an index, not a table scan.</p>
                </div>
                <div class="feature">
                    <div class="feature-num">ENUM</div>
                    <h3>Constrained Types</h3>
                    <p>Blood groups, urgency levels, and request statuses are MySQL ENUMs — the database itself rejects invalid values, no app-layer validation alone.</p>
                </div>
                <div class="feature">
                    <div class="feature-num">JOIN</div>
                    <h3>Multi-Table Joins</h3>
                    <p>The dashboard queries combine donors, requests, donations, and responses with proper JOINs — showing matching emergencies, donation history, and active responses in one cohesive view.</p>
                </div>
                <div class="feature">
                    <div class="feature-num">LIVE</div>
                    <h3>Eligibility Filtering</h3>
                    <p>Filter donors by the 90-day rule directly in SQL — DATEDIFF(CURDATE(), last_donation_date) >= 90 — surfacing only donors safe to call upon.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Built for <em>Bangladesh</em>.</h2>
                <p>This is a student project. But the problem is real, and the solution can be too.</p>
                <a href="register.php" class="btn btn-primary" style="background: var(--crimson-bright); border-color: var(--crimson-bright); box-shadow: 4px 4px 0 var(--cream);">Join the Network</a>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-bottom">
                <div>© 2026 RAKTA.BD — DBMS PROJECT</div>
                <div>BUILT WITH ♥ FOR BANGLADESH</div>
            </div>
        </div>
    </footer>
</body>
</html>
