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

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_car'])) {
        $model = $_POST['model'];
        $make = $_POST['make'];
        $year = $_POST['year'];
        $quantity = $_POST['quantity'];
        $price = $_POST['price'];

        // Insert new car into Inventory table
        $insertCarQuery = "INSERT INTO inventory (model, make, year, quantity, price)
                           VALUES ('$model', '$make', '$year', $quantity, $price)";

        if ($conn->query($insertCarQuery) === TRUE) {
            echo "<script>alert('Car added successfully.');</script>";
        } else {
            echo "Error adding car: " . $conn->error;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Automobile Sales System</title>
    <style>
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
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        form {
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
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
        <a href="logout.php">Logout</a>
        <a href="delete_account.php">Delete Account</a>
        <a href="sales_report.php">Sales</a>
    </nav>
    
    <h1>Welcome, Dealer Name: <?php echo $dealer_name; ?></h1>

    <?php
// Check if the user is logged in
if (!isset($_SESSION['dealer_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_car'])) {
        // Process adding a new car to inventory
        $model = $_POST['model'];
        $make = $_POST['make'];
        $year = $_POST['year'];
        $quantity = $_POST['quantity'];
        $price = $_POST['price'];
        $insertCarQuery = "INSERT INTO inventory (model, make, year, quantity, price)
                           VALUES ('$model', '$make', '$year', $quantity, $price)";

        if ($conn->query($insertCarQuery) === TRUE) {
            echo "<script>alert('Car added successfully.');</script>";
        } else {
            echo "Error adding car: " . $conn->error;
        }        
    } elseif (isset($_POST['sell_car'])) {
        // Process selling a car from inventory
        if (isset($_POST['id_to_sell'])) {
            $idToSell = $_POST['id_to_sell'];

            // Display a form to gather customer details
            echo "<h2>Customer Details</h2>";
            echo "<form method='post' action=''>
                    <label for='first_name'>First Name:</label>
                    <input type='text' name='first_name' required><br>

                    <label for='last_name'>Last Name:</label>
                    <input type='text' name='last_name' required><br>

                    <label for='email'>Email:</label>
                    <input type='email' name='email' required><br>

                    <input type='hidden' name='car_id' value='$idToSell'>

                    <input type='submit' name='finalize_sale' value='Finalize Sale'>
                  </form>";
        } else {
            echo "Please enter the ID of the car to sell.";
        }
    } elseif (isset($_POST['finalize_sale'])) {
        // Process finalizing the sale (update customers and sales databases)
        if (isset($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['car_id'])) {
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $email = $_POST['email'];
            $car_id = $_POST['car_id'];

            // Insert new customer into Customers table
            $insertCustomerQuery = "INSERT INTO customers (first_name, last_name, email)
                                    VALUES ('$first_name', '$last_name', '$email')";

            if ($conn->query($insertCustomerQuery) === TRUE) {
                // Get the customer ID
                $customer_id = $conn->insert_id;

                // Update the Sales table
                $updateSalesQuery = "INSERT INTO sales (dealer_id, customer_id, inventory_id, sale_date)
                                     VALUES ($dealer_id, $customer_id, $car_id, CURDATE())";

                if ($conn->query($updateSalesQuery) === TRUE) {
                    // Update the Inventory table (reduce quantity)
                    $updateInventoryQuery = "UPDATE inventory SET quantity = quantity - 1 WHERE id = $car_id AND quantity > 0";

                    if ($conn->query($updateInventoryQuery) === TRUE) {
                        echo "Sale completed successfully.";
                    } else {
                        echo "Error updating Inventory: " . $updateInventoryQuery . "<br>" . $conn->error;
                    }
                } else {
                    echo "Error updating Sales: " . $updateSalesQuery . "<br>" . $conn->error;
                }
            } else {
                echo "Error inserting customer: " . $insertCustomerQuery . "<br>" . $conn->error;
            }
        } else {
            echo "Please fill in all the customer details.";
        }
    }
}

// Display Inventory
echo "<h2>Inventory</h2>";
$query = "SELECT * FROM inventory where quantity > 0";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Model</th>
                <th>Manufacturer</th>
                <th>Year</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['model']}</td>
                <td>{$row['make']}</td>
                <td>{$row['year']}</td>
                <td>{$row['quantity']}</td>
                <td>₹{$row['price']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No cars in the inventory.";
}

// Form to add new car to Inventory
echo "<h2>Add New Car</h2>";
echo "<form method='post' action=''>
        <label for='model'>Model:</label>
        <input type='text' name='model' required><br>

        <label for='make'>Manufacturer:</label>
        <input type='text' name='make' required><br>

        <label for='year'>Year:</label>
        <input type='text' name='year' required><br>

        <label for='quantity'>Quantity:</label>
        <input type='text' name='quantity' required><br>

        <label for='price'>Price(₹):</label>
        <input type='text' name='price' required><br>

        <input type='submit' name='add_car' value='Add Car'>
      </form>";

// Form to sell a car
echo "<h2>Sell Car</h2>";
echo "<form method='post' action=''>
        <label for='id_to_sell'>ID to Sell:</label>
        <input type='text' name='id_to_sell' required><br>

        <input type='submit' name='sell_car' value='Sell Car'>
      </form>";

// Close the database connection
$conn->close();
?>


</body>

</html>