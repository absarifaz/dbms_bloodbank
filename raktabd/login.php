<?php
require_once 'php/db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($conn, $_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT donor_id, full_name, password FROM donors WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password']) || $password === 'demo123') {
            $_SESSION['donor_id'] = $user['donor_id'];
            $_SESSION['full_name'] = $user['full_name'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "No account found with this email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login — RAKTA.bd</title>
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
                <li><a href="register.php" class="btn btn-primary" style="padding: 10px 20px;">Register</a></li>
            </ul>
        </div>
    </nav>

    <section class="form-section">
        <div class="container">
            <div class="form-grid">
                <div class="form-side">
                    <span class="section-tag">/ WELCOME BACK</span>
                    <h1>Sign back into the <em>network</em>.</h1>
                    <p>Pick up exactly where you left off — manage your donations, view active requests, and continue making impact across the campus community.</p>
                    <div class="quote">
                        Every login is a quiet promise: I'm still here, still ready, still willing to show up when called.
                    </div>
                </div>

                <div class="form-card">
                    <h2>Donor Login</h2>
                    <p class="form-sub">Welcome back. Enter your credentials to access your dashboard.</p>

                    <?php if ($error): ?>
                        <div class="alert alert-error">⚠ <?php echo $error; ?></div>
                    <?php endif; ?>

                    <div class="alert alert-warning" style="font-size: 13px;">
                        <strong>Demo:</strong> Use any registered email + password "demo123" to test
                    </div>

                    <form method="POST">
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" placeholder="you@university.edu.bd" required>
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" placeholder="Enter your password" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Login to Dashboard</button>
                    </form>

                    <div class="form-link">
                        Not registered yet? <a href="register.php">Create an account →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
