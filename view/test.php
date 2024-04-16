<?php
// กำหนดข้อมูลที่จะแสดง
$data = array(
    "ข้อมูลที่ 1",
    "ข้อมูลที่ 2",
    "ข้อมูลที่ 3",
    "ข้อมูลที่ 4",
    "ข้อมูลที่ 5",
    // เพิ่มข้อมูลต่อไปตามต้องการ
);

// กำหนดจำนวนรายการต่อหน้า
$items_per_page = 2;

// รับค่าหมายเลขหน้าที่กำหนด
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

// คำนวณหน้าเริ่มต้นและสิ้นสุดของข้อมูลในหน้านั้นๆ
$start = ($current_page - 1) * $items_per_page;
$end = $start + $items_per_page;

// ดึงข้อมูลที่จะแสดงในหน้านั้นๆ
$display_data = array_slice($data, $start, $items_per_page);

// แสดงข้อมูล
foreach ($display_data as $item) {
    echo $item . "<br>";
}

// สร้างลิงก์ในการเลื่อนหน้า
echo "<br>หน้า: ";
for ($i = 1; $i <= ceil(count($data) / $items_per_page); $i++) {
    echo "<a href='?page=$i'>$i</a> ";
}
?>
