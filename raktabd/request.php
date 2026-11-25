<?php
require_once 'php/db.php';
requireLogin();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_name = sanitize($conn, $_POST['patient_name']);
    $blood_group = sanitize($conn, $_POST['blood_group']);
    $units_needed = (int)$_POST['units_needed'];
    $hospital_name = sanitize($conn, $_POST['hospital_name']);
    $location = sanitize($conn, $_POST['location']);
    $contact_phone = sanitize($conn, $_POST['contact_phone']);
    $urgency = sanitize($conn, $_POST['urgency']);
    $notes = sanitize($conn, $_POST['notes']);
    $needed_by = !empty($_POST['needed_by']) ? sanitize($conn, $_POST['needed_by']) : null;
    $requester_id = $_SESSION['donor_id'];

    $stmt = $conn->prepare("INSERT INTO blood_requests (requester_id, patient_name, blood_group, units_needed, hospital_name, location, contact_phone, urgency, notes, needed_by) VALUES (?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ississssss", $requester_id, $patient_name, $blood_group, $units_needed, $hospital_name, $location, $contact_phone, $urgency, $notes, $needed_by);
    if ($stmt->execute()) {
        $success = "Request posted successfully! Donors will be notified.";
        header("refresh:2;url=requests.php");
    } else {
        $error = "Failed to post request: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post Request — RAKTA.bd</title>
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

    <section class="form-section">
        <div class="container">
            <div class="form-grid">
                <div class="form-side">
                    <span class="section-tag">/ EMERGENCY REQUEST</span>
                    <h1>Post a <em>blood</em> request.</h1>
                    <p>Be as specific as possible. The more accurate the information, the faster a matching donor can respond. Critical requests are surfaced to the top of every donor's feed.</p>
                    <div class="quote">
                        Speed matters. So does clarity. Tell us exactly where, when, and how — and the network will mobilize.
                    </div>
                </div>

                <div class="form-card">
                    <h2>Request Details</h2>
                    <p class="form-sub">Fill in patient and hospital information. All fields are visible to donors.</p>

                    <?php if ($error): ?><div class="alert alert-error">⚠ <?php echo $error; ?></div><?php endif; ?>
                    <?php if ($success): ?><div class="alert alert-success">✓ <?php echo $success; ?></div><?php endif; ?>

                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Patient Name</label>
                                <input type="text" name="patient_name" required>
                            </div>
                            <div class="form-group">
                                <label>Blood Group</label>
                                <select name="blood_group" required>
                                    <option value="">Select</option>
                                    <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $g): ?>
                                        <option value="<?php echo $g; ?>"><?php echo $g; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Units Needed</label>
                                <input type="number" name="units_needed" min="1" max="10" value="1" required>
                            </div>
                            <div class="form-group">
                                <label>Urgency</label>
                                <select name="urgency" required>
                                    <option value="Critical">Critical (Within hours)</option>
                                    <option value="Urgent" selected>Urgent (Within 24h)</option>
                                    <option value="Normal">Normal (Scheduled)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Hospital Name</label>
                            <input type="text" name="hospital_name" placeholder="e.g. Square Hospital" required>
                        </div>

                        <div class="form-group">
                            <label>Location / Address</label>
                            <input type="text" name="location" placeholder="Area, City" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Contact Phone</label>
                                <input type="tel" name="contact_phone" required>
                            </div>
                            <div class="form-group">
                                <label>Needed By</label>
                                <input type="date" name="needed_by">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Additional Notes</label>
                            <textarea name="notes" rows="3" placeholder="Any other relevant information..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Post Request</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
