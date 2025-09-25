<?php
//handles database connection
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

        input[type="text"] {
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
            max-height: 600px; 
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
    </style>
</head>

<body>
    <a href='login.php'>Log out</a>
    <h2>User Page</h2>
    <form method="GET" action="">
        <select name = "filter"> 
            <option value = "Title">Title</option> 
            <option value = "Author">Author</option>
            <option value = "Year">Year</option>
            <option value = "isbn">ISBN</option>
        </select>
        
        <input type="text" name="query" placeholder="Enter title, author, year, or ISBN" required>
        <input type="submit" value="Search">
        <button onclick="window.location.href='userCatalog.php'">Refresh</button>
    </form>
    <br>


    <?php
    $defaultQuery = "SELECT * FROM books";
    $data = $mysql->query($defaultQuery);

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
                $searchQuery = "SELECT * from books WHERE title = $search";
                break;

            case 'isbn':
                $searchQuery = "SELECT * from books WHERE title = $search";
                break;
            }

        $data = $mysql->query($searchQuery);

    }

    if ($data->num_rows > 0) {
        echo "<div class='table-container'>";
        echo "<table>";
        echo "<tr>
                <th> TITLE </th>
                <th> AUTHOR </th>
                <th> YEAR </th>
                <th> ISBN </th>
              </tr>";

        while ($row = $data->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['title'] . "</td>";
            echo "<td>" . $row['author'] . "</td>";
            echo "<td>" . $row['year'] . "</td>";
            echo "<td>" . $row['isbn'] . "</td>";
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
