<?php
// โหลดไฟล์การกำหนดค่าฐานข้อมูล
include_once 'db.php'; 

// ตรวจสอบว่ามีการส่งรหัสสถานที่มาหรือไม่
if(isset($_GET['place_id']) && !empty($_GET['place_id'])) {
    // ดึงรหัสสถานที่จากพารามิเตอร์
    $place_id = $_GET['place_id'];

    // สร้างคำสั่ง SQL เพื่อดึงข้อมูลของสถานที่โรงแรมที่มีรหัสที่ระบุ
    $strSQL = "SELECT * FROM hotels WHERE place_id = '$place_id'";
    $result = $con->query($strSQL);

    // ตรวจสอบว่ามีข้อมูลหรือไม่
    if($result->num_rows > 0) {
        // ดึงข้อมูลแรกที่พบ
        $row = $result->fetch_assoc();

        // ตรวจสอบว่ามีการส่งแบบฟอร์มเพื่ออัปเดตผู้ใช้หรือไม่
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // รับค่าอินพุตที่ซ่อนอยู่
            $place_id = $_POST['place_id'];

            // รับข้อมูลแบบฟอร์มอื่น
            $name = $_POST['name'];
            $address = $_POST['address'];
            $phone_number = $_POST['phone_number'];
            $latitude = $_POST['latitude'];
            $longitude = $_POST['longitude'];
            $first_photo_url = $_POST['first_photo_url'];
            $map_url = $_POST['map_url'];
            $website = $_POST['website'];
            $uploadDate = $_POST['uploadDate'];

            // อัพเดตข้อมูลผู้ใช้ลงในฐานข้อมูล
            $sql = "UPDATE hotels SET name = '$name', address = '$address', phone_number = '$phone_number', latitude = '$latitude', longitude = '$longitude', first_photo_url = '$first_photo_url', map_url = '$map_url', website = '$website', uploadDate = '$uploadDate' WHERE place_id = '$place_id'";
            if(mysqli_query($con, $sql)) {
                echo "อัปเดตบันทึกเรียบร้อยแล้ว.";
                // ส่งผู้ใช้ไปที่หน้า home.php
                header("Location: view.php");
                exit; // ออกจากสคริปต์

            } else {
                echo "ข้อผิดพลาด : ไม่สามารถดำเนินการได้ -> $sql. " . mysqli_error($con);
            }

            // Close connection
            mysqli_close($con);
        }
?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Edit Hotel</title>
            <!-- เรียกใช้ CSS ของ Bootstrap -->
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        </head>
        <body>
        <div class="container">
            <h2>แก้ไขข้อมูล / อัปเดตข้อมูล</h2>
            <form action="" method="POST"> <!-- เปลี่ยน action เป็นว่าง เพื่อให้ส่งไปหน้าเดียวกัน -->
                <input type="hidden" name="place_id" value="<?php echo $row['place_id']; ?>">
                <div class="form-group">
                    <label for="name">ชื่อ:</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $row['name']; ?>">
                </div>
                <div class="form-group">
                    <label for="address">ที่อยู่:</label>
                    <input type="text" class="form-control" id="address" name="address" value="<?php echo $row['address']; ?>">
                </div>
                <div class="form-group">
                    <label for="phone_number">เบอร์โทร:</label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo $row['phone_number']; ?>">
                </div>
                <div class="form-group">
                    <label for="latitude">latitude:</label>
                    <input type="text" class="form-control" id="latitude" name="latitude" value="<?php echo $row['latitude']; ?>">
                </div>
                <div class="form-group">
                    <label for="longitude">longitude:</label>
                    <input type="text" class="form-control" id="longitude" name="longitude" value="<?php echo $row['longitude']; ?>">
                </div>
                <div class="form-group">
                    <label for="first_photo_url">รูปภาพ:</label>
                    <img src="<?php echo $row['first_photo_url']; ?>" class="img-fluid" alt="รูปภาพ">
                    <input type="text" class="form-control" id="first_photo_url" name="first_photo_url" value="<?php echo $row['first_photo_url']; ?>">
                </div>
                <div class="form-group">
                    <label for="map_url">แผนที่:</label>
                    <input type="text" class="form-control" id="map_url" name="map_url" value="<?php echo $row['map_url']; ?>">
                </div>
                <div class="form-group">
                    <label for="website">เว็บไซต์:</label>
                    <input type="text" class="form-control" id="website" name="website" value="<?php echo $row['website']; ?>">
                </div>
                <div class="form-group">
                    <label for="uploadDate">อัปเดตวันที่:</label>
                    <input type="date" class="form-control" id="uploadDate" name="uploadDate" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <!-- ส่วนอื่นๆของฟอร์ม ตามต้องการ -->
                <button type="submit" class="btn btn-primary">อัปเดตข้อมูล</button>
            </form>
        </div>
        </body>
        </html>
<?php
    } else {
        echo "ไม่พบข้อมูลสำหรับรหัสสถานที่ที่ระบุ";
    }
} else {
    echo "ไม่ได้ระบุรหัสสถานที่";
}
?>
