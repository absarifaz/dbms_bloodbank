<?php
require_once 'php/db.php';
requireLogin();

$donor_id = $_SESSION['donor_id'];
$donor = $conn->query("SELECT * FROM donors WHERE donor_id = $donor_id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log_donation'])) {
    $date = sanitize($conn, $_POST['donation_date']);
    $loc = sanitize($conn, $_POST['location']);
    $notes = sanitize($conn, $_POST['notes']);
    $stmt = $conn->prepare("INSERT INTO donations (donor_id, donation_date, location, notes) VALUES (?,?,?,?)");
    $stmt->bind_param("isss", $donor_id, $date, $loc, $notes);
    $stmt->execute();
    $conn->query("UPDATE donors SET last_donation_date = '$date', total_donations = total_donations + 1 WHERE donor_id = $donor_id");
    header("Location: dashboard.php?logged=1");
    exit;
}

$myRequests = $conn->query("SELECT * FROM blood_requests WHERE requester_id = $donor_id ORDER BY created_at DESC");
$myDonations = $conn->query("SELECT * FROM donations WHERE donor_id = $donor_id ORDER BY donation_date DESC");
$myResponses = $conn->query("SELECT r.*, br.patient_name, br.blood_group FROM responses r JOIN blood_requests br ON r.request_id = br.request_id WHERE r.donor_id = $donor_id ORDER BY r.responded_at DESC");

$matchingRequests = $conn->query("SELECT r.*, d.full_name AS requester_name FROM blood_requests r JOIN donors d ON r.requester_id = d.donor_id WHERE r.blood_group = '{$donor['blood_group']}' AND r.status = 'Open' AND r.requester_id != $donor_id ORDER BY FIELD(r.urgency,'Critical','Urgent','Normal'), r.created_at DESC");

