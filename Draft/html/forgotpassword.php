<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Forgot Password | LOFT & LIVING</title>

  <link rel="stylesheet" href="../css/header_footer_style.css">
  <link rel="stylesheet" href="../css/signup.style.css">

  <style>
    body {
      display: flex !important;
      flex-direction: column !important;
      min-height: 100vh;
      margin: 0;
      background-color: #EAE8E4;
    }

    main {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 30px 16px;
    }

    .card {
      width: 100%;
      max-width: 420px;
      background: #fff;
      border-radius: 10px;
      padding: 28px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    .card h1 {
      margin: 0 0 8px;
      font-size: 24px;
      text-align: center;
    }

    .card p {
      margin: 0 0 18px;
      font-size: 14px;
      color: #666;
      text-align: center;
      line-height: 1.4;
    }

    .form-group {
      margin-bottom: 14px;
    }

    label {
      display: block;
      font-size: 14px;
      margin-bottom: 6px;
    }

    input[type="email"] {
      width: 100%;
      padding: 12px 12px;
      border: 1px solid #ccc;
      border-radius: 6px;
      outline: none;
      font-size: 14px;
      box-sizing: border-box;
    }

    input[type="email"]:focus {
      border-color: #999;
    }

    .btn {
      width: 100%;
      padding: 12px 14px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
    }

    .btn-primary {
      background: #111;
      color: #fff;
    }

    .links {
      margin-top: 14px;
      text-align: center;
      font-size: 14px;
    }

    .links a {
      color: #333;
      text-decoration: none;
    }

    .links a:hover {
      text-decoration: underline;
    }

    .message {
      display: none;
      margin: 10px 0 0;
      padding: 10px;
      border-radius: 6px;
      font-size: 14px;
    }

    .message.success {
      display: block;
      background: #eaf7ea;
      color: #1f6b1f;
      border: 1px solid #bde5bd;
    }

    .message.error {
      display: block;
      background: #fdecec;
      color: #8b1d1d;
      border: 1px solid #f4bcbc;
    }
  </style>
</head>

<body>
  <main>
    <div class="card">
      <h1>Forgot Password</h1>
      <p>Enter your email address and we’ll send you a link to reset your password.</p>

      <form action="#" method="POST">
        <div class="form-group">
          <label for="email">Email</label>
          <input
            type="email"
            id="email"
            name="email"
            placeholder="you@example.com"
            required
          />
        </div>

        <button class="btn btn-primary" type="submit">Send Reset Link</button>
      </form>

      <div class="links">
        <a href="signin.php">Back to Sign In</a>
      </div>
    </div>
  </main>
</body>
</html>
