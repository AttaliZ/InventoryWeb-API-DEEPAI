<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['description'])) {
    $description = trim($_POST['description']);

    if (empty($description)) {
        echo json_encode(["success" => false, "message" => "Description is required."]);
        exit();
    }

    $api_key = "ไม่ให้คีย์หรอกอยากได้ก็ไปหาเอาเอง"; // ใส่ API Key จาก DeepAI
    $api_url = "https://api.deepai.org/api/text2img";

    $data = ["text" => $description];
    $headers = ["api-key: $api_key"];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    curl_close($ch);

    $response_data = json_decode($response, true);

    if (isset($response_data['output_url'])) {
        $image_url = $response_data['output_url'];

        // ดาวน์โหลดและบันทึกภาพ
        $image_data = file_get_contents($image_url);
        $image_name = "generated_" . time() . ".jpg";
        $save_dir = "uploads/cakes/";
        $save_path = $save_dir . $image_name;

        // สร้างโฟลเดอร์ถ้ายังไม่มี
        if (!file_exists($save_dir)) {
            mkdir($save_dir, 0777, true);
        }

        // บันทึกภาพลงโฟลเดอร์
        if (file_put_contents($save_path, $image_data)) {
            // สร้าง URL ที่เข้าถึงได้สำหรับไฟล์ที่บันทึกไว้
            // สมมติว่าไฟล์นี้อยู่ในโฟลเดอร์ public ของเว็บไซต์
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
            $host = $_SERVER['HTTP_HOST'];
            // dirname($_SERVER['REQUEST_URI']) จะได้ path ของไฟล์ generate_cake.php
            $currentPath = rtrim(dirname($_SERVER['REQUEST_URI']), '/\\');
            $saved_url = $protocol . $host . $currentPath . "/" . $save_path;

            echo json_encode([
                "success"    => true,
                "image_url"  => $image_url,
                "saved_path" => $saved_url  // ส่งกลับ URL ที่เข้าถึงได้
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to save image."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Failed to generate image."]);
    }
}
?>
