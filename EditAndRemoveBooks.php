<?php
/* --------------------------------------
   Database Connection Setup
----------------------------------------*/
$servername = "mysql-db";
$username = "root";
$password = "rootpassword";
$dbname = "librarymanagementsystem_db";
$mysql = new mysqli($servername, $username, $password, $dbname);

if ($mysql->connect_error) {
    echo "<h2> Connection Failed </h2>";
    exit;
}

// Handle Book Removal
if (isset($_POST['delete_book'])) {
    $book_id_to_delete = filter_var($_POST['book_id_to_delete'], FILTER_SANITIZE_NUMBER_INT);
    $delete_query = "DELETE FROM books WHERE id = ?";
    $stmt = $mysql->prepare($delete_query);
    $stmt->bind_param("i", $book_id_to_delete);
    if ($stmt->execute()) {
        echo "<p style='color: green; text-align: center;'>Book removed successfully!</p>";
    } else {
        echo "<p style='color: red; text-align: center;'>Error removing book: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Handle Book Edit Submission
if (isset($_POST['edit_book'])) {
    $book_id = filter_var($_POST['book_id'], FILTER_SANITIZE_NUMBER_INT);
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $year = filter_var($_POST['year'], FILTER_SANITIZE_NUMBER_INT);
    $isbn = filter_var($_POST['isbn'], FILTER_SANITIZE_NUMBER_INT);
    
    $error_message = '';

    // Validate Year
    if ($year < 1500 || $year > 2025) {
        $error_message = "<p style='color: red; text-align: center;'>Publication year should be between 1500 and 2025</p>";
    }

    // Validate Unique ISBN
    if (empty($error_message)) {
        $check_isbn_query = "SELECT id FROM books WHERE isbn = ? AND id != ?";
        $stmt_check = $mysql->prepare($check_isbn_query);
        $stmt_check->bind_param("ii", $isbn, $book_id);
        $stmt_check->execute();
        $check_result = $stmt_check->get_result();
        if ($check_result->num_rows > 0) {
            $error_message = "<p style='color: red; text-align: center;'>Error: ISBN already exists for another book.</p>";
        }
        $stmt_check->close();
    }

    // Update Book if no errors
    if (empty($error_message)) {
        $update_query = "UPDATE books SET title = ?, author = ?, year = ?, isbn = ? WHERE id = ?";
        $stmt = $mysql->prepare($update_query);
        $stmt->bind_param("ssisi", $title, $author, $year, $isbn, $book_id);
        if ($stmt->execute()) {
            echo "<p style='color: green; text-align: center;'>Book updated successfully!</p>";
        } else {
            echo "<p style='color: red; text-align: center;'>Error updating book: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo $error_message;
    }
}

// Handle Search
$search = "";
$query = "SELECT id, title, author, year, isbn FROM books";
if (isset($_GET['search'])) {
    $search = $mysql->real_escape_string($_GET['search']);
    $query = "SELECT id, title, author, year, isbn FROM books 
              WHERE title LIKE '%$search%' 
                 OR author LIKE '%$search%' 
                 OR year LIKE '%$search%' 
                 OR isbn LIKE '%$search%'";
}
$result = $mysql->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit and Remove Books</title>
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
    .action-button {
        display: inline-block;
        padding: 6px 12px;
        margin: 2px;
        font-size: 0.9em;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.3s;
        text-decoration: none;
    }
    .edit-btn { background: #2e7d32; }
    .edit-btn:hover { background: #1b5e20; }
    .remove-btn { background: #d32f2f; }
    .remove-btn:hover { background: #b71c1c; }

    /* Edit Form */
    .edit-form-container {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin: 30px auto;
        max-width: 500px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .edit-form-container h4 {
        margin: 0 0 15px 0;
        text-align: center;
        color: #2e7d32;
    }
    .edit-form-container form {
        display: grid;
        grid-template-columns: 120px 1fr;
        gap: 10px 15px;
        align-items: center;
        margin-bottom: 0;
    }
    .edit-form-container input[type="submit"] {
        grid-column: span 2;
        background: #2e7d32;
        color: white;
        border: none;
        padding: 10px;
        margin-top: 10px;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.3s;
    }
    .edit-form-container input[type="submit"]:hover { 
        background: #1b5e20;
    }
</style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <h1>Edit & Remove Books</h1>
    <div class="nav-links">
        <a href="Login.php"><button>Log out</button></a>
        <a href="LibrarianCatalog.php"><button>Back to Catalog</button></a>
    </div>
</div>

<!-- Main Container -->
<div class="container">
    <h2>Book List</h2>

    <!-- Search Bar -->
    <div id="searchBar">
        <form method="GET" action="" style="display: flex; width: 100%; gap: 10px;">
            <input type="text" name="search" placeholder="Search by Title, Author, Year, ISBN..." 
                   value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th>Book Title</th>
                <th>Author</th>
                <th>Publication Year</th>
                <th>ISBN</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $book_id = htmlspecialchars($row['id']);
                    echo "<tr>
                            <td>".htmlspecialchars($row['title'])."</td>
                            <td>".htmlspecialchars($row['author'])."</td>
                            <td>".htmlspecialchars($row['year'])."</td>
                            <td>".htmlspecialchars($row['isbn'])."</td>
                            <td>
                                <a href='?edit_id={$book_id}&search={$search}' class='action-button edit-btn'>Edit</a>
                                <form method='POST' action='' style='display:inline;' onsubmit=\"return confirm('Are you sure you want to delete this book?');\">
                                    <input type='hidden' name='book_id_to_delete' value='{$book_id}'>
                                    <button type='submit' name='delete_book' class='action-button remove-btn'>Delete</button>
                                </form>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No books found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php
// Edit Form
if (isset($_GET['edit_id'])) {
    $edit_id = filter_var($_GET['edit_id'], FILTER_SANITIZE_NUMBER_INT);
    $edit_query = "SELECT id, title, author, year, isbn FROM books WHERE id = ?";
    $stmt = $mysql->prepare($edit_query);
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_result = $stmt->get_result();
    if ($edit_result->num_rows > 0) {
        $book_to_edit = $edit_result->fetch_assoc();
        ?>
        <div class="edit-form-container">
            <h4>Edit Book: <?= htmlspecialchars($book_to_edit['title']); ?></h4>
            <form action="" method="post">
                <input type="hidden" name="book_id" value="<?= htmlspecialchars($book_to_edit['id']); ?>">
                <input type="hidden" name="search" value="<?= htmlspecialchars($search); ?>">

                <label>Book Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($book_to_edit['title']); ?>" required>

                <label>Book Author</label>
                <input type="text" name="author" value="<?= htmlspecialchars($book_to_edit['author']); ?>" required>

                <label>Publication Year</label>
                <input type="number" name="year" value="<?= htmlspecialchars($book_to_edit['year']); ?>" required>

                <label>ISBN</label>
                <input type="number" name="isbn" value="<?= htmlspecialchars($book_to_edit['isbn']); ?>" readonly>

                <input type="submit" name="edit_book" value="Save Changes">
            </form>
        </div>
        <?php
    }
    $stmt->close();
}
$mysql->close();
?>

</body>
</html>