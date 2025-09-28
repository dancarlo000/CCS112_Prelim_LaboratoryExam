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

// Handle Book Removal (DELETE) 
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

// Book Edit Submission 
if (isset($_POST['edit_book'])) {
    $book_id = filter_var($_POST['book_id'], FILTER_SANITIZE_NUMBER_INT);
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $year = filter_var($_POST['year'], FILTER_SANITIZE_NUMBER_INT);
    $isbn = filter_var($_POST['isbn'], FILTER_SANITIZE_NUMBER_INT);
    
    $error_message = '';

    // Validate Publication Year
    if ($year < 1500 || $year > 2025) {
        $error_message = "<p style='color: red; text-align: center;'>The publication year should stay between the year 1500 and 2025</p>";
    }

    // Validate Unique ISBN (must not match any other book's ISBN)
    if (empty($error_message)) {
        // Query to check if the ISBN already exists for a DIFFERENT book ID
        $check_isbn_query = "SELECT id FROM books WHERE isbn = ? AND id != ?";
        $stmt_check = $mysql->prepare($check_isbn_query);
        $stmt_check->bind_param("ii", $isbn, $book_id);
        $stmt_check->execute();
        $check_result = $stmt_check->get_result();
        
        if ($check_result->num_rows > 0) {
            $error_message = "<p style='color: red; text-align: center;'>Error: The ISBN number already exists for another book.</p>";
        }
        $stmt_check->close();
    }

    // Perform Update if no errors
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

// Handle Search and Fetch Books
$search = "";
$query = "SELECT id, title, author, year, isbn FROM books";
if (isset($_GET['search'])) {
    $search = $mysql->real_escape_string($_GET['search']);
    $query = "SELECT id, title, author, year, isbn FROM books WHERE title LIKE '%$search%' OR author LIKE '%$search%'";
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
    font-size: 24px;
}

/* Header/Navigation Bar */
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
    text-decoration: none;
}
.header a button:hover {
    background: #1b5e20;
}

/* Search Form */
.search-form {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 20px;
}
.search-form input[type="text"] {
    padding: 10px;
    width: 250px;
    border: 1px solid #ccc;
    border-radius: 6px 0 0 6px;
    margin-right: 0;
}
.search-form input[type="submit"] {
    padding: 10px 15px;
    border: none;
    border-radius: 0 6px 6px 0;
    background: #2e7d32;
    color: white;
    cursor: pointer;
    transition: 0.3s;
}
.search-form input[type="submit"]:hover {
    background: #1b5e20;
}

/* Catalog Table Styling */
table {
    width: 90%; 
    max-width: 900px;
    margin: 20px auto;
    border-collapse: collapse;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    background: white;
    border-radius: 8px;
    overflow: hidden;
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

/* Action Buttons (Edit and Remove) */
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
.edit-btn {
    background: #2e7d32;
}
.edit-btn:hover {
    background: #1b5e20;
}
.remove-btn {
    background: #d32f2f;
}
.remove-btn:hover {
    background: #b71c1c;
}

/* Style for the Edit Form */
.edit-form-container {
    background: #ffffff;
    border: 2px solid #2e7d32;
    border-radius: 10px;
    padding: 20px;
    width: 450px;
    margin: 30px auto;
    text-align: left;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.edit-form-container h4 {
    margin: 0 0 15px 0;
    text-align: center;
    color: #1b5e20;
}
.edit-form-container form {
    display: grid;
    grid-template-columns: 120px 1fr;
    gap: 10px 15px;
    align-items: center;
    justify-content: initial;
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
    width: auto;
}
.edit-form-container input[type="submit"]:hover {
    background: #1b5e20;
}

</style>
</head>
<body>

<div class="container">
    <h2>Edit and Remove Book</h2>

    <div class="header">
        <a href="Login.php"><button>Log out</button></a>
        <a href="AddBook.php"><button>Add Book</button></a>
        <a href="LibrarianCatalog.php"><button>Library Catalog</button></a>
        <a href="ReturnAndBorrowBooks.php"><button>Return and Borrow Books</button></a>
    </div>
</div>

<div class="search-form">
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Search by Title or Author" value="<?php echo htmlspecialchars($search); ?>">
        <input type="submit" value="Search">
    </form>
</div>

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
                    $book_title = htmlspecialchars($row['title']);
                    echo "<tr>
                            <td>{$book_title}</td>
                            <td>".htmlspecialchars($row['author'])."</td>
                            <td>".htmlspecialchars($row['year'])."</td>
                            <td>".htmlspecialchars($row['isbn'])."</td>
                            <td>
                                <a href='?edit_id={$book_id}&search={$search}' class='action-button edit-btn'>Edit</a>
                                
                                <form method='POST' style='display:inline;' onsubmit=\"return confirm('Confirm removal of: {$book_title}?');\">
                                    <input type='hidden' name='book_id_to_delete' value='{$book_id}'>
                                    <button type='submit' name='delete_book' class='action-button remove-btn'>Remove</button>
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

<?php
// Display Edit Form (pre-populated) 
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
                <input type="number" name="isbn" value="<?= htmlspecialchars($book_to_edit['isbn']); ?>" required>

                <input type="submit" name="edit_book" value="Save Changes">
            </form>
        </div>
        <?php
    }
    $stmt->close();
}
?>

</body>
</html>

<?php
$mysql->close();
?>