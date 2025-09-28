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
   Borrow & Return Feature (Librarian Side)
   Librarian approves borrow and marks return.
-----------------------------------------*/
if (isset($_GET['action']) && isset($_GET['book_id'])) {
    $book_id = intval($_GET['book_id']);
    if ($_GET['action'] == "approve") {
        $mysql->query("UPDATE books SET status='borrowed' WHERE id=$book_id AND status='pending'");
    } elseif ($_GET['action'] == "return") {
        $mysql->query("UPDATE books SET status='available' WHERE id=$book_id AND status='borrowed'");
    }
}

/* --------------------------------------
   Search & Query Books (Pending & Borrowed)
-----------------------------------------*/
$search = "";
if (isset($_GET['search'])) {
    $search = $mysql->real_escape_string($_GET['search']);
    $query = "SELECT * FROM books 
              WHERE (status='pending' OR status='borrowed')
              AND (title LIKE '%$search%' 
                   OR author LIKE '%$search%' 
                   OR year LIKE '%$search%'
                   OR isbn LIKE '%$search%')";
} else {
    $query = "SELECT * FROM books WHERE status='pending' OR status='borrowed'";
}
$result = $mysql->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return and Borrowed Books</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }

        /* Navbar */
        .navbar {
            background-color: #2e7d32;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar h1 {
            margin: 0;
            font-size: 20px;
        }
        .navbar .nav-links {
            display: flex;
            gap: 10px;
        }
        .navbar button {
            background: white;
            color: #2e7d32;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: bold;
        }
        .navbar button:hover {
            background: #e0e0e0;
        }

        /* Container */
        .container {
            margin: 30px auto;
            max-width: 1000px;
            background: #fff;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        h2 {
            color: #2e7d32;
            margin-bottom: 15px;
        }

        /* Search Bar */
        #searchBar {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        #searchBar input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        #searchBar button {
            padding: 10px 15px;
            background: #2e7d32;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }
        #searchBar button:hover {
            background: #1b5e20;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #2e7d32;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }

        /* Action buttons */
        .approve {
            background: #4CAF50;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }
        .approve:hover {
            background: #388e3c;
        }
        .return {
            background: #f44336;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }
        .return:hover {
            background: #d32f2f;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <h1>Return & Borrow</h1>
        <div class="nav-links">
            <a href="Login.php"><button>Log out</button></a>
            <a href="LibrarianCatalog.php"><button>Back to Catalog</button></a>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container">
        <h2>Pending & Borrowed Books List</h2>

        <!-- Search Bar -->
        <form id="searchBar" method="GET" action="">
            <input type="text" name="search" placeholder="Search by Title, Author, Year, ISBN..." 
                value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
            <button type="submit">Search</button>
            <a href="ReturnandBorrowBooks.php"><button type="button">Refresh</button></a> 
        </form>

        <!-- Table -->
        <table>
            <tr>
                <th>Book Title</th>
                <th>Author</th>
                <th>Publication Year</th>
                <th>ISBN</th>
                <th>Status</th>
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
                                <td>".htmlspecialchars($row['status'])."</td>
                                <td>";
                        if ($row['status'] == "pending") {
                            echo "<a href='?action=approve&book_id=".$row['id']."'><button class='approve'>Approve Borrow</button></a>";
                        } elseif ($row['status'] == "borrowed") {
                            echo "<a href='?action=return&book_id=".$row['id']."'><button class='return'>Return</button></a>";
                        }
                        echo "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No borrowed or pending books</td></tr>";
                }
            ?>
        </table>
    </div>
</body>
</html>

<?php
$mysql->close();
?>
