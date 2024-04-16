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
<form class="form-horizontal" action="" method="post" name="place_name" enctype="multipart/form-data">
<div class="container">
        <div class="container text-center">
            <h4> เพิ่มชื่อโรงแรม </h4>
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label"><h5>ชื่อสถานที่ : </h5></label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="place_name" name="place_name">
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label"><h5>เลือกจังหวัด : </h5></label>
            <div class="col-sm-10">
                <select class="form-select" name="id_province">
                    <?php
                    // ดึงข้อมูลจังหวัดจากฐานข้อมูล
                    // $sqlSelectprovince = "SELECT * FROM store_category";
                    $sqlSelectprovince = "SELECT * FROM province";
                    $resultprovince = mysqli_query($con, $sqlSelectprovince);
                    if(mysqli_num_rows($resultprovince) > 0){
                        while($rowprovince = mysqli_fetch_array($resultprovince)){
                            echo '<option value="' . $rowprovince['nameprovince'] . '">' . $rowprovince['nameprovince'] . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <input type="hidden" name="upload_time" value="<?php echo date('Y-m-d'); ?>"> 
        <!-- เพิ่มฟิลด์เวลาที่ซ่อนไว้ -->
        <button type="submit" class="btn btn-primary" name="save">Save</button>
        <button class="btn btn-secondary" name="cancel">Cancel</button>
</div>
</div>
</form>
<p></p>

</body>
</html>

<?php
// เมื่อมีการส่งไฟล์ CSV มาทางฟอร์ม
if(isset($_POST["save"])){
    $placeName = $_POST["place_name"];
    $province_id = $_POST["id_province"]; // รับค่าจังหวัดจากฟอร์ม
    $uploadDate = date('Y-m-d'); // รับค่าวันที่และเวลาปัจจุบัน

    // $API_KEY = 'Google Places API Key';
    $API_KEY = 'AIzaSyBbGf_oYjI5qoewIZi4dp5JXvij6Ml4kVg';

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

                // SQL query สำหรับการบันทึกข้อมูล
                $sql = "REPLACE INTO hotels (place_id, name, address, phone_number, latitude, longitude, first_photo_url, map_url, website, uploadDate, image, writer, category_id) 
                VALUES ('$place_id', '$name', '$address', '$phone_number', '$latitude', '$longitude', '$first_photo_url' , '$map_url', '$website', '$uploadDate', '$image', '$writer', '$province_id')";
                        
                // SQL query สำหรับตรวจสอบข้อมูลที่มีอยู่ในตาราง
                $check_duplicate_sql = "SELECT * FROM hotels WHERE place_id = '$place_id'";

                echo '<div class="container">';

                // ทำการ execute query
                $check_result = mysqli_query($con, $check_duplicate_sql);


                // ตรวจสอบว่ามีข้อมูลที่ซ้ำหรือไม่
                if(mysqli_num_rows($check_result) == 0) {
                    // ถ้าไม่มีข้อมูลที่ซ้ำ ให้ทำการเพิ่มข้อมูล
                    if(mysqli_query($con, $sql)){
                        // บันทึกสำเร็จ
                        echo '<h6><div class="text-center p-3 mb-2 bg-success text-white">บันทึกข้อมูลสำเร็จ ชื่อ: ' . $name . ' </div></h6>';
                    } else{
                        // หากเกิดข้อผิดพลาดในการ execute query
                        echo '<h6><div class="text-center p-3 mb-2 bg-danger text-white">หากเกิดข้อผิดพลาด</div></h6>';
                    }
                } else {
                    // ถ้ามีข้อมูลที่ซ้ำอยู่แล้ว
                    echo '<h6><div class="text-center p-3 mb-2 bg-danger text-white">ข้อมูลซ้ำ ชื่อ: ' . $name . '</div></h6>';
                }
            } else {
                // หากไม่สามารถดึงข้อมูลจาก Place Details API ได้
                echo '<p style="text-align: center; font-size: 20px; color: red;">ไม่สามารถดึงข้อมูลจาก Place Details API : '  . $name .'</p>';
            }
        } else {
            // หากไม่สามารถดึงข้อมูลจาก Place Search API ได้ . $details_data['status'].
            echo '<p style="text-align: center; font-size: 20px; color: red;">ไม่สามารถดึงข้อมูลจาก Place Search API : ' . $name .'</p>';
        }
}  

echo '</div>';
echo '</div>';
?>

<!-- เรียกใช้ Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
