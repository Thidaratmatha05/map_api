<?php

// โหลดไฟล์การกำหนดค่าฐานข้อมูล
include_once 'db.php'; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- เรียกใช้ Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .map-link {
            color: blue;
            text-decoration: underline;
            cursor: pointer;
        }
        .map-link:hover {
            color: darkblue;
        }
    </style>
</head>
<body>

<p></p>
<form class="form-horizontal" action="" method="post" name="uploadCsv" enctype="multipart/form-data">
    <div class="container">
    <div class="container text-center">
    <h4> เพิ่มไฟล์ชื่อโรงแรม </h4>
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label"><h5> เลือกไฟล์โรงแรม : </h5></label>
            <div class="col-sm-10">
                <input type="file" class="form-control" name="file" accept=".csv">
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-2 col-form-label"><h5> เลือกจังหวัด : </h5></label>
            <div class="col-sm-10">
                <select class="form-select" name="province_id">
                    <?php
                    // ดึงข้อมูลจังหวัดจากฐานข้อมูล
                    // $sqlSelectprovince = "SELECT * FROM store_category";
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
        </div>

        <input type="hidden" name="upload_time" value="<?php echo date('Y-m-d'); ?>"> <!-- เพิ่มฟิลด์เวลาที่ซ่อนไว้ -->
        <button type="submit" class="btn btn-primary" name="import">Import</button>
        <button class="btn btn-secondary" name="cancel">Cancel</button>
    </div>
    </div>
</form>
<p></p>

</body>
</html>

