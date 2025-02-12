<?php
include('connect.php');  

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image'])) {
    $image_path = trim($_POST['image']);

    if (empty($image_path)) {
        echo json_encode(["success" => false, "message" => "No image path provided."]);
        exit();
    }

    // ค่าเริ่มต้นสำหรับสินค้าใหม่
    $product_name = "AI Cake";
    $flavor = "Default";
    $price = 199.99;
    $description = "AI-generated cake";
    $stock_quantity = 10;
    $category = "AI Cakes";
    $manufacturer = "KateBakery";
    $country = "Thailand";
    $warranty_start = date("Y-m-d");
    $warranty_expiry = date("Y-m-d", strtotime("+30 days"));

    $query = "INSERT INTO MCake 
        (M_ProductName, M_Flavor, M_WarrantyStartDate, M_WarrantyExpiryDate, 
        M_Manufacturer, M_CountryOfManufacture, M_Price, M_Description, 
        M_StockQuantity, M_Category, M_ProductImage) 
        VALUES 
        ('$product_name', '$flavor', '$warranty_start', '$warranty_expiry', 
        '$manufacturer', '$country', '$price', '$description', 
        '$stock_quantity', '$category', '$image_path')";

    if (mysqli_query($conn, $query)) {
        echo json_encode(["success" => true, "message" => "Cake added to database successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error: " . mysqli_error($conn)]);
    }

    mysqli_close($conn);
}
?>
