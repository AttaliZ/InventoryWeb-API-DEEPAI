<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

include('connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์ม
    $cake_name = mysqli_real_escape_string($conn, $_POST['cake_name']);
    $flavor = mysqli_real_escape_string($conn, $_POST['flavor']);
    $production_date = mysqli_real_escape_string($conn, $_POST['production_date']);
    $expiration_date = mysqli_real_escape_string($conn, $_POST['expiration_date']);
    $baker = mysqli_real_escape_string($conn, $_POST['baker']);
    $country_of_origin = mysqli_real_escape_string($conn, $_POST['country_of_origin']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $stock_quantity = mysqli_real_escape_string($conn, $_POST['stock_quantity']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);

    $image_name = $_FILES['cake_image']['name'];
    $image_tmp_name = $_FILES['cake_image']['tmp_name'];
    $image_size = $_FILES['cake_image']['size'];
    $image_error = $_FILES['cake_image']['error'];
    
    $upload_dir = 'uploads/cakes/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if ($image_error === 0) {
        $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
        $image_new_name = uniqid('cake_', true) . '.' . $image_ext;
        $image_destination = $upload_dir . $image_new_name;

        if (move_uploaded_file($image_tmp_name, $image_destination)) {
            // บันทึกลงฐานข้อมูล
            $query = "INSERT INTO MCake (
                M_ProductName, 
                M_Flavor, 
                M_WarrantyStartDate, 
                M_WarrantyExpiryDate, 
                M_Manufacturer, 
                M_CountryOfManufacture, 
                M_Price, 
                M_Description, 
                M_StockQuantity, 
                M_Category, 
                M_ProductImage
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 
                "ssssssdssss",
                $cake_name,
                $flavor,
                $production_date,
                $expiration_date,
                $baker,
                $country_of_origin,
                $price,
                $description,
                $stock_quantity,
                $category,
                $image_destination
            );

            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Cake added successfully!'); window.location = 'show_cakes.php';</script>";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Failed to upload the image.";
        }
    } else {
        echo "Error uploading image.";
    }
}

mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Cake - KateBakery</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .header {
            background-color: #ff69b4;
            padding: 20px;
            text-align: center;
            color: white;
            font-size: 24px;
        }

        .menu {
            display: flex;
            justify-content: center;
            background-color: white;
            border-bottom: 2px solid #ddd;
            padding: 10px;
        }

        .menu a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            padding: 10px 20px;
            transition: background-color 0.3s;
            border-radius: 5px;
        }

        .menu a:hover {
            background-color: #ffc0cb;
        }

        .container {
            text-align: center;
            padding: 40px 20px;
        }

        h1 {
            margin-bottom: 20px;
            font-size: 36px;
        }

        p {
            font-size: 18px;
            margin-bottom: 30px;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #ff69b4;
        }
        form input, form textarea, form button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        form button {
            background-color: #ff69b4;
            color: white;
            border: none;
            cursor: pointer;
        }
        form button:hover {
            background-color: #ff85c0;
        }
    </style>
</head>
<body>
<div class="header">
        <?php echo "Welcome to KateBakery"; ?>
    </div>

    <div class="menu">
        <a href="home.php">Home</a>
        <a href="add_product.php">Add Cake</a>
        <a href="show_cakes.php">Show Cakes</a>
        <a href="edit_product.php">Edit Cake</a>
        <a href="delete_product.php">Delete</a>
        <a href="search_product.php">Search</a>
    </div>
    <div class="container">
        <h2>Add New Cake</h2>
        <form action="add_product.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="cake_name" placeholder="Cake Name" required>
            <input type="text" name="flavor" placeholder="Flavor" required>
            <input type="date" name="production_date" required>
            <input type="date" name="expiration_date" required>
            <input type="text" name="baker" placeholder="Baker" required>
            <input type="text" name="country_of_origin" placeholder="Country of Origin" required>
            <input type="number" name="price" placeholder="Price" step="0.01" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <input type="number" name="stock_quantity" placeholder="Stock Quantity" required>
            <input type="text" name="category" placeholder="Category" required>
            <input type="file" name="cake_image" accept="image/*" required>
            <button type="submit">Add Cake</button>
        </form>
    </div>
</body>
</html>
