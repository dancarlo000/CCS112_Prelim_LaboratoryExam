<?php
$servername = "mysql-db";
$username = "root";
$password = "rootpassword";
$dbname = "librarymanagementsystem_db";

// Connect to database
$mysql = new mysqli($servername, $username, $password, $dbname);
if ($mysql->connect_error) {
    die("<h2>Connection Failed</h2>");
}

// Handle borrow/return actions
if (isset($_GET['action']) && isset($_GET['book_id'])) {
    $book_id = intval($_GET['book_id']);
    if ($_GET['action'] == "borrow") {
        $mysql->query("UPDATE books SET status='borrowed' WHERE id=$book_id AND status='available'");
    } elseif ($_GET['action'] == "return") {
        $mysql->query("UPDATE books SET status='available' WHERE id=$book_id AND status='borrowed'");
    }
}

// Handle search
$search = "";
if (isset($_GET['search'])) {
    $search = $mysql->real_escape_string($_GET['search']);
    $query = "SELECT * FROM books WHERE title LIKE '%$search%' OR author LIKE '%$search%'";
} else {
    $query = "SELECT * FROM books";
}
$result = $mysql->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Library Borrow & Return</title>
<style>
/* General Styles */
body {
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #74ebd5, #ACB6E5);
    color: #2e4d2e;
    text-align: center;
    margin: 0;
    padding: 20px;
}
.container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}
h2 {
    color: #2e7d32;
    margin-bottom: 10px;
}

/* Header Buttons */
.header {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
}
.header a button {
    display: inline-block;
    color: #fff;
    background: #2e7d32;
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: 0.3s;
}
.header a button:hover {
    background: #1b5e20;
}

/* Search Form */
.search-form {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 20px;
}
.search-form input[type="text"] {
    padding: 8px 12px;
    width: 250px;
    border: 1px solid #ccc;
    border-radius: 6px;
}
.search-form input[type="submit"] {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    background: #2e7d32;
    color: white;
    cursor: pointer;
    transition: 0.3s;
}
.search-form input[type="submit"]:hover {
    background: #1b5e20;
}


/* Table Styles */
table {
    width: 90%;              /* slightly smaller than full width */
    max-width: 900px;        /* restricts maximum size */
    margin: 0 auto;          /* centers the table */
    border-collapse: collapse;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    background: white;
}
th, td {
    padding: 12px 15px;
    text-align: center;
    border: 1px solid #ddd;
}
th {
    background: #2e7d32;
    color: white;
}
tr:nth-child(even) {
    background: #f7f7f7;
}


/* Borrow / Return Buttons */
button.borrow {
    background: #4CAF50;
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    transition: 0.3s;
}
button.borrow:hover {
    background: #388e3c;
}
button.return {
    background: #f44336;
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    transition: 0.3s;
}
button.return:hover {
    background: #d32f2f;
}

/* Responsive Table */
@media screen and (max-width: 768px) {
    table, tr, td, th {
        display: block;
    }
    tr {
        margin-bottom: 15px;
    }
    td {
        text-align: right;
        padding-left: 50%;
        position: relative;
    }
    td::before {
        content: attr(data-label);
        position: absolute;
        left: 15px;
        font-weight: bold;
        text-align: left;
    }
    th { display: none; }
}
</style>
</head>
<body>

<div class="container">
    <h2>Return and Borrow Book</h2>

    <div class="header">
        <a href="login.php"><button>Log out</button></a>
        <a href="addBook.php"><button>Add Book</button></a>
        <a href="librarianCatalog.php"><button>Library Catalog</button></a>
        <a href="EditAndRemoveBooks.php"><button>Edit and Remove Books</button></a>
    </div>
</div>

<!-- Search Form -->
<form method="GET" action="" class="search-form">
    <input type="text" name="search" placeholder="Search by Title or Author" value="<?php echo htmlspecialchars($search); ?>">
    <input type="submit" value="Search">
</form>

<!-- Book Table -->
<table>
<tr>
    <th>Title</th>
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
                <td data-label='Title'>".htmlspecialchars($row['title'])."</td>
                <td data-label='Author'>".htmlspecialchars($row['author'])."</td>
                <td data-label='Year'>".htmlspecialchars($row['year'])."</td>
                <td data-label='ISBN'>".htmlspecialchars($row['isbn'])."</td>
                <td data-label='Status'>".htmlspecialchars($row['status'])."</td>
                <td data-label='Action'>";
        if ($row['status'] == 'available') {
            echo "<a href='?action=borrow&book_id=".$row['id']."'><button class='borrow'>Borrow</button></a>";
        } else {
            echo "<a href='?action=return&book_id=".$row['id']."'><button class='return'>Return</button></a>";
        }
        echo "</td></tr>";
    }
} else {
    echo "<tr><td colspan='6'>No books found</td></tr>";
}
?>
</table>

<?php $mysql->close(); ?>
</body>
</html>

