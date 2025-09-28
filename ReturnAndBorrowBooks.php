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
            max-width: 900px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        #catalog h4 {
            margin: 0 0 15px 0;
            color: #1b5e20;
        }
        #searchBar {
            margin-bottom: 15px;
        }
        #searchBar input[type="text"] {
            padding: 10px;
            width: 60%;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        #searchBar button {
            padding: 10px 15px;
            margin-left: 5px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background: #2e7d32;
            color: white;
        }
        tr:nth-child(even) {
            background: #f2f2f2;
        }
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
    <div class="container">
        <div class="header">
            <h2>Return and Borrowed Books</h2>
            <a href="Login.php"><button>Log out</button></a>
            <a href="AddBook.php"><button>Add Book</button></a>
            <a href="LibrarianCatalog.php"><button>Library Catalog</button></a>
            <a href="EditandRemoveBooks.php"><button>Edit and Remove Books</button></a>
        </div>

        <div id="catalog">
            <h4>Pending & Borrowed Books</h4>
            
            <!-- Search Bar -->
            <form id="searchBar" method="GET" action="">
                <input type="text" name="search" placeholder="Search by Title, Author, Year, ISBN..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
                <a href="ReturnandBorrow.php"><button type="button">Reset</button></a>
            </form>

            <table>
                <tr>
                    <th>Book Title</th>
                    <th>Author</th>
                    <th>Year</th>
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
    </div>
</body>
</html>

<?php
$mysql->close();
?>


