<?php
require_once 'php/db.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = sanitize($conn, $_POST['student_id']);
    $full_name = sanitize($conn, $_POST['full_name']);
    $email = sanitize($conn, $_POST['email']);
    $phone = sanitize($conn, $_POST['phone']);
    $blood_group = sanitize($conn, $_POST['blood_group']);
    $department = sanitize($conn, $_POST['department']);
    $campus = sanitize($conn, $_POST['campus']);
    $address = sanitize($conn, $_POST['address']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $last_donation = !empty($_POST['last_donation_date']) ? sanitize($conn, $_POST['last_donation_date']) : null;

    $check = $conn->prepare("SELECT donor_id FROM donors WHERE email = ? OR student_id = ?");
    $check->bind_param("ss", $email, $student_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "A donor with this email or student ID already exists.";
    } else {
        $stmt = $conn->prepare("INSERT INTO donors (student_id, full_name, email, password, phone, blood_group, department, campus, address, last_donation_date) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssssssss", $student_id, $full_name, $email, $password, $phone, $blood_group, $department, $campus, $address, $last_donation);
        if ($stmt->execute()) {
            $success = "Registration successful! You can now login.";
            header("refresh:2;url=login.php");
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register as Donor — RAKTA.bd</title>
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
                <li><a href="login.php">Login</a></li>
            </ul>
        </div>
    </nav>

    <section class="form-section">
        <div class="container">
            <div class="form-grid">
                <div class="form-side">
                    <span class="section-tag">/ JOIN THE NETWORK</span>
                    <h1>Add your name to the <em>roster</em>.</h1>
                    <p>Registration takes under two minutes. Once registered, you become discoverable in emergencies — but always with full control over when you respond.</p>
                    <div class="quote">
                        "The best time to give blood was yesterday. The next best time is when someone needs it."
                    </div>
                </div>

                <div class="form-card">
                    <h2>Donor Registration</h2>
                    <p class="form-sub">Fill in your details below — all information stays within the campus network.</p>

                    <?php if ($error): ?>
                        <div class="alert alert-error">⚠ <?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success">✓ <?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Student ID</label>
                                <input type="text" name="student_id" placeholder="CSE-2021-001" required>
                            </div>
                            <div class="form-group">
                                <label>Blood Group</label>
                                <select name="blood_group" required>
                                    <option value="">Select</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" placeholder="Your full name" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" placeholder="you@university.edu.bd" required>
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="tel" name="phone" placeholder="017XXXXXXXX" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Department</label>
                                <input type="text" name="department" placeholder="Computer Science" required>
                            </div>
                            <div class="form-group">
                                <label>Campus / University</label>
                                <input type="text" name="campus" placeholder="Dhaka University" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="address" placeholder="Area, City">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" placeholder="Min. 6 characters" required minlength="6">
                            </div>
                            <div class="form-group">
                                <label>Last Donation (optional)</label>
                                <input type="date" name="last_donation_date">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Register & Save Lives</button>
                    </form>

                    <div class="form-link">
                        Already part of the network? <a href="login.php">Login here →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
