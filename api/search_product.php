<?php
include('connect.php'); // เชื่อมต่อฐานข้อมูล

// กำหนดตัวแปรสำหรับการค้นหา
$search_id = "";
$search_query = "";
$search_results = [];
$message = "";

// หากยังไม่ได้ค้นหา ให้แสดงสินค้าทั้งหมด
$sql = "SELECT * FROM MCake";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $search_results[] = $row;
    }
}

// ตรวจสอบการส่งฟอร์มสำหรับการค้นหา
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // ค้นหาตาม Product ID
    if (!empty($_GET['search_id'])) {
        $search_id = mysqli_real_escape_string($conn, $_GET['search_id']);

        $sql = "SELECT * FROM MCake WHERE M_ProductID = '$search_id'";
        $result = mysqli_query($conn, $sql);

        $search_results = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $search_results[] = $row;
            }
        } else {
            $message = "No product found with ID '$search_id'";
        }
    }

    // ค้นหาแบบทั่วไป
    if (!empty($_GET['search_query'])) {
        $search_query = mysqli_real_escape_string($conn, $_GET['search_query']);

        $sql = "SELECT * FROM MCake WHERE 
            M_ProductID LIKE '%$search_query%' OR
            M_ProductName LIKE '%$search_query%' OR
            M_Flavor LIKE '%$search_query%' OR
            M_Category LIKE '%$search_query%' OR
            M_Description LIKE '%$search_query%' OR
            M_Price LIKE '%$search_query%' OR
            M_Manufacturer LIKE '%$search_query%' OR
            M_CountryOfManufacture LIKE '%$search_query%'";

        $result = mysqli_query($conn, $sql);

        $search_results = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $search_results[] = $row;
            }
        } else {
            $message = "No products found matching '$search_query'";
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Product - KateBakery</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
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
            padding: 10px;
            background-color: white;
            border-bottom: 2px solid #ddd;
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

        .search-container {
            text-align: center;
            padding: 50px 20px;
        }

        .search-box {
            max-width: 600px;
            margin: auto;
            display: flex;
            flex-wrap: wrap;
            background-color: white;
            border-radius: 50px;
            padding: 10px 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        input[type="text"],
        input[type="number"] {
            flex-grow: 1;
            border: none;
            outline: none;
            padding: 15px;
            border-radius: 50px;
            font-size: 18px;
            margin: 5px;
        }

        button[type="submit"] {
            background-color: #ff69b4;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 18px;
            margin: 5px;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #ff85c0;
        }

        .results-container {
            margin-top: 40px;
        }

        .product-card {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .product-card img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
        }

        .product-details {
            flex-grow: 1;
        }

        .product-details h3 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .product-details p {
            margin: 5px 0;
            color: gray;
        }

        .price {
            font-size: 20px;
            color: #f1c40f;
            font-weight: bold;
        }

        .no-results {
            text-align: center;
            font-size: 20px;
            color: gray;
            margin-top: 40px;
        }
    </style>
</head>

<body>
    <div class="header">
        Search Product - KateBakery
    </div>

    <div class="menu">
        <a href="home.php">Home</a>
        <a href="add_product.php">Add Cake</a>
        <a href="show_cakes.php">Show Cakes</a>
        <a href="edit_product.php">Edit Cake</a>
        <a href="delete_product.php">Delete</a>
        <a href="search_product.php">Search</a>
    </div>

    <div class="search-container">
        <h1>Find Your Favorite Cake</h1>
        <form method="GET" action="search_product.php" class="search-box">
            <input type="number" name="search_id" placeholder="Enter cake ID..." value="<?php echo htmlspecialchars($search_id); ?>">
            <input type="text" name="search_query" placeholder="Enter product name, price, or category..." value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="results-container">
        <?php if (!empty($search_results)) : ?>
            <?php foreach ($search_results as $cake) : ?>
                <div class="product-card">
                    <?php
                    $image_path = htmlspecialchars($cake['M_ProductImage']);
                    
                    // ตรวจสอบว่าไฟล์รูปมีอยู่จริงในโฟลเดอร์ uploads/cakes/
                    if (strpos($image_path, 'uploads/cakes/') === 0 && file_exists($image_path)) {
                        echo "<img src='$image_path' alt='Cake Image'>";
                    } else {
                        echo "<img src='https://via.placeholder.com/150' alt='No Image Available'>";
                    }
                    ?>
                    <div class="product-details">
                        <h3><?php echo htmlspecialchars($cake['M_ProductName']); ?></h3>
                        <p><strong>ID:</strong> <?php echo htmlspecialchars($cake['M_ProductID']); ?></p>
                        <p><strong>Flavor:</strong> <?php echo htmlspecialchars($cake['M_Flavor']); ?></p>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($cake['M_Category']); ?></p>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($cake['M_Description']); ?></p>
                        <p><strong>Manufacturer:</strong> <?php echo htmlspecialchars($cake['M_Manufacturer']); ?></p>
                        <p><strong>Country:</strong> <?php echo htmlspecialchars($cake['M_CountryOfManufacture']); ?></p>
                        <p class="price"><strong>฿<?php echo number_format($cake['M_Price'], 2); ?></strong></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p class="no-results"><?php echo $message ?? "No products found"; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
