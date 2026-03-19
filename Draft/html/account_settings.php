<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

require_once '../backend/config/db_connect.php';

$user_ID = $_SESSION['user_ID'] ?? null;

if (!$user_ID) {
    header("Location: signin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $raw  = file_get_contents("php://input");
    $data = json_decode($raw, true);
    $action = $data['action'] ?? '';

    if ($action === 'get_personal_info') {
        $stmt = $conn->prepare("SELECT name, surname, email, address FROM users WHERE user_ID = ?");
        $stmt->bind_param("i", $user_ID);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        echo json_encode($user
            ? ["success" => true, "user" => $user]
            : ["success" => false, "message" => "User not found."]);
        exit();
    }

    if ($action === 'update_personal_info') {
        $name    = trim($data['name']    ?? '');
        $surname = trim($data['surname'] ?? '');
        $email   = trim($data['email']   ?? '');
        $address = trim($data['address'] ?? '');

        if (!$name || !$surname || !$email) {
            echo json_encode(["success" => false, "message" => "Name, surname and email are required."]);
            exit();
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["success" => false, "message" => "Invalid email address."]);
            exit();
        }

        $check = $conn->prepare("SELECT user_ID FROM users WHERE email = ? AND user_ID != ?");
        $check->bind_param("si", $email, $user_ID);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $check->close();
            echo json_encode(["success" => false, "message" => "That email address is already in use."]);
            exit();
        }
        $check->close();

        $stmt = $conn->prepare("UPDATE users SET name = ?, surname = ?, email = ?, address = ? WHERE user_ID = ?");
        $stmt->bind_param("ssssi", $name, $surname, $email, $address, $user_ID);
        $ok = $stmt->execute();
        $stmt->close();

        if ($ok) {
            $_SESSION['name'] = $name;
            echo json_encode(["success" => true, "message" => "Personal information updated."]);
        } else {
            echo json_encode(["success" => false, "message" => "Update failed. Please try again."]);
        }
        exit();
    }

    if ($action === 'update_password') {
        $currentPassword = trim($data['currentPassword'] ?? '');
        $newPassword     = trim($data['newPassword']     ?? '');
        $confirmPassword = trim($data['confirmPassword'] ?? '');

        if (!$currentPassword || !$newPassword || !$confirmPassword) {
            echo json_encode(["success" => false, "message" => "All password fields are required."]);
            exit();
        }
        if ($newPassword !== $confirmPassword) {
            echo json_encode(["success" => false, "message" => "New passwords do not match."]);
            exit();
        }
        if (strlen($newPassword) < 8) {
            echo json_encode(["success" => false, "message" => "Password must be at least 8 characters."]);
            exit();
        }
        if (!preg_match('/[A-Z]/', $newPassword)) {
            echo json_encode(["success" => false, "message" => "Password must include an uppercase letter."]);
            exit();
        }
        if (!preg_match('/[a-z]/', $newPassword)) {
            echo json_encode(["success" => false, "message" => "Password must include a lowercase letter."]);
            exit();
        }
        if (!preg_match('/[0-9]/', $newPassword)) {
            echo json_encode(["success" => false, "message" => "Password must include a number."]);
            exit();
        }
        if (!preg_match('/[^A-Za-z0-9]/', $newPassword)) {
            echo json_encode(["success" => false, "message" => "Password must include a special character."]);
            exit();
        }

        $stmt = $conn->prepare("SELECT password FROM users WHERE user_ID = ?");
        $stmt->bind_param("i", $user_ID);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row || !password_verify($currentPassword, $row['password'])) {
            echo json_encode(["success" => false, "message" => "Current password is incorrect."]);
            exit();
        }
        if (password_verify($newPassword, $row['password'])) {
            echo json_encode(["success" => false, "message" => "New password must differ from your current one."]);
            exit();
        }

        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_ID = ?");
        $stmt->bind_param("si", $hashed, $user_ID);
        $ok = $stmt->execute();
        $stmt->close();

        echo json_encode($ok
            ? ["success" => true,  "message" => "Password updated successfully."]
            : ["success" => false, "message" => "Failed to update password. Please try again."]);
        exit();
    }

    if ($action === 'forgot_password') {
        $email = trim($data['email'] ?? '');
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["success" => false, "message" => "Please enter a valid email address."]);
            exit();
        }
        // TODO: generate token, store in DB, send reset email
        echo json_encode(["success" => true, "message" => "If that email exists, a reset link has been sent."]);
        exit();
    }

    // ── Delete account ──
    if ($action === 'delete_account') {
        $password = trim($data['password'] ?? '');

        if (!$password) {
            echo json_encode(["success" => false, "message" => "Please enter your password to confirm."]);
            exit();
        }

        // Verify password before deleting
        $stmt = $conn->prepare("SELECT password FROM users WHERE user_ID = ?");
        $stmt->bind_param("i", $user_ID);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row || !password_verify($password, $row['password'])) {
            echo json_encode(["success" => false, "message" => "Incorrect password. Account not deleted."]);
            exit();
        }

        // Delete the user row — if you have FK constraints with CASCADE DELETE set, related rows
        // (basket, orders etc.) will be removed automatically. Otherwise add DELETE statements here first.
        $stmt = $conn->prepare("DELETE FROM users WHERE user_ID = ?");
        $stmt->bind_param("i", $user_ID);
        $ok = $stmt->execute();
        $stmt->close();

        if ($ok) {
            $_SESSION = [];
            session_destroy();
            echo json_encode(["success" => true, "redirect" => "homepage.php"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to delete account. Please try again."]);
        }
        exit();
    }

    echo json_encode(["success" => false, "message" => "Unknown action."]);
    exit();
}

