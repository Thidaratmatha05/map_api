<?php
// โหลดไฟล์การกำหนดค่าฐานข้อมูล
include_once 'db.php'; 

// ตรวจสอบว่ามีการส่งค่า place_id มาหรือไม่
if(isset($_GET['place_id'])) {
    // ใช้ฟังก์ชัน mysqli_real_escape_string เพื่อป้องกัน SQL Injection
    $place_id = mysqli_real_escape_string($con, $_GET['place_id']);

    // สร้างคำสั่ง SQL เพื่อดึงข้อมูลโรงแรมที่ต้องการลบ
    $sql = "SELECT * FROM hotels WHERE place_id = '$place_id'";
    $result = mysqli_query($con, $sql);

    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $name = $row['name'];

        // แสดงข้อมูลโรงแรมที่ต้องการลบ และแสดงแบบฟอร์มยืนยันการลบ
        echo "คุณต้องการลบโรงแรม $name หรือไม่?<br>";
        echo '<form action="" method="post">';
        echo '<input type="hidden" name="place_id" value="' . $place_id . '">';
        echo '<input type="submit" name="confirm_delete" value="ยืนยัน">';
        echo '</form>';
        
        // ถ้าผู้ใช้กดยืนยันการลบ
        if(isset($_POST['confirm_delete'])) {
            // สร้างคำสั่ง SQL เพื่อลบข้อมูล
            $delete_sql = "DELETE FROM hotels WHERE place_id = '$place_id'";
            // ทำการลบข้อมูล
            if(mysqli_query($con, $delete_sql)) {
                echo "รายการ $name ถูกลบเรียบร้อยแล้ว";
            } else {
                echo "เกิดข้อผิดพลาดในการลบรายการ: " . mysqli_error($con);
            }
        }
    } else {
        echo "ไม่พบข้อมูลโรงแรมที่ต้องการลบ";
    }
} else {
    // กรณีที่ไม่ได้รับค่า place_id มา
    echo "ไม่พบรหัสสถานที่ที่ต้องการลบ";
}

// ปิดการเชื่อมต่อฐานข้อมูล
mysqli_close($con);
?>
