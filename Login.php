<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") { 
        $username = $_POST["username"];     
        $password = $_POST["password"];  

        if ($username === "student" && $password === "student123") {
            header("Location: userCatalog.php");
            exit();
        } elseif ($username === "admin" && $password === "admin123") {
            header("Location: librarianCatalog.php");
            exit();
        } else {
            $error = "Invalid username or password";
        }
    }
?>          

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f2f2f2;  /* plain background, no gradient */
      margin: 0;
      padding: 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-container {
      background: #ffffff;
      border: 2px solid #2e7d32;
      border-radius: 12px;
      padding: 30px 25px;
      width: 350px;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
      margin: 0 0 20px 0;
      color: #1b5e20;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    input[type="text"], 
    input[type="password"] {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 15px;
      line-height: 1.4;   /* keeps placeholder and text aligned */
      width: 100%;
      box-sizing: border-box;
    }

    input::placeholder {
      color: #888;
      font-size: 14px;
    }

    input[type="submit"] {
      background: #2e7d32;
      color: white;
      border: none;
      padding: 10px;
      border-radius: 6px;
      font-size: 15px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }

    input[type="submit"]:hover {
      background: #1b5e20;
    }

    .error {
      color: red;
      margin-top: 12px;
      font-size: 0.9em;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Log In</h2>
    <form action="Login.php" method="post">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="submit" value="Log in">
    </form>
    <?php 
      if (!empty($error)) {
          echo "<p class='error'>$error</p>";
      }
    ?>
  </div>
</body>
</html>