<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION['dealer_id'])) {
    header("Location: login.php");
    exit();
}

// Get the dealer's name
$dealer_id = $_SESSION['dealer_id'];
$dealerQuery = "SELECT name FROM dealers WHERE id = $dealer_id";
$dealerResult = $conn->query($dealerQuery);

if ($dealerResult->num_rows > 0) {
    $dealerRow = $dealerResult->fetch_assoc();
    $dealer_name = $dealerRow['name'];
} else {
    $dealer_name = 'Unknown Dealer';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <style>
        /* Your styles go here */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        .total-cost {
            margin-top: 20px;
            font-weight: bold;
        }
        
        nav {
            background-color: #333;
            overflow: hidden;
        }

        nav a {
            float: right;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        nav a:hover {
            background-color: #ddd;
            color: black;
        }
    </style>
</head>

<body>

    <nav>
        <a href="index.php">Home</a>
        <a href="logout.php">Logout</a>
        <a href="delete_account.php">Delete Account</a>
    </nav>

    <h1>Sales Report for <?php echo $dealer_name; ?></h1>

    <?php
    // Fetch and display sales data
    $salesQuery = "SELECT sales.id AS sale_id, customers.first_name, customers.last_name, inventory.model, inventory.make, inventory.year, inventory.price, sales.sale_date
                    FROM sales
                    JOIN customers ON sales.customer_id = customers.id
                    JOIN inventory ON sales.inventory_id = inventory.id
                    WHERE sales.dealer_id = $dealer_id";

    $salesResult = $conn->query($salesQuery);

    if ($salesResult->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>Sale ID</th>
                    <th>Customer Name</th>
                    <th>Car Model</th>
                    <th>Car Make</th>
                    <th>Car Year</th>
                    <th>Sale Date</th>
                    <th>Sale Cost</th>
                </tr>";

        $totalSaleCost = 0;

        while ($row = $salesResult->fetch_assoc()) {
            $saleCost = $row['price'];
            $totalSaleCost += $saleCost;

            echo "<tr>
                    <td>{$row['sale_id']}</td>
                    <td>{$row['first_name']} {$row['last_name']}</td>
                    <td>{$row['model']}</td>
                    <td>{$row['make']}</td>
                    <td>{$row['year']}</td>
                    <td>{$row['sale_date']}</td>
                    <td>₹{$saleCost}</td>
                  </tr>";
        }

        echo "</table>";

        // Display total sale cost
        echo "<p class='total-cost'>Total Sale Cost: ₹$totalSaleCost</p>";
    } else {
        echo "No sales data available.";
    }
    ?>

</body>

</html>
