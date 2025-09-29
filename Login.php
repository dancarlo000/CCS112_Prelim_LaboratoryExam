<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log In</title>
  <style>
        /* ===== CSS STYLES DESIGN ===== */

    body {
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #74ebd5, #ACB6E5); 
      font-family: Arial, sans-serif;
    }
    .box {
      background: white;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      text-align: center;
    }
    h2 {
      margin-bottom: 20px;
    }
    .btn {
      display: inline-block;
      padding: 12px 24px;
      margin: 10px;
      border: none;
      border-radius: 8px;
      background-color: #4CAF50;
      color: white;
      font: white;
      font-size: 16px;
      cursor: pointer;
      text-decoration: none;
      transition: background 0.3s ease;
    }
    .btn:hover {
      background-color: #07660bff;
    }

    a {
      color: white;
    }
</style>

</head>
<body>
    <!-- Top navigation -->
  <div class="box">
    <h2>Select User Type</h2>
    <a href="UserCatalog.php" class="btn">User</a>
    <a href="LibrarianCatalog.php" class="btn">Librarian</a>
  </div>
</body>
</html>
