<?php
require_once 'php/db.php';

$bloodFilter = isset($_GET['blood_group']) ? sanitize($conn, $_GET['blood_group']) : '';
$campusFilter = isset($_GET['campus']) ? sanitize($conn, $_GET['campus']) : '';
$eligibleOnly = isset($_GET['eligible']) && $_GET['eligible'] === '1';

$sql = "SELECT * FROM eligible_donors WHERE 1=1";
if ($bloodFilter) $sql .= " AND blood_group = '$bloodFilter'";
if ($campusFilter) $sql .= " AND campus LIKE '%$campusFilter%'";
if ($eligibleOnly) $sql .= " AND eligibility_status IN ('Eligible','Never Donated')";
$sql .= " ORDER BY total_donations DESC, full_name ASC";

$donors = $conn->query($sql);

$campuses = $conn->query("SELECT DISTINCT campus FROM donors ORDER BY campus");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Find Donors — RAKTA.bd</title>
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
                <?php if (isLoggedIn()): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php" class="btn btn-secondary" style="padding: 10px 20px;">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php" class="btn btn-primary" style="padding: 10px 20px;">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <section class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <span class="section-tag">/ DONOR DIRECTORY</span>
                <h1>Find a <em>donor</em> nearby.</h1>
                <p>Search by blood group, campus, and the critical 90-day eligibility window. Reach out directly to coordinate.</p>
            </div>

            <form method="GET" class="filter-bar">
                <div class="form-group" style="margin: 0;">
                    <label>Blood Group</label>
                    <select name="blood_group">
                        <option value="">All Groups</option>
                        <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $g): ?>
                            <option value="<?php echo $g; ?>" <?php if ($bloodFilter === $g) echo 'selected'; ?>><?php echo $g; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="margin: 0;">
                    <label>Campus</label>
                    <select name="campus">
                        <option value="">All Campuses</option>
                        <?php while ($c = $campuses->fetch_assoc()): ?>
                            <option value="<?php echo $c['campus']; ?>" <?php if ($campusFilter === $c['campus']) echo 'selected'; ?>><?php echo $c['campus']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group" style="margin: 0;">
                    <label>Eligibility</label>
                    <select name="eligible">
                        <option value="0">All Donors</option>
                        <option value="1" <?php if ($eligibleOnly) echo 'selected'; ?>>Eligible Only (90+ days)</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Apply Filters</button>
                </div>
            </form>

            <div style="margin-bottom: 24px; font-family: 'JetBrains Mono', monospace; font-size: 13px; color: var(--ink-soft);">
                ▸ FOUND <?php echo $donors->num_rows; ?> DONOR<?php echo $donors->num_rows !== 1 ? 'S' : ''; ?> MATCHING YOUR FILTERS
            </div>

            <?php if ($donors->num_rows === 0): ?>
                <div class="empty-state">
                    <h3>No donors match your filters</h3>
                    <p>Try widening your search or adjusting the eligibility criteria.</p>
                    <a href="donors.php" class="btn btn-primary">Reset Filters</a>
                </div>
            <?php else: ?>
                <?php while ($d = $donors->fetch_assoc()): ?>
                    <div class="donor-card">
                        <div class="donor-blood"><?php echo $d['blood_group']; ?></div>
                        <div class="donor-info">
                            <h3><?php echo htmlspecialchars($d['full_name']); ?></h3>
                            <div class="meta"><?php echo $d['student_id']; ?> · <?php echo $d['department']; ?></div>
                            <div class="details">
                                ◉ <?php echo $d['campus']; ?><br>
                                ◐ Total donations: <strong><?php echo $d['total_donations']; ?></strong>
                                <?php if ($d['last_donation_date']): ?>
                                    · Last: <?php echo date('M j, Y', strtotime($d['last_donation_date'])); ?>
                                    (<?php echo $d['days_since_donation']; ?> days ago)
                                <?php else: ?>
                                    · No previous donations recorded
                                <?php endif; ?>
                            </div>
                        </div>
                        <div>
                            <?php
                            $eligClass = strtolower(str_replace(' ', '-', $d['eligibility_status']));
                            ?>
                            <span class="eligibility <?php echo $eligClass === 'never-donated' ? 'never' : ($eligClass === 'eligible' ? 'eligible' : 'not-eligible'); ?>">
                                <?php echo $d['eligibility_status']; ?>
                            </span>
                        </div>
                        <div>
                            <?php if (isLoggedIn()): ?>
                                <a href="tel:<?php echo $d['phone']; ?>" class="btn btn-primary" style="padding: 12px 20px;">Call</a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-secondary" style="padding: 12px 20px;">Login to Contact</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
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
