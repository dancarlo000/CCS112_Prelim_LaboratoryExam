<?php
$servername = "mysql-db";
$username = "root";
$password = "rootpassword";
$dbname = "librarymanagementsystem_db";
$mysql = new mysqli($servername, $username, $password, $dbname);

if ($mysql->connect_error) {
    die("Connection failed: " . $mysql->connect_error);
}

// Handle book removal
if (isset($_POST['delete_book'])) {
    $book_id_to_delete = $_POST['book_id_to_delete'];
    $delete_query = "DELETE FROM books WHERE id = ?";
    $stmt = $mysql->prepare($delete_query);
    $stmt->bind_param("i", $book_id_to_delete);
    if ($stmt->execute()) {
        echo "<p>Book removed successfully!</p>";
    } else {
        echo "<p>Error removing book: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Handle book edit submission
if (isset($_POST['edit_book'])) {
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $year = $_POST['year'];
    $isbn = $_POST['isbn'];

    $update_query = "UPDATE books SET title = ?, author = ?, year = ?, isbn = ? WHERE id = ?";
    $stmt = $mysql->prepare($update_query);
    $stmt->bind_param("ssisi", $title, $author, $year, $isbn, $book_id);
    if ($stmt->execute()) {
        echo "<p>Book updated successfully!</p>";
    } else {
        echo "<p>Error updating book: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Fetch all books
$sql = "SELECT id, title, author, year, isbn FROM books";
$result = $mysql->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Book Catalog</title>
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

        .header a {
            display: inline-block;
            margin: 0 5px;
            color: #fff;
            background: #2e7d32;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            transition: 0.3s;
        }

        .header a:hover {
            background: #1b5e20;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #2e7d32;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .actions a, .actions button {
            color: #fff;
            background: #2e7d32;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .actions a:hover, .actions button:hover {
            background: #1b5e20;
        }

        .actions button.remove {
            background: #d32f2f;
        }

        .actions button.remove:hover {
            background: #b71c1c;
        }

        .edit-form-container {
            background: #ffffff;
            border: 2px solid #2e7d32;
            border-radius: 10px;
            padding: 20px;
            width: 500px;
            margin: 20px auto;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: left;
        }

        .edit-form-container h4 {
            text-align: center;
            color: #1b5e20;
        }

        .edit-form-container form {
            display: grid;
            grid-template-columns: 120px 1fr;
            gap: 10px 15px;
            align-items: center;
        }

        .edit-form-container label {
            font-weight: bold;
        }

        .edit-form-container input[type="text"], .edit-form-container input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .edit-form-container input[type="submit"] {
            grid-column: span 2;
            background: #2e7d32;
            color: white;
            border: none;
            padding: 10px;
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
    <h2>Librarian Catalog</h2>
    <div class="header">
        <a href="login.php">Log out</a>
        <a href="addBook.php">Add Book</a>
    </div>

    <div class="main-content">
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Publication Year</th>
                        <th>ISBN</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']); ?></td>
                            <td><?= htmlspecialchars($row['author']); ?></td>
                            <td><?= htmlspecialchars($row['year']); ?></td>
                            <td><?= htmlspecialchars($row['isbn']); ?></td>
                            <td class="actions">
                                <a href="?edit_id=<?= $row['id']; ?>">Edit</a>
                                <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to remove this book?');">
                                    <input type="hidden" name="book_id_to_delete" value="<?= $row['id']; ?>">
                                    <button type="submit" name="delete_book" class="remove">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No books found.</p>
        <?php endif; ?>

        <?php
        // Display the edit form if an edit ID is provided in the URL
        if (isset($_GET['edit_id'])) {
            $edit_id = $_GET['edit_id'];
            $edit_query = "SELECT id, title, author, year, isbn FROM books WHERE id = ?";
            $stmt = $mysql->prepare($edit_query);
            $stmt->bind_param("i", $edit_id);
            $stmt->execute();
            $edit_result = $stmt->get_result();
            if ($edit_result->num_rows > 0) {
                $book_to_edit = $edit_result->fetch_assoc();
                ?>
                <div class="edit-form-container">
                    <h4>Edit Book</h4>
                    <form action="" method="post">
                        <input type="hidden" name="book_id" value="<?= htmlspecialchars($book_to_edit['id']); ?>">

                        <label for="title">Book Title:</label>
                        <input type="text" id="title" name="title" value="<?= htmlspecialchars($book_to_edit['title']); ?>" required>

                        <label for="author">Book Author:</label>
                        <input type="text" id="author" name="author" value="<?= htmlspecialchars($book_to_edit['author']); ?>" required>

                        <label for="year">Publication Year:</label>
                        <input type="number" id="year" name="year" value="<?= htmlspecialchars($book_to_edit['year']); ?>" required>

                        <label for="isbn">ISBN:</label>
                        <input type="number" id="isbn" name="isbn" value="<?= htmlspecialchars($book_to_edit['isbn']); ?>" required>

                        <input type="submit" name="edit_book" value="Save Changes">
                    </form>
                </div>
                <?php
            }
            $stmt->close();
        }
        ?>
    </div>
    <?php $mysql->close(); ?>
</body>
</html>