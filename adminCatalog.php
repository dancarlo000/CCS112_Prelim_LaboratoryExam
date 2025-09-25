<?php
$servername = "db";
$username = "root";
$password = "rootpassword";
$dbname = "library_db";
$mysql = new mysqli($servername, $username, $password, $dbname);
if ($mysql->connect_error) {
    echo "<h2> Connection Failed </h2>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial scale=1.0">
    <title>User Page - Search Books</title>
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

        a {
            display: inline-block;
            margin-bottom: 20px;
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

        .table-container {
            max-height: 400px;
            overflow-y: auto;
            margin: 0 auto;
            width: 80%;
            border: 2px solid #2e7d32;
            border-radius: 8px;
            background: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th {
            background: #2e7d32;
            color: white;
            padding: 12px;
            position: sticky;
            top: 0; 
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background-color: #e8f5e9;
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

        select {
            border: none;
            background: #2e7d32;
            color: white;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
        }
        option{
            border: none;
        }
        .container {
            display: flex;       
            justify-content: center; 
            gap: 20px;           
        }

        #search{
            background: #ffffff;
            border: 2px solid #2e7d32;
            border-radius: 10px;
            padding: 10px 15px;
            width: 600px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);  
        }

        #addBook {
            background: #ffffff;
            border: 2px solid #2e7d32;
            border-radius: 10px;
            padding: 10px 15px;
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
        <div id = 'search'>
            <a  href='login.php'>Log out</a>
            <h2>Admin Page</h2>
            <form method="GET" action="">
                <select name = "filter">
                    <option value = "Title">Title</option> 
                    <option value = "Author">Author</option>
                    <option value = "Year">Year</option>
                    <option value = "isbn">ISBN</option>
                </select>
                <input type="text" name="query" placeholder="Enter title, author, year, or ISBN" required>
                <input type="submit" value="Search">
                <button onclick="window.location.href='adminCatalog.php'">Refresh</button>
            </form>
        </div>

        <div id='addBook'>
            <h4>Add Book</h4>
            <form method="GET" action="">
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
        //responsible for adding books
        if (isset($_GET["addBook"])) {
            $title = $_GET["title"];
            $author = $_GET["author"];
            $year = $_GET["year"];
            $isbn = $_GET["isbn"];

            $checkquery = "SELECT * FROM books WHERE isbn = '$isbn'";
            $result = $mysql->query($checkquery);

            //checks if the book's isbn is unique
            if ($result->num_rows > 0) {
                echo "<p>Book with ISBN $isbn already exists</p>";
            } else {
                $insertQuery = "INSERT INTO books (title, author, year, isbn)
                                VALUES ('$title', '$author', '$year', '$isbn')";
                if ($mysql->query($insertQuery) === true) {
                    echo "<p>Book added successfully</p>";
                } else {
                    echo "<p>Book addition failed</p>";
                }
            }
        }
    ?>

    <?php
    //responsible for displaying table items
    $defaultQuery = "SELECT * FROM books";
    $data = $mysql->query($defaultQuery);

    //responsible for filtering table items
    if (isset($_GET['query']) && !empty($_GET['query']) && isset($_GET['filter'])) { 
        $search = $_GET['query'];
        $filterOption = $_GET['filter'];
        
        switch ($filterOption){
            case 'Title':
                $searchQuery = "SELECT * from books WHERE title LIKE '%$search%'";
                break;

            case 'Author':
                $searchQuery = "SELECT * from books WHERE author LIKE '%$search%'";
                break;

            case 'Year':
                $searchQuery = "SELECT * from books WHERE year = $search";
                break;

            case 'isbn':
                $searchQuery = "SELECT * from books WHERE isbn = $search";
                break;
            }

        $data = $mysql->query($searchQuery);

    }

    //responsible for displaying filtered table items
    if ($data->num_rows > 0) {
        echo "<div class='table-container'>";
        echo "<table>";
        echo "<tr>
                <th> TITLE </th>
                <th> AUTHOR </th>
                <th> YEAR </th>
                <th> ISBN </th>
                <th> ACTION </th>
              </tr>";

        while ($row = $data->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['title'] . "</td>";
            echo "<td>" . $row['author'] . "</td>";
            echo "<td>" . $row['year'] . "</td>";
            echo "<td>" . $row['isbn'] . "</td>";
            echo "<td> 
                    <a style= 'margin: 0; padding: 0; background: red' id = 'deletebtn' href = 'adminCatalog.php?delete_id=" . $row['isbn'] . "'>
                        <button style= 'background: red; margin: 0'>Delete</button>
                    </a>         
                </td>";
            echo "</tr>";
        }

        echo "</table>";
        echo "</div>";
    } else {
        echo "<h3>No books found.</h3>";
    }
    ?>
</body>
</html>

<?php
//responsible for deletion
    if (isset($_GET['delete_id']) && isset($_GET['confirm'])) {
        $deleteId = (int)$_GET['delete_id'];
        $deleteQuery = $mysql->query("DELETE FROM books WHERE isbn = $deleteId");
        echo "<meta http-equiv='refresh' content='3;url=adminCatalog.php'>";
        echo "<p>Book with ISBN . $deleteId . deleted successfully</p>";
        exit;
    }

    // Ask for confirmation
    if (isset($_GET['delete_id']) && !isset($_GET['confirm'])) {
        $bookId = (int)$_GET['delete_id'];

    echo "<p>Are you sure you want to delete?</p>";
    echo "<a href='adminCatalog.php?delete_id={$bookId}&confirm=1'>Yes, delete</a> | ";
    echo "<a href='adminCatalog.php'>No, cancel</a>";
    exit;
    }
?>
