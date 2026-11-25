<?php
require_once 'php/db.php';

$statusFilter = isset($_GET['status']) ? sanitize($conn, $_GET['status']) : 'Open';
$bloodFilter = isset($_GET['blood_group']) ? sanitize($conn, $_GET['blood_group']) : '';

$sql = "SELECT r.*, d.full_name AS requester_name FROM blood_requests r 
        JOIN donors d ON r.requester_id = d.donor_id WHERE 1=1";
if ($statusFilter && $statusFilter !== 'All') $sql .= " AND r.status = '$statusFilter'";
if ($bloodFilter) $sql .= " AND r.blood_group = '$bloodFilter'";
$sql .= " ORDER BY FIELD(r.urgency,'Critical','Urgent','Normal'), r.created_at DESC";

$requests = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Active Requests — RAKTA.bd</title>
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
                <span class="section-tag">/ ACTIVE EMERGENCIES</span>
                <h1>Open <em>requests</em> right now.</h1>
                <p>People needing blood across the network. Sorted by urgency. Respond if you can.</p>
            </div>

            <?php if (isLoggedIn()): ?>
            <div style="margin-bottom: 32px;">
                <a href="request.php" class="btn btn-primary">+ Post a New Request</a>
            </div>
            <?php endif; ?>

            <form method="GET" class="filter-bar">
                <div class="form-group" style="margin: 0;">
                    <label>Status</label>
                    <select name="status">
                        <option value="All" <?php if ($statusFilter === 'All') echo 'selected'; ?>>All Requests</option>
                        <option value="Open" <?php if ($statusFilter === 'Open') echo 'selected'; ?>>Open</option>
                        <option value="Fulfilled" <?php if ($statusFilter === 'Fulfilled') echo 'selected'; ?>>Fulfilled</option>
                        <option value="Closed" <?php if ($statusFilter === 'Closed') echo 'selected'; ?>>Closed</option>
                    </select>
                </div>
                <div class="form-group" style="margin: 0;">
                    <label>Blood Group</label>
                    <select name="blood_group">
                        <option value="">All Groups</option>
                        <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $g): ?>
                            <option value="<?php echo $g; ?>" <?php if ($bloodFilter === $g) echo 'selected'; ?>><?php echo $g; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Apply Filters</button>
                </div>
            </form>

            <div style="margin-bottom: 24px; font-family: 'JetBrains Mono', monospace; font-size: 13px; color: var(--ink-soft);">
                ▸ <?php echo $requests->num_rows; ?> REQUEST<?php echo $requests->num_rows !== 1 ? 'S' : ''; ?> FOUND
            </div>

            <?php if ($requests->num_rows === 0): ?>
                <div class="empty-state">
                    <h3>No requests found</h3>
                    <p>That's actually good news. No emergencies in this category right now.</p>
                </div>
            <?php else: ?>
                <?php while ($r = $requests->fetch_assoc()): ?>
                    <div class="request-card">
                        <div class="urgency-bar <?php echo strtolower($r['urgency']); ?>"></div>
                        <div class="req-header">
                            <div>
                                <div class="req-blood">
                                    <div class="req-blood-bubble"><?php echo $r['blood_group']; ?></div>
                                    <div>
                                        <h3><?php echo htmlspecialchars($r['patient_name']); ?></h3>
                                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--ink-soft);">
                                            REQ#<?php echo str_pad($r['request_id'], 4, '0', STR_PAD_LEFT); ?> · POSTED BY <?php echo strtoupper(htmlspecialchars($r['requester_name'])); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <span class="req-tag <?php echo strtolower($r['urgency']); ?>"><?php echo $r['urgency']; ?></span>
                                <span class="req-tag <?php echo strtolower($r['status']); ?>"><?php echo $r['status']; ?></span>
                            </div>
                        </div>

                        <div class="req-details">
                            <div>
                                <strong>Hospital</strong>
                                <?php echo htmlspecialchars($r['hospital_name']); ?>
                            </div>
                            <div>
                                <strong>Location</strong>
                                <?php echo htmlspecialchars($r['location']); ?>
                            </div>
                            <div>
                                <strong>Units Needed</strong>
                                <?php echo $r['units_needed']; ?> unit<?php echo $r['units_needed'] > 1 ? 's' : ''; ?>
                            </div>
                            <div>
                                <strong>Needed By</strong>
                                <?php echo $r['needed_by'] ? date('M j, Y', strtotime($r['needed_by'])) : 'ASAP'; ?>
                            </div>
                            <?php if ($r['notes']): ?>
                            <div style="grid-column: 1 / -1;">
                                <strong>Notes</strong>
                                <?php echo htmlspecialchars($r['notes']); ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($r['status'] === 'Open'): ?>
                        <div class="req-actions">
                            <a href="tel:<?php echo $r['contact_phone']; ?>" class="btn btn-primary">Call <?php echo $r['contact_phone']; ?></a>
                            <?php if (isLoggedIn()): ?>
                                <a href="respond.php?req=<?php echo $r['request_id']; ?>" class="btn btn-secondary">I Can Donate</a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-secondary">Login to Respond</a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
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
