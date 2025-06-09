<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Campus Security Staff Management System</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #e0eafc, #cfdef3);
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      text-align: center;
    }

    .container {
      background-color: white;
      padding: 50px 60px;
      border-radius: 20px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      max-width: 650px;
      width: 90%;
    }

    .banner-img {
      width: 100%;
      max-height: 180px;
      object-fit: cover;
      border-radius: 12px;
      margin-bottom: 25px;
    }

    h1 {
      font-size: 28px;
      color: #0d47a1;
      margin-bottom: 20px;
      font-weight: bold;
      line-height: 1.4;
    }

    p {
      font-size: 16px;
      color: #555;
      margin-bottom: 30px;
    }

    .btn {
      padding: 12px 30px;
      background-color: #108ABE;
      color: white;
      text-decoration: none;
      font-size: 16px;
      border-radius: 8px;
      transition: background-color 0.3s ease;
      display: inline-block;
    }

    .btn:hover {
      background-color: #0b6c92;
    }

    footer {
      font-size: 14px;
      color: #888;
      margin-top: 40px;
    }
  </style>
</head>
<body>

<div class="container">
  <img src="images/bg.jpg" alt="Campus Banner" class="banner-img" onerror="this.style.display='none';">
  <h1>Welcome to the <br>Campus Security Staff Management System</h1>
  <p>Monitor duties, request leaves & overtime,<br> and ensure campus safety efficiently.</p>
  <a href="login.php" class="btn">Login to System</a>
  <footer>© 2025 EIU Security Department</footer>
</div>

</body>
</html>
