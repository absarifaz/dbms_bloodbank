<?php
require_once 'php/db.php';
requireLogin();

$req_id = isset($_GET['req']) ? (int)$_GET['req'] : 0;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $req_id = (int)$_POST['request_id'];
    $msg = sanitize($conn, $_POST['message']);
    $donor_id = $_SESSION['donor_id'];

    $stmt = $conn->prepare("INSERT INTO responses (request_id, donor_id, message) VALUES (?,?,?)");
    $stmt->bind_param("iis", $req_id, $donor_id, $msg);
    $stmt->execute();

    header("Location: dashboard.php?responded=1");
    exit;
}

$req = $conn->query("SELECT r.*, d.full_name FROM blood_requests r JOIN donors d ON r.requester_id = d.donor_id WHERE r.request_id = $req_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Respond — RAKTA.bd</title>
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
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="logout.php" class="btn btn-secondary" style="padding: 10px 20px;">Logout</a></li>
            </ul>
        </div>
    </nav>

    <section class="form-section">
        <div class="container">
            <div class="form-grid">
                <div class="form-side">
                    <span class="section-tag">/ RESPOND</span>
                    <h1>Step <em>forward</em>.</h1>
                    <p>Confirm your willingness to donate. The requester will be notified immediately and will reach out to coordinate.</p>
                    <?php if ($req): ?>
                    <div style="margin-top: 32px; padding: 24px; background: var(--cream-warm); border: 2px solid var(--ink);">
                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: var(--ink-soft); letter-spacing: 0.1em; margin-bottom: 8px;">REQUEST DETAILS</div>
                        <div style="font-size: 22px; font-family: 'Fraunces', serif; font-weight: 800;">
                            <?php echo $req['blood_group']; ?> · <?php echo htmlspecialchars($req['patient_name']); ?>
                        </div>
                        <div style="margin-top: 8px; color: var(--ink-soft);">
                            <?php echo $req['hospital_name']; ?>, <?php echo $req['location']; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="form-card">
                    <h2>Confirm Response</h2>
                    <p class="form-sub">Add a brief message about your availability.</p>

                    <form method="POST">
                        <input type="hidden" name="request_id" value="<?php echo $req_id; ?>">
                        <div class="form-group">
                            <label>Your Message</label>
                            <textarea name="message" rows="5" placeholder="e.g. I am available, can reach the hospital in 30 minutes..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Response</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
