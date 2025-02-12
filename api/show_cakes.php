<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShowCake - KateBakery</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;500&display=swap" rel="stylesheet">
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

        .content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .container {
            padding: 40px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .card {
            background-color: white;
            border-radius: 10px;
            width: 300px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: 0.3s;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

        .price {
            font-size: 20px;
            color: #ff69b4;
            font-weight: bold;
        }

        .description {
            color: #666;
            margin: 10px 0;
        }

        .stock {
            font-size: 18px;
            margin-top: 10px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin: 20px 0;
        }

        .pagination a {
            text-decoration: none;
            color: #333;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 50%;
            font-size: 14px;
            transition: 0.3s;
        }

        .pagination a:hover {
            background-color: #ffc0cb;
        }

        .pagination a.active {
            background-color: #ff69b4;
            color: white;
            pointer-events: none;
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


    <div class="content">
        <div class="container">
            <?php
            include('connect.php'); // เชื่อมต่อฐานข้อมูล

            // กำหนดค่าจำนวนสินค้าในแต่ละหน้า
            $items_per_page = 6;
            $current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $offset = ($current_page - 1) * $items_per_page;

            // ดึงสินค้าจากฐานข้อมูลพร้อมแบ่งหน้า
            $query = "SELECT * FROM MCake LIMIT $offset, $items_per_page";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='card'>";

                    // แสดงภาพเค้ก (ลิงก์)
if (!empty($row['M_ProductImage']) && file_exists($row['M_ProductImage'])) {
    echo "<a href='" . htmlspecialchars($row['M_ProductImage']) . "' target='_blank'>";
    echo "<img src='" . htmlspecialchars($row['M_ProductImage']) . "' alt='Cake Image'>";
    echo "</a>";
} else {
    echo "<a href='https://via.placeholder.com/300x200' target='_blank'>";
    echo "<img src='https://via.placeholder.com/300x200' alt='No Image Available'>";
    echo "</a>";
}

                    echo "<h2>" . htmlspecialchars($row['M_ProductName']) . "</h2>";
                    echo "<p class='description'>" . htmlspecialchars($row['M_Description']) . "</p>";
                    echo "<p class='price'>" . number_format($row['M_Price'], 2) . " THB</p>";
                    echo "<p class='stock'>Stock: " . ($row['M_StockQuantity'] > 0 ? $row['M_StockQuantity'] : 'Out of stock') . "</p>";

                    // ปุ่ม Delete หากสินค้าในสต็อกมีมากกว่า 0
                    if ($row['M_StockQuantity'] > 0) {
                        echo "<form method='POST' action='delete_product.php'>
                                <input type='hidden' name='product_id' value='" . $row['M_ProductID'] . "' />
                                <button type='submit' style='background-color: #ff69b4; color: white; border: none; padding: 10px 15px; border-radius: 5px;'>Delete</button>
                              </form>";
                    } else {
                        echo "<p>Out of stock</p>";
                    }

                    echo "</div>";
                }
            } else {
                echo "<p>No cakes available</p>";
            }

            // นับจำนวนสินค้าทั้งหมดเพื่อสร้าง Pagination
            $count_query = "SELECT COUNT(*) AS total FROM MCake";
            $count_result = mysqli_query($conn, $count_query);
            $total_items = mysqli_fetch_assoc($count_result)['total'];
            $total_pages = ceil($total_items / $items_per_page);

            mysqli_close($conn);
            ?>
        </div>

        <!-- Pagination อยู่ข้างล่าง -->
        <div class="pagination">
            <?php
            for ($i = 1; $i <= $total_pages; $i++) {
                $active_class = ($i == $current_page) ? "active" : "";
                echo "<a href='show_cakes.php?page=$i' class='$active_class'>$i</a>";
            }
            ?>
        </div>
    </div>
</body>

</html>
