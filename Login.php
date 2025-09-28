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
          width: 300px;
        }
        h2 {
          margin-bottom: 20px;
          color: #07660b;
        }
        input[type="text"], input[type="password"] {
          width: 90%;
          padding: 10px;
          margin: 10px 0;
          border: 1px solid #ccc;
          border-radius: 8px;
          font-size: 14px;
        }
        input[type="submit"] {
          width: 100%;
          padding: 12px;
          margin-top: 15px;
          border: none;
          border-radius: 8px;
          background-color: #4CAF50;
          color: white;
          font-size: 16px;
          cursor: pointer;
          transition: background 0.3s ease;
        }
        input[type="submit"]:hover {
          background-color: #07660b;
        }
        .error {
          color: red;
          margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="box">
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