// Load user for initial page render
$stmt = $conn->prepare("SELECT name, surname, email, address FROM users WHERE user_ID = ?");
$stmt->bind_param("i", $user_ID);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: signin.php");
    exit();
}

$addressParts = array_map('trim', explode(',', $user['address'] ?? '', 4));
$addr1    = $addressParts[0] ?? '';
$addr2    = $addressParts[1] ?? '';
$addrCity = $addressParts[2] ?? '';
$addrPost = $addressParts[3] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings | LOFT & LIVING</title>
    <link rel="stylesheet" href="../css/account_settings.css">
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=12">
    <link rel="stylesheet" href="../css/header_footer_style.css?v=16">
    <link rel="stylesheet" href="../css/reusable_header.css?v=6">
    <link rel="stylesheet" href="../css/account_settings_page.css?v=1">
    <script src="../javascript/dark-mode.js"></script>
    
</head>
<body class="account-page">

<?php $headerPartialOnly = true; include 'header.php'; ?>

<div class="page-header">
    <a href="user_dash.php" class="back-dashboard">← Back to Dashboard</a>
    <h1>Account Settings</h1>
    <p>Manage your personal information, security and preferences.</p>
</div>

<div class="settings-layout">

    <aside class="sidebar">
        <div class="profile-card">
            <div class="avatar" id="profileAvatar"></div>
            <h3 id="profileName"><?php echo htmlspecialchars($user['name'] . ' ' . $user['surname']); ?></h3>
            <p id="profileEmail"><?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        <nav class="sidebar-nav">
            <button class="nav-item active" onclick="switchPanel('personal', this)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                Personal Info
            </button>
            <button class="nav-item" onclick="switchPanel('password', this)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                Password
            </button>

            <button class="nav-item nav-item--danger" onclick="switchPanel('delete', this)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                Delete Account
            </button>
        </nav>
    </aside>

    <main class="main-panel">

        <!-- Personal Info -->
        <section class="panel active" id="panel-personal">
            <div class="panel-header">
                <h2>Personal Information</h2>
                <p>Update your name, email address and delivery details.</p>
            </div>
            <div class="panel-body">
                <div class="form-row">
                    <div class="field">
                        <label>First Name</label>
                        <input type="text" id="firstName" value="<?php echo htmlspecialchars($user['name']); ?>">
                    </div>
                    <div class="field">
                        <label>Last Name</label>
                        <input type="text" id="lastName" value="<?php echo htmlspecialchars($user['surname']); ?>">
                    </div>
                </div>
                <div class="form-row full">
                    <div class="field">
                        <label>Email Address</label>
                        <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                    </div>
                </div>

                <div class="section-divider">Delivery Address</div>

                <div class="form-row full">
                    <div class="field">
                        <label>Address Line 1</label>
                        <input type="text" id="address1" placeholder="123 High Street" value="<?php echo htmlspecialchars($addr1); ?>">
                    </div>
                </div>
                <div class="form-row full">
                    <div class="field">
                        <label>Address Line 2 <span style="font-weight:300;text-transform:none;letter-spacing:0">(optional)</span></label>
                        <input type="text" id="address2" placeholder="Apartment, suite, etc." value="<?php echo htmlspecialchars($addr2); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="field">
                        <label>City</label>
                        <input type="text" id="city" placeholder="London" value="<?php echo htmlspecialchars($addrCity); ?>">
                    </div>
                    <div class="field">
                        <label>Postcode</label>
                        <input type="text" id="postcode" placeholder="SW1A 1AA" value="<?php echo htmlspecialchars($addrPost); ?>">
                    </div>
                </div>

                <div class="form-actions">
                    <button class="btn-primary" id="savePersonalBtn" onclick="savePersonal()">Save Changes</button>
                    <button class="btn-ghost" onclick="resetPersonal()">Cancel</button>
                </div>
            </div>
        </section>

        <!-- Change Password -->
        <section class="panel" id="panel-password">
            <div class="panel-header">
                <h2>Change Password</h2>
                <p>Choose a strong, unique password to keep your account secure.</p>
            </div>
            <div class="panel-body">
                <div class="form-row full">
                    <div class="field">
                        <label>Current Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="currentPw" placeholder="Enter current password">
                        </div>
                    </div>
                </div>
                <div class="form-row full">
                    <div class="field">
                        <label>New Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="newPw" placeholder="Min. 8 characters" oninput="checkStrength(this.value)">
                        </div>
                        <div class="strength-bar">
                            <div class="strength-seg" id="s1"></div>
                            <div class="strength-seg" id="s2"></div>
                            <div class="strength-seg" id="s3"></div>
                            <div class="strength-seg" id="s4"></div>
                        </div>
                        <div class="strength-label" id="strengthLabel"></div>
                    </div>
                </div>
                <div class="form-row full">
                    <div class="field">
                        <label>Confirm New Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="confirmPw" placeholder="Repeat new password">
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button class="btn-primary" id="savePwBtn" onclick="savePassword()">Update Password</button>
                    <button class="btn-ghost" onclick="clearPasswordForm()">Cancel</button>
                </div>
            </div>
        </section>

        <!-- Delete Account -->
        <section class="panel" id="panel-delete">
            <div class="panel-header panel-header--danger">
                <h2>Delete Account</h2>
                <p>Permanently remove your account and all associated data.</p>
            </div>
            <div class="panel-body">
                <div class="danger-box">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" flex-shrink="0"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <div>
                        <strong>This action is permanent and cannot be undone.</strong>
                        <p>Deleting your account will remove all your personal data, order history, saved addresses and preferences from our system.</p>
                    </div>
                </div>

                <div class="form-row full" style="margin-top: 28px;">
                    <div class="field">
                        <label>Confirm your password to continue</label>
                        <input type="password" id="deletePassword" placeholder="Enter your current password">
                    </div>
                </div>

                <div class="form-actions">
                    <button class="btn-danger" id="deleteAccountBtn" onclick="openDeleteModal()">Delete My Account</button>
                    <button class="btn-ghost" onclick="switchPanel('personal', document.querySelectorAll('.nav-item')[0])">Cancel</button>
                </div>
            </div>
        </section>

    </main>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <div class="modal-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
        </div>
        <h3>Are you sure you want to delete your account?</h3>
        <p>Your account will be <strong>permanently deleted</strong>. This action cannot be undone.</p>
        <div class="modal-actions">
            <button class="btn-danger" id="confirmDeleteBtn" onclick="confirmDelete()">Delete Account</button>
            <button class="btn-ghost" onclick="closeDeleteModal()">CANCEL</button>
        </div>
    </div>
