<?php
include('connect.php'); // เชื่อมต่อฐานข้อมูล

// เมื่อกดปุ่มลบ
if (isset($_GET['delete'])) {
    $cake_id = mysqli_real_escape_string($conn, $_GET['delete']);
    
    // ดึงชื่อไฟล์ภาพเพื่อลบออกจากเซิร์ฟเวอร์
    $query_image = "SELECT M_ProductImage FROM MCake WHERE M_ProductID = '$cake_id'";
    $result_image = mysqli_query($conn, $query_image);
    $row_image = mysqli_fetch_assoc($result_image);

    if ($row_image) {
        $image_path = $row_image['M_ProductImage'];

        // ตรวจสอบว่าเป็นไฟล์ที่อัปโหลด (ไม่ใช่ URL) และไฟล์มีอยู่จริง
        if (strpos($image_path, 'uploads/cakes/') === 0 && file_exists($image_path)) {
            unlink($image_path); // ลบไฟล์ออกจากโฟลเดอร์
        }
    }

    // คำสั่ง SQL สำหรับลบข้อมูล
    $delete_query = "DELETE FROM MCake WHERE M_ProductID = '$cake_id'";
    if (mysqli_query($conn, $delete_query)) {
        header("Location: delete_product.php"); // รีเฟรชหน้าหลังจากลบ
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
    <title>Delete Product - KateBakery</title>
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
        .product-table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .product-table th, .product-table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        .product-table th {
            background-color: #ff69b4;
            color: white;
        }
        .product-table img {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            object-fit: cover;
            transition: 0.3s;
        }
        .product-table img:hover {
            transform: scale(1.1);
        }
        .product-table a {
            text-decoration: none;
            color: #e74c3c;
            font-weight: bold;
        }
        .product-table a:hover {
            text-decoration: underline;
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
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php if (mysqli_num_rows($result) > 0) : ?>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['M_ProductName']); ?></td>
                    <td><?php echo htmlspecialchars($row['M_Flavor']); ?></td>
                    <td><?php echo htmlspecialchars($row['M_WarrantyStartDate']); ?></td>
                    <td><?php echo htmlspecialchars($row['M_WarrantyExpiryDate']); ?></td>
                    <td><?php echo htmlspecialchars($row['M_Manufacturer']); ?></td>
                    <td><?php echo htmlspecialchars($row['M_CountryOfManufacture']); ?></td>
                    <td><?php echo number_format($row['M_Price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['M_Description']); ?></td>
                    <td><?php echo $row['M_StockQuantity']; ?></td>
                    <td><?php echo htmlspecialchars($row['M_Category']); ?></td>
                    <td>
                        <?php
                        $image_path = htmlspecialchars($row['M_ProductImage']);
                        
                        // แสดงเฉพาะรูปที่อยู่ใน uploads/cakes/
                        if (strpos($image_path, 'uploads/cakes/') === 0 && file_exists($image_path)) {
                            echo "<a href='$image_path' target='_blank'>
                                    <img src='$image_path' alt='Cake Image'>
                                  </a>";
                        } else {
                            echo "<a href='https://via.placeholder.com/100x100' target='_blank'>
                                    <img src='https://via.placeholder.com/100x100' alt='No Image Available'>
                                  </a>";
                        }
                        ?>
                    </td>
                    <td>
                        <a href="?delete=<?php echo $row['M_ProductID']; ?>" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else : ?>
            <tr><td colspan="12">No products found</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
