<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
    <style>
        /* Wrapper */
        .admin-wrapper {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
        }

        /* Titles */
        .title { font-size: 28px; margin-bottom: 5px; color: #333; }
        .subtitle { font-size: 15px; color: #666; margin-bottom: 25px; }

        /* Form grid */
        .form-grid {
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
        }
        .form-left {
            flex: 2;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .form-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
        }

        /* Buttons / Links */
        .link-btn {
            display: block;
            padding: 12px;
            border-radius: 6px;
            background: #5c5c5c;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: 0.2s ease;
        }
        .link-btn:hover { background: #1f8438; }

        /* Profile summary */
        .profile-card {
            width: 100%;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            text-align: center;
            box-sizing: border-box;
        }
        .profile-card img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }
        .profile-card h3 {
            margin: 0;
            color: #333;
            font-size: 20px;
        }
        .profile-card p {
            margin: 5px 0 0;
            color: #666;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-grid { flex-direction: column; }
            .form-right { align-items: flex-start; }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <h1 class="title">Account Settings</h1>
        <p class="subtitle">Manage your account preferences</p>

        <div class="form-grid">
            <!-- Left Column: Account Links -->
            <div class="form-left">
                <a href="/account/change-info" class="link-btn">Change Personal Information</a>
                <a href="/account/change-password" class="link-btn">Change Password</a>
                <a href="/account/recent-orders" class="link-btn">My Recent Orders</a>
            </div>

            <!-- Right Column: Profile Summary -->
            <div class="form-right">
                <div class="profile-card">
                    <img src="/images/default-avatar.png" alt="User Avatar" id="profileAvatar">
                    <h3 id="profileName">John Doe</h3>
                    <p id="profileEmail">johndoe@example.com</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>