</div>

<div class="toast" id="toast">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    <span id="toastMsg"></span>
</div>

<script>
    const SELF = 'account_settings.php';

    function switchPanel(name, btn) {
        document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
        document.getElementById('panel-' + name).classList.add('active');
        btn.classList.add('active');
    }

    function getInitials(name) {
        const parts = name.trim().split(' ').filter(Boolean);
        return (parts[0][0] + (parts.length > 1 ? parts[parts.length - 1][0] : '')).toUpperCase();
    }

    const initialName = document.getElementById('profileName').textContent;
    document.getElementById('profileAvatar').textContent = getInitials(initialName);

    function showToast(msg, type = 'success') {
        const t = document.getElementById('toast');
        document.getElementById('toastMsg').textContent = msg;
        t.className = 'toast ' + type + ' show';
        setTimeout(() => t.classList.remove('show'), 3500);
    }

    async function apiCall(action, payload = {}) {
        const res = await fetch(SELF, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action, ...payload })
        });
        return res.json();
    }

    async function savePersonal() {
        const name    = document.getElementById('firstName').value.trim();
        const surname = document.getElementById('lastName').value.trim();
        const email   = document.getElementById('email').value.trim();
        const address = [
            document.getElementById('address1').value.trim(),
            document.getElementById('address2').value.trim(),
            document.getElementById('city').value.trim(),
            document.getElementById('postcode').value.trim()
        ].filter(Boolean).join(', ');

        if (!name || !surname || !email) return showToast('Name, surname and email are required.', 'error');

        const btn = document.getElementById('savePersonalBtn');
        btn.disabled = true; btn.textContent = 'Saving…';
        const data = await apiCall('update_personal_info', { name, surname, email, address });
        btn.disabled = false; btn.textContent = 'Save Changes';

        if (data.success) {
            showToast('Personal information updated.');
            const fullName = name + ' ' + surname;
            document.getElementById('profileName').textContent   = fullName;
            document.getElementById('profileEmail').textContent  = email;
            document.getElementById('profileAvatar').textContent = getInitials(fullName);
        } else {
            showToast(data.message || 'Update failed.', 'error');
        }
    }

    function resetPersonal() {
        document.getElementById('firstName').value = '<?php echo addslashes($user['name']); ?>';
        document.getElementById('lastName').value  = '<?php echo addslashes($user['surname']); ?>';
        document.getElementById('email').value     = '<?php echo addslashes($user['email']); ?>';
        document.getElementById('address1').value  = '<?php echo addslashes($addr1); ?>';
        document.getElementById('address2').value  = '<?php echo addslashes($addr2); ?>';
        document.getElementById('city').value      = '<?php echo addslashes($addrCity); ?>';
        document.getElementById('postcode').value  = '<?php echo addslashes($addrPost); ?>';
    }

    const strengthColors = ['#e74c3c', '#e67e22', '#f1c40f', '#2C6E49'];
    const strengthLabels = ['Weak', 'Fair', 'Good', 'Strong'];

    function checkStrength(val) {
        let score = 0;
        if (val.length >= 8)          score++;
        if (/[A-Z]/.test(val))        score++;
        if (/[0-9]/.test(val))        score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        for (let i = 1; i <= 4; i++) {
            document.getElementById('s' + i).style.background =
                i <= score ? strengthColors[score - 1] : 'var(--light)';
        }
        const label = document.getElementById('strengthLabel');
        label.textContent = val.length ? (strengthLabels[score - 1] || '') : '';
        label.style.color = score ? strengthColors[score - 1] : 'var(--mid)';
    }

    async function savePassword() {
        const currentPassword = document.getElementById('currentPw').value;
        const newPassword     = document.getElementById('newPw').value;
        const confirmPassword = document.getElementById('confirmPw').value;

        if (!currentPassword || !newPassword || !confirmPassword)
            return showToast('Please fill in all password fields.', 'error');
        if (newPassword !== confirmPassword)
            return showToast('New passwords do not match.', 'error');

        const btn = document.getElementById('savePwBtn');
        btn.disabled = true; btn.textContent = 'Updating…';
        const data = await apiCall('update_password', { currentPassword, newPassword, confirmPassword });
        btn.disabled = false; btn.textContent = 'Update Password';

        if (data.success) {
            showToast('Password updated successfully.');
            clearPasswordForm();
        } else {
            showToast(data.message || 'Update failed.', 'error');
        }
    }

    function clearPasswordForm() {
        ['currentPw', 'newPw', 'confirmPw'].forEach(id => document.getElementById(id).value = '');
        checkStrength('');
    }

    async function sendResetLink() {
        const email = document.getElementById('forgotEmail').value.trim();
        if (!email || !email.includes('@'))
            return showToast('Please enter a valid email address.', 'error');
        const data = await apiCall('forgot_password', { email });
        showToast(data.message || 'Reset link sent — check your inbox.');
        document.getElementById('forgotEmail').value = '';
    }

    // ── Delete Account ──
    function openDeleteModal() {
        const password = document.getElementById('deletePassword').value;
        if (!password) return showToast('Please enter your password first.', 'error');
        document.getElementById('deleteModal').classList.add('active');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('active');
    }

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });

    async function confirmDelete() {
        const password = document.getElementById('deletePassword').value;
        const btn = document.getElementById('confirmDeleteBtn');
        btn.disabled = true; btn.textContent = 'Deleting…';

        const data = await apiCall('delete_account', { password });

        if (data.success) {
            window.location.href = data.redirect || 'homepage.php';
        } else {
            closeDeleteModal();
            btn.disabled = false; btn.textContent = 'Yes, Delete My Account';
            showToast(data.message || 'Deletion failed.', 'error');
        }
    }
</script>

<?php $footerPartialOnly = true; include 'footer.php'; ?>
</body>
</html>
