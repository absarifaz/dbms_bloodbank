<?php require_once 'php/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAKTA — Campus Blood Donation Network</title>
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
                <li><a href="requests.php">Active Requests</a></li>
                <li><a href="about.php">About</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php" class="btn btn-secondary" style="padding: 10px 20px;">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php" class="btn btn-primary" style="padding: 10px 20px;">Become a Donor</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <section class="hero">
        <div class="container">
            <div class="hero-grid">
                <div>
                    <div class="hero-tag">LIVE — 247 DONORS ACTIVE NOW</div>
                    <h1>
                        Every drop <em>writes</em><br>
                        a <span class="accent">second</span><br>
                        chapter.
                    </h1>
                    <p class="hero-subtitle">
                        A campus-rooted network connecting students across Bangladesh in moments that matter most. Register your blood group, respond to emergencies, save a life before sundown.
                    </p>
                    <div class="hero-actions">
                        <a href="register.php" class="btn btn-primary">Register as Donor</a>
                        <a href="request.php" class="btn btn-secondary">Request Blood</a>
                    </div>
                    <div class="hero-stats">
                        <?php
                        $totalDonors = $conn->query("SELECT COUNT(*) AS c FROM donors")->fetch_assoc()['c'];
                        $totalDonations = $conn->query("SELECT COALESCE(SUM(total_donations),0) AS c FROM donors")->fetch_assoc()['c'];
                        $openRequests = $conn->query("SELECT COUNT(*) AS c FROM blood_requests WHERE status='Open'")->fetch_assoc()['c'];
                        ?>
                        <div>
                            <div class="stat-num"><?php echo $totalDonors; ?></div>
                            <div class="stat-label">Registered Donors</div>
                        </div>
                        <div>
                            <div class="stat-num"><?php echo $totalDonations; ?>+</div>
                            <div class="stat-label">Lives Touched</div>
                        </div>
                        <div>
                            <div class="stat-num"><?php echo $openRequests; ?></div>
                            <div class="stat-label">Open Now</div>
                        </div>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="blood-card blood-card-1">
                        <div class="label">Donor / Active</div>
                        <div class="group">O+</div>
                        <div class="info">
                            Rahim Ahmed<br>
                            Computer Science<br>
                            Dhaka University
                        </div>
                        <div class="meta">→ DONATED 5×</div>
                    </div>
                    <div class="blood-card blood-card-2">
                        <div class="label">Donor / Universal</div>
                        <div class="group">O−</div>
                        <div class="info">
                            Tanvir Hasan<br>
                            Mechanical Eng.<br>
                            BUET Campus
                        </div>
                        <div class="meta">→ ELIGIBLE TODAY</div>
                    </div>
                    <div class="blood-card blood-card-3">
                        <div class="label">Request / Critical</div>
                        <div class="group">B+</div>
                        <div class="info">
                            Square Hospital<br>
                            2 units required<br>
                            Cancer transfusion
                        </div>
                        <div class="meta">→ RESPOND NOW</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="process-strip">
        <div class="marquee">
            <span>Save a life ✦ Donate today ✦ Be the reason someone smiles tomorrow ✦ One pint, three lives ✦ Save a life ✦ Donate today ✦ Be the reason someone smiles tomorrow ✦ One pint, three lives</span>
            <span>Save a life ✦ Donate today ✦ Be the reason someone smiles tomorrow ✦ One pint, three lives ✦ Save a life ✦ Donate today ✦ Be the reason someone smiles tomorrow ✦ One pint, three lives</span>
        </div>
    </div>

    <section class="section section-cream">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">/ HOW IT WORKS</span>
                <h2>Three steps. <em>Zero</em> friction.</h2>
                <p style="font-size: 17px; color: var(--ink-soft); max-width: 600px;">
                    From registration to response, the system is engineered for speed — because emergencies don't wait for paperwork.
                </p>
            </div>
            <div class="features-grid">
                <div class="feature">
                    <div class="feature-num">01</div>
                    <div class="feature-icon">◉</div>
                    <h3>Register Once</h3>
                    <p>Sign up with your campus email, blood group, and contact details. Your profile becomes searchable to those in need within seconds.</p>
                </div>
                <div class="feature">
                    <div class="feature-num">02</div>
                    <div class="feature-icon">◈</div>
                    <h3>Find Instantly</h3>
                    <p>Smart filters surface donors by blood group, campus, and the critical 90-day eligibility window — no manual phone-tree required.</p>
                </div>
                <div class="feature">
                    <div class="feature-num">03</div>
                    <div class="feature-icon">◆</div>
                    <h3>Respond Fast</h3>
                    <p>Open requests are visible across the network. Accept, coordinate, donate. Every response is logged and celebrated.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">/ THE PROBLEM</span>
                <h2>In Bangladesh, finding blood is <em>still</em> a phone call away from too late.</h2>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 64px; align-items: center;">
                <div>
                    <p style="font-size: 18px; line-height: 1.7; margin-bottom: 24px; color: var(--ink-soft);">
                        Hospitals across our country face a daily shortage of safe, timely blood. Families end up posting on Facebook groups, calling friends-of-friends at 2AM, or worse — losing time they don't have.
                    </p>
                    <p style="font-size: 18px; line-height: 1.7; color: var(--ink-soft);">
                        University campuses are full of healthy, willing donors. We just lacked the wiring. <strong style="color: var(--ink);">RAKTA is that wiring.</strong>
                    </p>
                </div>
                <div style="background: var(--ink); color: var(--cream); padding: 48px; border: 2px solid var(--ink); position: relative;">
                    <div style="font-family: 'Fraunces', serif; font-size: 96px; line-height: 1; color: var(--crimson-bright); margin-bottom: 12px;">"</div>
                    <p style="font-family: 'Fraunces', serif; font-style: italic; font-size: 22px; line-height: 1.5; margin-bottom: 24px;">
                        Three out of every ten emergency surgeries in Bangladesh are delayed due to blood unavailability — most within walking distance of a campus.
                    </p>
                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 12px; letter-spacing: 0.1em; opacity: 0.7;">
                        — BANGLADESH RED CRESCENT, 2024
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <span class="section-tag" style="color: var(--crimson-bright);">/ JOIN THE NETWORK</span>
                <h2>Be the call <em>they</em> were hoping for.</h2>
                <p>It takes ninety seconds to register. The next emergency doesn't get to choose its hour — but you can be ready.</p>
                <div class="hero-actions" style="justify-content: center;">
                    <a href="register.php" class="btn btn-primary" style="background: var(--crimson-bright); border-color: var(--crimson-bright); box-shadow: 4px 4px 0 var(--cream);">Become a Donor</a>
                    <a href="donors.php" class="btn" style="color: var(--cream); border: 2px solid var(--cream);">Browse Donors</a>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="index.php" class="logo">
                        <div class="logo-mark"></div>
                        <div class="logo-text">RAKTA<span>.bd</span></div>
                    </a>
                    <p>A campus-based blood donation network rebuilding the way emergency blood is found in Bangladesh — one student, one pledge, one life at a time.</p>
                </div>
                <div class="footer-col">
                    <h4>/ Network</h4>
                    <ul>
                        <li><a href="donors.php">Find Donors</a></li>
                        <li><a href="requests.php">View Requests</a></li>
                        <li><a href="register.php">Register</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>/ Resources</h4>
                    <ul>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="#">Eligibility Guide</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>/ Contact</h4>
                    <ul>
                        <li><a href="mailto:hello@rakta.bd">hello@rakta.bd</a></li>
                        <li><a href="tel:+8801700000000">+880 1700 000000</a></li>
                        <li>Dhaka, Bangladesh</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <div>© 2026 RAKTA.BD — DBMS PROJECT</div>
                <div>BUILT WITH ♥ FOR BANGLADESH</div>
            </div>
        </div>
    </footer>
</body>
</html>
