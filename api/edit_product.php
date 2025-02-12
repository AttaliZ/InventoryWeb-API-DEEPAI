<?php
session_start();
include('connect.php'); // เชื่อมต่อฐานข้อมูล

// เปิด Error Reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// อัปเดตข้อมูลสินค้า
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $cake_id = mysqli_real_escape_string($conn, $_POST['M_ProductID']);
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
    $existing_image = mysqli_real_escape_string($conn, $_POST['existing_image']);

    $image_name = $existing_image; // ใช้รูปเดิมหากไม่มีการอัปโหลดใหม่

    // ตรวจสอบการอัปโหลดรูปภาพ
    if (!empty($_FILES['cake_image']['name'])) {
        $image_tmp = $_FILES['cake_image']['tmp_name'];
        $image_name = "uploads/cakes/" . basename($_FILES['cake_image']['name']);

        // ตรวจสอบประเภทไฟล์ และขนาดไฟล์
        if ($_FILES['cake_image']['size'] > 1000000) {
            echo "The file is too large. Please upload a smaller file.";
            exit();
        }

        // ลบไฟล์เก่าออกก่อน
        if (!empty($existing_image) && file_exists($existing_image)) {
            unlink($existing_image);
        }

        // อัปโหลดรูปภาพใหม่
        if (!move_uploaded_file($image_tmp, $image_name)) {
            echo "Failed to upload image. Please check permissions.";
            exit();
        }
    }

    // คำสั่ง SQL สำหรับอัปเดตข้อมูล
    $update_query = "UPDATE MCake SET 
        M_ProductName='$cake_name', M_Flavor='$flavor', M_WarrantyStartDate='$production_date', 
        M_WarrantyExpiryDate='$expiration_date', M_Manufacturer='$baker', M_CountryOfManufacture='$country_of_origin', 
        M_Price='$price', M_Description='$description', M_StockQuantity='$stock_quantity', 
        M_Category='$category', M_ProductImage='$image_name' 
        WHERE M_ProductID='$cake_id'";

    if (mysqli_query($conn, $update_query)) {
        header("Location: edit_product.php");
        exit();
    } else {
        echo "Error updating product: " . mysqli_error($conn);
    }
}

// ลบข้อมูลสินค้า
if (isset($_GET['delete'])) {
    $cake_id = mysqli_real_escape_string($conn, $_GET['delete']);

    // ดึงชื่อไฟล์ภาพก่อนลบ
    $query_image = "SELECT M_ProductImage FROM MCake WHERE M_ProductID='$cake_id'";
    $result_image = mysqli_query($conn, $query_image);
    $row_image = mysqli_fetch_assoc($result_image);

    if ($row_image) {
        $image_path = $row_image['M_ProductImage'];

        // ลบไฟล์รูปออกจากเซิร์ฟเวอร์
        if (!empty($image_path) && file_exists($image_path)) {
            unlink($image_path);
        }
    }

    // ลบข้อมูลจากฐานข้อมูล
    $delete_query = "DELETE FROM MCake WHERE M_ProductID='$cake_id'";
    if (mysqli_query($conn, $delete_query)) {
        header("Location: edit_product.php");
        exit();
    } else {
        echo "Error deleting product: " . mysqli_error($conn);
    }
}

// ดึงข้อมูลทั้งหมดจากฐานข้อมูล
$query = "SELECT * FROM MCake";
$result = mysqli_query($conn, $query);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - KateBakery</title>
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

        /* Table and form styling */
        .product-table {
            width: 80%;
            margin: 30px auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .product-table th, .product-table td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .product-table th {
            background-color: #ff69b4;
            color: white;
        }

        .product-table td input,
        .product-table td select,
        .product-table td textarea {
            width: 100%;
            padding: 5px;
            margin: 5px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .product-table td input[type="file"] {
            padding: 5px;
            cursor: pointer;
        }

        .submit-btn {
            background-color: #ff69b4;
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            margin-top: 20px;
        }

        .submit-btn:hover {
            background-color: #ff85c0;
        }

        .delete-btn {
            background-color: #ff4d4d;
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #ff6666;
        }
    </style>
</head>
<body>
<div class="header">Welcome to KateBakery</div>

<div class="menu">
    <a href="home.php">Home</a>
    <a href="add_product.php">Add Cake</a>
    <a href="show_cakes.php">Show Cakes</a>
    <a href="edit_product.php">Edit Cake</a>
    <a href="delete_product.php">Delete</a>
    <a href="search_product.php">Search</a>
</div>

<h1 style="text-align: center; color: #ff69b4; font-size: 40px; margin-bottom: 20px;">Edit Product</h1>

<table class="product-table">
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Flavor</th>
            <th>Production Date</th>
            <th>Expiration Date</th>
            <th>Baker</th>
            <th>Country of Origin</th>
            <th>Price (THB)</th>
            <th>Description</th>
            <th>Stock Quantity</th>
            <th>Category</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (mysqli_num_rows($result) > 0) : ?>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <form action="edit_product.php" method="POST" enctype="multipart/form-data">
                    <tr>
                        <td><input type="text" name="cake_name" value="<?php echo htmlspecialchars($row['M_ProductName']); ?>" required></td>
                        <td><input type="text" name="flavor" value="<?php echo htmlspecialchars($row['M_Flavor']); ?>" required></td>
                        <td><input type="date" name="production_date" value="<?php echo htmlspecialchars($row['M_WarrantyStartDate']); ?>" required></td>
                        <td><input type="date" name="expiration_date" value="<?php echo htmlspecialchars($row['M_WarrantyExpiryDate']); ?>" required></td>
                        <td><input type="text" name="baker" value="<?php echo htmlspecialchars($row['M_Manufacturer']); ?>" required></td>
                        <td><input type="text" name="country_of_origin" value="<?php echo htmlspecialchars($row['M_CountryOfManufacture']); ?>" required></td>
                        <td><input type="number" name="price" value="<?php echo htmlspecialchars($row['M_Price']); ?>" step="0.01" required></td>
                        <td><textarea name="description" required><?php echo htmlspecialchars($row['M_Description']); ?></textarea></td>
                        <td><input type="number" name="stock_quantity" value="<?php echo htmlspecialchars($row['M_StockQuantity']); ?>" required></td>
                        <td><input type="text" name="category" value="<?php echo htmlspecialchars($row['M_Category']); ?>" required></td>
                        <td>
                            <img src="<?php echo htmlspecialchars($row['M_ProductImage']); ?>" width="100" height="100">
                            <input type="file" name="cake_image">
                            <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($row['M_ProductImage']); ?>">
                        </td>
                        <td>
                            <input type="hidden" name="M_ProductID" value="<?php echo htmlspecialchars($row['M_ProductID']); ?>">
                            <button type="submit" name="update">Update</button>
                            <a href="edit_product.php?delete=<?php echo $row['M_ProductID']; ?>">Delete</a>
                        </td>
                    </tr>
                </form>
            <?php endwhile; ?>
        <?php endif; ?>
    </tbody>
</table>
</body>
</html>