<?php
// เมื่อมีการส่งไฟล์ CSV มาทางฟอร์ม
if(isset($_POST["import"])){
    $fileName = $_FILES["file"]["tmp_name"];
    $province_id = $_POST["province_id"]; // รับค่าจังหวัดจากฟอร์ม
    $uploadDate = date('Y-m-d'); // รับค่าวันที่และเวลาปัจจุบัน

    // ตรวจสอบขนาดไฟล์
    if($_FILES["file"]["size"] > 0){
       
        // เปิดไฟล์ CSV
        $file = fopen($fileName, "r");

        // ข้ามบรรทัดแรก (ส่วนหัว) ของไฟล์ CSV
        fgetcsv($file);

        // นับจำนวนข้อมูลที่นำเข้า
        $importedDataCount = 0;
        // นับจำนวนข้อมูลที่นำเข้า
        $SaveDataCount = 0;

        // อ่านข้อมูลและแสดงผลข้อความที่อ่านได้
        while(($row = fgetcsv($file)) !== FALSE) {
            // $API_KEY = 'Google Places API Key';
            $API_KEY = 'AIzaSyBbGf_oYjI5qoewIZi4dp5JXvij6Ml4kVg';

            // ตรวจสอบว่าข้อมูลในแต่ละคอลัมน์ไม่มีช่องว่าง
            if (!in_array('', $row, true)) {

                // คอลัมน์ที่ 0 เป็นข้อความที่ต้องการแสดง
                $placeName = $row[0];

                // สร้าง URL สำหรับ Place Search API โดยระบุประเทศไทย
                $search_url = 'https://maps.googleapis.com/maps/api/place/textsearch/json?query=' . urlencode($placeName) . '&type=lodging&region=th&key=' . $API_KEY;

                // ดึงข้อมูลจาก Place Search API
                $search_response = file_get_contents($search_url);
                $search_data = json_decode($search_response, true);

                // ตรวจสอบว่ามีผลลัพธ์หรือไม่
                if ($search_data['status'] == 'OK') {
                    // รับ place_id ของผลลัพธ์แรก
                    $place_id = $search_data['results'][0]['place_id'];

                    // สร้าง URL สำหรับ Place Details API
                    $details_url = 'https://maps.googleapis.com/maps/api/place/details/json?place_id=' . $place_id . '&fields=name,formatted_address,formatted_phone_number,opening_hours,photos,website,geometry&language=th&key=' . $API_KEY;

                    // ดึงข้อมูลจาก Place Details API
                    $details_response = file_get_contents($details_url);
                    $details_data = json_decode($details_response, true);

                    // ตรวจสอบว่ามีผลลัพธ์หรือไม่
                    if ($details_data['status'] == 'OK') {
                        // เพิ่มจำนวนข้อมูลที่นำเข้า
                        $importedDataCount++;

                        // แสดงรายละเอียดสถานที่
                        $name = $details_data['result']['name'];
                        $address = $details_data['result']['formatted_address']?? "ไม่ทราบ";
                        $phone_number = $details_data['result']['formatted_phone_number']?? "ไม่ทราบ";
                        $photos = $details_data['result']['photos']?? array();
                        $website = $details_data['result']['website']?? "ไม่ทราบ";
                        // รับค่าละติจูดและลองจิจูดของสถานที่
                        $latitude = $details_data['result']['geometry']['location']['lat'];
                        $longitude = $details_data['result']['geometry']['location']['lng'];
                        // ผู้เพิ่มข้อมูลโรงแรม
                        $writer = "admin";
                        // รูปภาพ
                        $image = "ไม่มีข้อมูล";

                        // ตรวจสอบว่ามีรูปภาพหรือไม่
                        if (!empty($photos)) {
                            // รับข้อมูลรูปภาพแรก
                            $first_photo_reference = $photos[0]['photo_reference'];
                            $first_photo_url = 'https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference=' . $first_photo_reference . '&key=' . $API_KEY;
                        } else {
                            // ถ้าไม่มีรูปภาพ
                            $first_photo_url = "ไม่มีรูปภาพ";
                        }
                        $map_url = 'https://www.google.com/maps/search/?api=1&query=' . $name;
                        // echo '<p class="card-text"><a href="' . $map_url . '" class="map-link">' . $map_url . '</a></p>';

                        // SQL query สำหรับการบันทึกข้อมูลหรืออัปเดตข้อมูลที่มีอยู่แล้ว
                        $sql = "REPLACE INTO hotels(place_id, name, address, phone_number, latitude, longitude, first_photo_url, map_url, website, uploadDate, image, writer, category_id) 
                        VALUES ('$place_id', '$name', '$address', '$phone_number', '$latitude', '$longitude', '$first_photo_url' , '$map_url', '$website', '$uploadDate', '$image', '$writer', '$province_id')";

                        //SQL query สำหรับตรวจสอบข้อมูลที่มีอยู่ในตาราง
                        $check_duplicate_sql = "SELECT * FROM hotels WHERE place_id = '$place_id'";

                        // ทำการ execute query
                        $check_result = mysqli_query($con, $check_duplicate_sql);

                        // ตรวจสอบว่ามีข้อมูลที่ซ้ำหรือไม่
                        if(mysqli_num_rows($check_result) == 0) {
                            // ถ้าไม่มีข้อมูลที่ซ้ำ ให้ทำการเพิ่มข้อมูล
                            if(mysqli_query($con, $sql)){
                                $SaveDataCount ++;
                                // บันทึกสำเร็จ
                                // echo '<h6><div class="text-center p-3 mb-2 bg-success text-white">บันทึกข้อมูลสำเร็จ: ' . $importedDataCount . ' => ' . $name . '</div></h6>';
                            }
                        }

                    } else {
                        // หากไม่สามารถดึงข้อมูลจาก Place Details API ได้
                        // echo '<p style="text-align: center; font-size: 20px; color: red;">ไม่สามารถดึงข้อมูลจาก Place Details API : '  . $name .' ลำดับที่ : '. $importedDataCount . '</p>';
                    }
                } else {
                    // หากไม่สามารถดึงข้อมูลจาก Place Search API ได้ . $details_data['status'].
                    // echo '<p style="text-align: center; font-size: 20px; color: red;">ไม่สามารถดึงข้อมูลจาก Place Search API : ' . $name .' ลำดับที่ : '. $importedDataCount . '</p>';
                }
            }
        }
        
        // ปิดไฟล์ CSV
        fclose($file);
    }
    echo '<div class="container">';
    // แสดงจำนวนข้อมูลที่นำเข้าเรียบร้อย
    echo '<h6><div class="text-center p-3 mb-2 bg-white">จำนวนข้อมูลที่นำเข้าทั้งหมด : ' . $importedDataCount . ' </div></h6>';
    echo '<h6><div class="text-center p-3 mb-2 bg-success text-white">จำนวนบันทึกได้ : ' . $SaveDataCount . ' บันทึกสำเสร็จ </div></h6>';
    echo '</div>';
}
?>

<!-- เรียกใช้ Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
