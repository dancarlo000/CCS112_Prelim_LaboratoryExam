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

// Handle search
$search = ""; 
if (isset($_GET['search'])) {
    $search = $mysql->real_escape_string($_GET['search']);
    $query = "SELECT * FROM books 
              WHERE title LIKE '%$search%' 
                 OR author LIKE '%$search%' 
                 OR year LIKE '%$search%' 
                 OR isbn LIKE '%$search%'";
} else {
    $query = "SELECT * FROM books WHERE status='available'";
}

$result = $mysql->query($query);
?>
<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Book Catalog</title>
    <style>
         /* CCS STYLES DESIGN */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            color: #2e4d2e;
            text-align: center;
            margin: 0;
            padding: 20px;
        }

        h2 {
            color: #1b5e20;
        }

        a button {
            display: inline-block;
            margin: 5px;
            color: #fff;
            background: #2e7d32;
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        a button:hover {
            background: #1b5e20;
        }

        .container {
            display: flex;     
            flex-direction: column;  
            justify-content: center;
            align-items: center;
            gap: 20px;
        }

        #catalog {
            background: #ffffff;
            border: 2px solid #2e7d32;
            border-radius: 10px;
            padding: 20px;
            width: 80%;
            max-width: 800px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        #catalog h4 {
            margin: 0 0 15px 0;
            color: #1b5e20;
        }

        #catalog input[type="text"] {
            padding: 6px;
            width: 60%;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-right: 5px;
        }

        #catalog input[type="submit"] {
            background: #2e7d32;
            color: white;
            border: none;
            padding: 6px 15px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }

        #catalog input[type="submit"]:hover {
            background: #1b5e20;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }

        th {
            background: #2e7d32;
            color: white;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }

    </style>
</head>
<body>
    <div class="container">
          <h2>Librarian Book Catalog</h2>
         <!-- Top navigation -->
        <div class="header">
            <a href="Login.php"><button>Log out</button></a>
            <a href="AddBook.php"><button>Add Book</button></a>
            <a href="ReturnAndBorrowBooks.php"><button>Return and Borrow Books</button></a>
            <a href="EditAndRemoveBooks.php"><button>Edit and Remove Books</button></a>

        </div>
        <!-- Search Form -->
        <div id="catalog">
            <h4>Librarian Book Catalog</h4>
            <form method="GET" action="">
                <form method="GET" action="">
                    <input type="text" name="search" placeholder="Search by Title, Author, Year, ISBN..." value="<?php echo htmlspecialchars($search); ?>">
                    <input type="submit" value="Search">
                </form>
        <!-- Book Table -->
            <table>
                <tr>
                    <th>Book Title</th>
                    <th>Author</th>
                    <th>Publication Year</th>
                    <th>ISBN</th>
                </tr>
                <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>".htmlspecialchars($row['title'])."</td>
                                    <td>".htmlspecialchars($row['author'])."</td>
                                    <td>".htmlspecialchars($row['year'])."</td>
                                    <td>".htmlspecialchars($row['isbn'])."</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No books found</td></tr>";
                    }
                ?>
            </table>
        </div>
    </div>
</body>
</html>

<?php
$mysql->close();
?>