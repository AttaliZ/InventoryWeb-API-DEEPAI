<?php
include 'connect.php';  

// รับค่าจากฟอร์ม
$cake_name = $_POST['cake_name'];
$flavor = $_POST['flavor'];
$image_name = $_FILES['cake_image']['name'];
$image_tmp_name = $_FILES['cake_image']['tmp_name'];
$image_folder = "cake_images/" . $image_name;

// ย้ายไฟล์รูปไปยังโฟลเดอร์
if (!is_dir("cake_images")) {
    mkdir("cake_images", 0777, true);
}

if (move_uploaded_file($image_tmp_name, $image_folder)) {
    $sql = "INSERT INTO MCake (M_ProductName, M_Version, M_ProductImage) VALUES ('$cake_name', '$flavor', '$image_folder')";
    if (mysqli_query($conn, $sql)) {
        echo "เพิ่มสินค้าเรียบร้อยแล้ว!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "การอัปโหลดไฟล์ล้มเหลว";
}

mysqli_close($conn);
?>
