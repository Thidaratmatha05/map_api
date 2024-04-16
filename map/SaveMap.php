<?php
// โหลดไฟล์การกำหนดค่าฐานข้อมูล
include_once 'db.php'; 

?>

<!DOCTYPE html>
<html>
<head>
    <title>บันทึกข้อมูล</title>
    <!-- เรียกใช้ Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <h1 class="mt-5">บันทึกข้อมูล</h1>

    <form action="" method="post" class="mt-4">
        <div class="mb-3">
            <label for="place_id" class="form-label">Place ID:</label>
            <input type="text" id="place_id" name="place_id" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">ชื่อ:</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">ที่อยู่:</label>
            <input type="text" id="address" name="address" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="phone_number" class="form-label">เบอร์โทรศัพท์:</label>
            <input type="text" id="phone_number" name="phone_number" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="latitude" class="form-label">ละติจูด:</label>
            <input type="text" id="latitude" name="latitude" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="longitude" class="form-label">ลองจิจูด:</label>
            <input type="text" id="longitude" name="longitude" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="first_photo_url" class="form-label">URL รูปภาพ:</label>
            <input type="text" id="first_photo_url" name="first_photo_url" class="form-control">
        </div>
        <div class="mb-3">
            <label for="website" class="form-label">เว็บไซต์:</label>
            <input type="text" id="website" name="website" class="form-control">
        </div>
        <div class="mb-3">
            <label for="province_id" class="form-label">เลือกจังหวัด :</label>
            <select class="form-select" name="province_id">
                <?php
                // ดึงข้อมูลจังหวัดจากฐานข้อมูล
                $sqlSelectprovince = "SELECT * FROM province";
                $resultprovince = mysqli_query($con, $sqlSelectprovince);
                if(mysqli_num_rows($resultprovince) > 0){
                    while($rowprovince = mysqli_fetch_array($resultprovince)){
                        echo '<option value="' . $rowprovince['idprovince'] . '">' . $rowprovince['nameprovince'] . '</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <input type="submit" value="บันทึกข้อมูล" name="submit" class="btn btn-primary">
        </div>
    </form>
</div>

</body>
</html>


<?php
// ตรวจสอบว่ามีการส่งข้อมูลแบบ POST มาหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากแบบฟอร์ม
    $place_id = $_POST['place_id'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $first_photo_url = $_POST['first_photo_url'];
    $map_url = 'https://www.google.com/maps/search/?api=1&query=' . $name;
    $website = $_POST['website'];
    $uploadDate = date('Y-m-d');
    $image ="image";
    $writer = "admin";
    $province_id = $_POST['province_id'];

    // เตรียมคำสั่ง SQL
    $sql = "INSERT INTO hotels (place_id, name, address, phone_number, latitude, longitude, first_photo_url, map_url, website, uploadDate, image, writer, province) 
    VALUES ('$place_id', '$name', '$address', '$phone_number', '$latitude', '$longitude', '$first_photo_url' , '$map_url', '$website', '$uploadDate', '$image', '$writer', '$province_id')";

    if (mysqli_query($con, $sql)) {
        echo "บันทึกข้อมูลสำเร็จ";
    } else {
        echo "Error: บันทึกข้อมูลไม่ได้ " . $sql . "<br>" . $conn->error;
    }

}
?>
