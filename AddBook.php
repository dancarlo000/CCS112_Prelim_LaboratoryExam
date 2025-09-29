<?php
/* --------------------------------------
   Database Connection Setup
   Connect to MySQL server and Library Management System
-----------------------------------------*/
$servername = "mysql-db";
$username = "root";
$password = "rootpassword";
$dbname = "librarymanagementsystem_db";
$mysql = new mysqli($servername, $username, $password, $dbname);
if ($mysql->connect_error) {
    echo "<h2> Connection Failed </h2>";
    exit;
}
?>
<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Book</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f6f9;
      margin: 0;
      padding: 0;
    }

    /* Top Navbar */
    .navbar {
      background-color: #2e7d32;
      color: #fff;
      padding: 12px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .navbar h2 {
      margin: 0;
      font-size: 20px;
    }

    .navbar .nav-buttons a {
      margin-left: 10px;
    }

    .navbar .nav-buttons button {
    background: #fff;
    color: #2e7d32;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
    font-weight: bold;   /* Make text thicker */
    }

    .navbar .nav-buttons button:hover {
      background: #e0e0e0;
    }

    /* Main container */
    .container {
      max-width: 700px;
      margin: 30px auto;
      background: #fff;
      padding: 20px 30px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .container h3 {
      text-align: center;
      color: #2e7d32;
      margin-bottom: 20px;
    }

    form {
      display: grid;
      grid-template-columns: 150px 1fr;
      gap: 15px 20px;
    }

    label {
      font-weight: bold;
      color: #333;
    }

    input[type="text"],
    input[type="number"] {
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 5px;
      width: 100%;
    }

    input[type="submit"] {
      grid-column: span 2;
      background: #2e7d32;
      color: #fff;
      border: none;
      padding: 10px;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.3s;
    }

    input[type="submit"]:hover {
      background: #1b5e20;
    }

    .message {
      text-align: center;
      margin-top: 15px;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <div class="navbar">
    <h2>Add Books</h2>
    <div class="nav-buttons">
      <a href="Login.php"><button>Log out</button></a>
      <a href="LibrarianCatalog.php"><button>View Catalog</button></a>
    </div>
  </div>

  <!-- Form container -->
  <div class="container">
    <h3>Book Information</h3>
    <form method="POST" action="">
      <label>Book Title</label>
      <input type="text" name="title" required>

      <label>Book Author</label>
      <input type="text" name="author" required>

      <label>Publication Year</label>
      <input type="number" name="year" required>

      <label>ISBN</label>
      <input type="number" name="isbn" required>

      <input type="submit" name="addBook" value="Add Book">
    </form>

    <?php
      // Responsible for adding books
      if (isset($_POST["addBook"])) {
          $title = $_POST["title"];
          $author = $_POST["author"];
          $year = $_POST["year"];
          $isbn = $_POST["isbn"];

          // input validation for publication year
          if ($year < 1500 || $year > 2025) {
              echo "<p class='message' style='color:red;'>The publication year should stay between 1500 and 2025</p>";
              exit;
          }

          $checkquery = "SELECT * FROM books WHERE isbn = '$isbn'";
          $result = $mysql->query($checkquery);

          if ($result->num_rows > 0) {
              echo "<p class='message' style='color:red;'>Book with ISBN $isbn already exists</p>";
          } else {
              $insertQuery = "INSERT INTO books (title, author, year, isbn)
                              VALUES ('$title', '$author', '$year', '$isbn')";
              if ($mysql->query($insertQuery) === true) {
                   echo "<p class='message' style='color:green;'>Book added successfully!</p>";
              } else {
                   echo "<p class='message' style='color:red;'>Book addition failed.</p>";
              }
          }
      }
      $mysql->close();
    ?>
  </div>
</body>
</html>