$daysSince = $donor['last_donation_date'] ? floor((time() - strtotime($donor['last_donation_date'])) / 86400) : null;
$isEligible = $daysSince === null || $daysSince >= 90;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard — RAKTA.bd</title>
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
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="logout.php" class="btn btn-secondary" style="padding: 10px 20px;">Logout</a></li>
            </ul>
        </div>
    </nav>

    <section class="dashboard">
        <div class="container">
            <div class="dashboard-header">
                <span class="section-tag">/ YOUR PROFILE</span>
                <h1>Hello, <em><?php echo htmlspecialchars($donor['full_name']); ?></em>.</h1>
                <p>Here's your donation snapshot, requests, and matching emergency calls.</p>
            </div>

            <?php if (isset($_GET['responded'])): ?>
                <div class="alert alert-success" style="margin-bottom: 24px;">✓ Your response has been recorded. The requester will reach out shortly.</div>
            <?php endif; ?>
            <?php if (isset($_GET['logged'])): ?>
                <div class="alert alert-success" style="margin-bottom: 24px;">✓ Donation logged successfully. Thank you for saving lives!</div>
            <?php endif; ?>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="label">Blood Group</div>
                    <div class="value"><?php echo $donor['blood_group']; ?></div>
                    <div class="sub">Your registered type</div>
                </div>
                <div class="stat-card">
                    <div class="label">Total Donations</div>
                    <div class="value"><?php echo $donor['total_donations']; ?></div>
                    <div class="sub">Lives potentially saved: <?php echo $donor['total_donations'] * 3; ?></div>
                </div>
                <div class="stat-card">
                    <div class="label">Eligibility</div>
                    <div class="value" style="font-size: 28px; line-height: 1.2;">
                        <?php echo $isEligible ? 'READY' : 'WAIT'; ?>
                    </div>
                    <div class="sub">
                        <?php if ($daysSince === null): ?>
                            Never donated yet
                        <?php elseif ($isEligible): ?>
                            <?php echo $daysSince; ?> days since last
                        <?php else: ?>
                            <?php echo 90 - $daysSince; ?> days remaining
                        <?php endif; ?>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="label">My Responses</div>
                    <div class="value"><?php echo $myResponses->num_rows; ?></div>
                    <div class="sub">Calls answered</div>
                </div>
            </div>

            <div class="tabs">
                <button class="tab active" data-tab="matches">Matching Requests (<?php echo $matchingRequests->num_rows; ?>)</button>
                <button class="tab" data-tab="myreqs">My Requests</button>
                <button class="tab" data-tab="donations">Donation History</button>
                <button class="tab" data-tab="log">Log Donation</button>
            </div>

            <div class="tab-content" id="matches">
                <h2 style="margin-bottom: 16px; font-size: 28px;">Open requests for <em style="color: var(--crimson); font-style: italic;"><?php echo $donor['blood_group']; ?></em> blood</h2>
                <?php if ($matchingRequests->num_rows === 0): ?>
                    <div class="empty-state">
                        <h3>No matching requests right now</h3>
                        <p>Good news! No emergencies in your blood group. Stay registered, stay ready.</p>
                    </div>
                <?php else: ?>
                    <?php while ($r = $matchingRequests->fetch_assoc()): ?>
                        <div class="request-card">
                            <div class="urgency-bar <?php echo strtolower($r['urgency']); ?>"></div>
                            <div class="req-header">
                                <div>
                                    <div class="req-blood">
                                        <div class="req-blood-bubble"><?php echo $r['blood_group']; ?></div>
                                        <div>
                                            <h3><?php echo htmlspecialchars($r['patient_name']); ?></h3>
                                            <div style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--ink-soft);">
                                                REQ#<?php echo str_pad($r['request_id'], 4, '0', STR_PAD_LEFT); ?> · <?php echo strtoupper(htmlspecialchars($r['requester_name'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <span class="req-tag <?php echo strtolower($r['urgency']); ?>"><?php echo $r['urgency']; ?></span>
                            </div>
                            <div class="req-details">
                                <div><strong>Hospital</strong><?php echo htmlspecialchars($r['hospital_name']); ?></div>
                                <div><strong>Location</strong><?php echo htmlspecialchars($r['location']); ?></div>
                                <div><strong>Units</strong><?php echo $r['units_needed']; ?></div>
                                <div><strong>By</strong><?php echo $r['needed_by'] ? date('M j', strtotime($r['needed_by'])) : 'ASAP'; ?></div>
                            </div>
                            <div class="req-actions">
                                <a href="tel:<?php echo $r['contact_phone']; ?>" class="btn btn-primary">Call Now</a>
                                <a href="respond.php?req=<?php echo $r['request_id']; ?>" class="btn btn-secondary">Respond</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>

            <div class="tab-content" id="myreqs" style="display: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <h2 style="font-size: 28px;">Requests <em style="color: var(--crimson); font-style: italic;">you</em> posted</h2>
                    <a href="request.php" class="btn btn-primary">+ New Request</a>
                </div>
                <?php if ($myRequests->num_rows === 0): ?>
                    <div class="empty-state">
                        <h3>You haven't posted any requests</h3>
                        <p>If you ever need blood for a family member or friend, post a request here.</p>
                        <a href="request.php" class="btn btn-primary">Post First Request</a>
                    </div>
                <?php else: ?>
                    <?php while ($r = $myRequests->fetch_assoc()): ?>
                        <div class="request-card">
                            <div class="urgency-bar <?php echo strtolower($r['urgency']); ?>"></div>
                            <div class="req-header">
                                <div>
                                    <div class="req-blood">
                                        <div class="req-blood-bubble"><?php echo $r['blood_group']; ?></div>
                                        <div>
                                            <h3><?php echo htmlspecialchars($r['patient_name']); ?></h3>
                                            <div style="font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--ink-soft);">
                                                <?php echo date('M j, Y', strtotime($r['created_at'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 8px;">
                                    <span class="req-tag <?php echo strtolower($r['urgency']); ?>"><?php echo $r['urgency']; ?></span>
                                    <span class="req-tag <?php echo strtolower($r['status']); ?>"><?php echo $r['status']; ?></span>
                                </div>
                            </div>
                            <div class="req-details">
                                <div><strong>Hospital</strong><?php echo htmlspecialchars($r['hospital_name']); ?></div>
                                <div><strong>Units</strong><?php echo $r['units_needed']; ?></div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>

            <div class="tab-content" id="donations" style="display: none;">
                <h2 style="margin-bottom: 24px; font-size: 28px;">Your <em style="color: var(--crimson); font-style: italic;">donation</em> history</h2>
                <?php if ($myDonations->num_rows === 0): ?>
                    <div class="empty-state">
                        <h3>No donations logged yet</h3>
                        <p>Your first donation could save up to 3 lives.</p>
                    </div>
                <?php else: ?>
                    <?php while ($d = $myDonations->fetch_assoc()): ?>
                        <div class="donor-card">
                            <div class="donor-blood">★</div>
                            <div class="donor-info">
                                <h3><?php echo date('F j, Y', strtotime($d['donation_date'])); ?></h3>
                                <div class="meta">DONATION#<?php echo str_pad($d['donation_id'], 4, '0', STR_PAD_LEFT); ?></div>
                                <div class="details">
                                    ◉ <?php echo htmlspecialchars($d['location'] ?: 'Location not specified'); ?>
                                    <?php if ($d['notes']): ?><br><?php echo htmlspecialchars($d['notes']); ?><?php endif; ?>
                                </div>
                            </div>
                            <span class="eligibility eligible">COMPLETED</span>
                            <div></div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>

            <div class="tab-content" id="log" style="display: none;">
                <h2 style="margin-bottom: 24px; font-size: 28px;">Log a <em style="color: var(--crimson); font-style: italic;">new</em> donation</h2>
                <div style="max-width: 600px;">
                    <div class="form-card">
                        <h2>Add Donation Record</h2>
                        <p class="form-sub">Update your record after donating. This helps the system track your eligibility.</p>
                        <form method="POST">
                            <div class="form-group">
                                <label>Donation Date</label>
                                <input type="date" name="donation_date" required max="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="form-group">
                                <label>Location / Blood Bank</label>
                                <input type="text" name="location" placeholder="e.g. Red Crescent Society">
                            </div>
                            <div class="form-group">
                                <label>Notes</label>
                                <textarea name="notes" rows="3" placeholder="Optional notes..."></textarea>
                            </div>
                            <button type="submit" name="log_donation" class="btn btn-primary">Log Donation</button>
                        </form>
                    </div>
                </div>
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

    <script>
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
                tab.classList.add('active');
                document.getElementById(tab.dataset.tab).style.display = 'block';
            });
        });
    </script>
</body>
</html>
