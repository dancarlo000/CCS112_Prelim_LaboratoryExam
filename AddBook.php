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
    <title>Add book page</title>
    
    <style>
        /* CCS STYLES DESIGN */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            color: #2e4d2e;
            text-align: center;
            height: 150vh;
            margin: 0;
            padding: 20px;
        }

        h2 {
            color: #1b5e20;
        }

        a {
            display: inline-block;
            margin-bottom: 0px;
            color: #fff;
            background: #2e7d32;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            transition: 0.3s;
        }

        a:hover {
            background: #1b5e20;
        }

        form {
            margin-bottom: 20px;
        }

        input[type="text"], input[type="number"] {
            padding: 8px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        input[type="submit"] {
            background: #2e7d32;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }

        input[type="submit"]:hover {
            background: #1b5e20;
        }

        input[type="submit"], button {
            background: #2e7d32;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
            margin-left: 5px; 
        }

        input[type="submit"]:hover, button:hover {
            background: #1b5e20;
        }

        .container {
            display: flex;     
            flex-direction: column;  
            justify-content: center;
            align-items: center;
            height: 50%; 
            gap: 20px;

        }

        #addBook {
            background: #ffffff;
            border: 2px solid #2e7d32;
            border-radius: 10px;
            padding: 10px 15px;
            padding-right: 20px;
            width: 400px; 
            text-align: left;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        #addBook h4 {
            margin: 0 0 10px 0;
            text-align: center;
            color: #1b5e20;
        }

        #addBook form {
            display: grid;
            grid-template-columns: 120px 1fr; 
            gap: 8px 12px; 
            align-items: center;
            padding-bottom: 0;
            margin-bottom: 0;
        }

        #addBook label {
            font-weight: bold;
            font-size: 0.9em;
        }

        #addBook input[type="text"],
        #addBook input[type="number"] {
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 0.9em;
            width: 100%;
            padding-bottom: 0;
        }

        #addBook input[type="submit"] {
            grid-column: span 2; 
            background: #2e7d32;
            color: white;
            border: none;
            padding: 8px;
            margin-bottom: 0;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }

        #addBook input[type="submit"]:hover {
            background: #1b5e20;
        }
    </style>
</head>

<body>
    <div class="container"> 
            <!-- Top navigation -->
        <div class ="header">
            <a href="Login.php" style = "margin-right: 125px">
                <button>Log out</button>
            </a>
            <a href ="LibrarianCatalog.php">
                <button>View Book Catalog</button>
            </a>
        </div>
        <div id='addBook'>
            <h4>Add Book</h4>
            <form method="POST" action="">
                <label>Book Title</label>
                <input type="text" name="title" required>

                <label>Book Author</label>
                <input type="text" name="author" required>

                <label>Publication Year</label>
                <input type="number" name="year" required>

                <label>ISBN</label>
                <input type="number" name="isbn" required>

                <input type="submit" name= "addBook" value="Add Book">
            </form>
        </div>
    </div>
    <br>

    <?php
        // Responsible for adding books
        if (isset($_POST["addBook"])) {
            $title = $_POST["title"];
            $author = $_POST["author"];
            $year = $_POST["year"];
            $isbn = $_POST["isbn"];

            //input validation for publication year
            if ($year < 1500 || $year > 2025) {
                echo "<p>The publication year should stay between the year 1500 and 2025</p>";
                exit;
            }

            $checkquery = "SELECT * FROM books WHERE isbn = '$isbn'";
            $result = $mysql->query($checkquery);

            // Checks if the book's isbn is unique
            if ($result->num_rows > 0) {
                echo "<p>Book with ISBN $isbn already exists</p>";
            } else {
                $insertQuery = "INSERT INTO books (title, author, year, isbn)
                                VALUES ('$title', '$author', '$year', '$isbn')";
                if ($mysql->query($insertQuery) === true) {
                     echo "<p style='color: green; text-align: center;'>Book added successfully!</p>";
                } else {
                     echo "<p style='color: green; text-align: center;'>Book addition failed.</p>";
                }
            }
        }
        $mysql->close();
    ?>
</body>
</html>