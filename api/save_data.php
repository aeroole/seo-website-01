<?php
// กำหนดให้เบราว์เซอร์รู้ว่าเราจะตอบกลับเป็น JSON
header('Content-Type: application/json');

// 1. รับข้อมูล JSON ที่ส่งมาจาก Client-side
$json_input = file_get_contents('php://input');
$data = json_decode($json_input, true);

if (empty($data) || !isset($data['filename']) || !isset($data['content'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

$filename = $data['filename'];
$content_to_save = $data['content'];

// 2. กำหนดพาธของไฟล์ (สำคัญ: ต้องตรงกับตำแหน่งไฟล์ JSON จริง)
// สมมติว่าไฟล์ JSON อยู่ในโฟลเดอร์ 'data/'
$filepath = './../data/' . basename($filename); 

// 3. ตรวจสอบว่าชื่อไฟล์ถูกต้องและเป็นไฟล์ที่เราอนุญาตให้แก้ไข (ป้องกันการเขียนไฟล์ที่ไม่ต้องการ)
if (!in_array($filename, ['articles.json', 'sliders.json'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['success' => false, 'message' => 'ไม่อนุญาตให้แก้ไขไฟล์นี้']);
    exit;
}

// 4. บันทึกข้อมูล JSON ลงในไฟล์
$json_output = json_encode($content_to_save, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
$result = file_put_contents($filepath, $json_output);

if ($result === false) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถเขียนไฟล์ได้ (ตรวจสอบสิทธิ์การเขียน: CHMOD 777)']);
} else {
    echo json_encode(['success' => true, 'message' => 'บันทึกสำเร็จ']);
}
?>