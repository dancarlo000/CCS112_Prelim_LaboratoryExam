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

    /* --------------------------------------
    Borrow Feature (User Side)
    User can only request borrow.
    Status will be 'pending' until librarian approves.
    -----------------------------------------*/
if (isset($_GET['action']) && isset($_GET['book_id'])) {
    $book_id = intval($_GET['book_id']);
    if ($_GET['action'] == "borrow") {
        // Only allow borrow if book is available
        $mysql->query("UPDATE books SET status='pending' WHERE id=$book_id AND status='available'");
    }
}

/* --------------------------------------
   Search Feature
   If user submits a search, filter by title or author.
   Otherwise, display all books.
-----------------------------------------*/
$search = "";
if (isset($_GET['search'])) {
    $search = $mysql->real_escape_string($_GET['search']);
    $query = "SELECT * FROM books 
              WHERE (title LIKE '%$search%' 
                  OR author LIKE '%$search%' 
                  OR year LIKE '%$search%' 
                  OR isbn LIKE '%$search%')
              AND status='available'";
} else {
    $query = "SELECT * FROM books WHERE status='available'";
}
$result = $mysql->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Book Catalog</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f6f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            background: #2e7d32;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        header h2 {
            margin: 0;
            font-size: 22px;
        }

        .nav-buttons a button {
            margin-left: 10px;
            background: white;
            color: #2e7d32;
            font-weight: bold;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }

        .nav-buttons a button:hover {
            background: #1b5e20;
            color: white;
        }

        .container {
            margin: 30px auto;
            max-width: 900px;
            padding: 20px;
        }

        #catalog {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        #catalog h4 {
            margin-bottom: 15px;
            font-size: 18px;
            color: #2e7d32;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 5px;
        }

        form {
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 8px;
            width: 60%;
            border: 1px solid #bbb;
            border-radius: 6px;
        }

        input[type="submit"], .borrow, .refresh-btn {
    background: #2e7d32;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s;
}

input[type="submit"]:hover, .borrow:hover, .refresh-btn:hover {
    background: #1b5e20;
}

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #e0e0e0;
            text-align: left;
        }

        th {
            background: #2e7d32;
            color: white;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }
    </style>
</head>
<body>
    <!-- Top Header -->
    <header>
        <h2>User Book Catalog</h2>
        <div class="nav-buttons">
            <a href="Login.php"><button>Log out</button></a>
        </div>
    </header>

    <!-- Main Container -->
    <div class="container">
        <div id="catalog">
            <h4>Book List</h4>
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search by Title, Author, Year, ISBN..." value="<?php echo htmlspecialchars($search); ?>">
                <input type="submit" value="Search">
                <a href="UserCatalog.php"><button type="button" class="refresh-btn">Refresh</button></a> 
            </form>
            <!-- Book Table -->
            <table>
                <tr>
                    <th>Book Title</th>
                    <th>Author</th>
                    <th>Publication Year</th>
                    <th>ISBN</th>
                    <th>Action</th>
                </tr>
                <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>".htmlspecialchars($row['title'])."</td>
                                    <td>".htmlspecialchars($row['author'])."</td>
                                    <td>".htmlspecialchars($row['year'])."</td>
                                    <td>".htmlspecialchars($row['isbn'])."</td>
                                    <td>
                                        <a href='?action=borrow&book_id=".$row['id']."'>
                                            <button class='borrow'>Borrow</button>
                                        </a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No books available</td></tr>";
                    }
                ?>
            </table>
        </div>
    </div>
</body>
</html>

<?php $mysql->close(); ?>