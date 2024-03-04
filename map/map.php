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
</head>
<body>

<form class="form-horizontal" action="" method="post" name="uploadCsv" enctype="multipart/form-data">
    <div>
        <label>เพิ่มไฟล์  : </label>
        <input type="file" name="file" accept=".csv">
        <label>เลือกจังหวัด : </label>

        <select name="province">
        <?php
        // ดึงข้อมูลจังหวัดจากฐานข้อมูล
        $sqlSelectprovince = "SELECT * FROM province";
        $resultprovince = mysqli_query($con, $sqlSelectprovince);
        if(mysqli_num_rows($resultprovince) > 0){
            while($rowprovince = mysqli_fetch_array($resultprovince)){
                echo '<option value="' . $rowprovince['province'] . '">' . $rowprovince['province'] . '</option>';
            }
        }
        ?>
        </select>

        <input type="hidden" name="upload_time" value="<?php echo date('Y-m-d'); ?>"> <!-- เพิ่มฟิลด์เวลาที่ซ่อนไว้ -->
        <button type="submit" name="import">Import</button>
        <button name="cancel">cancel</button>

    </div>
</form>

</body>
</html>

<?php
// เมื่อมีการส่งไฟล์ CSV มาทางฟอร์ม
if(isset($_POST["import"])){
    $fileName = $_FILES["file"]["tmp_name"];
    $province = $_POST["province"]; // รับค่าจังหวัดจากฟอร์ม
    $uploadDate = date('Y-m-d'); // รับค่าวันที่และเวลาปัจจุบัน

    // ตรวจสอบขนาดไฟล์
    if($_FILES["file"]["size"] > 0){
       
        // เปิดไฟล์ CSV
        $file = fopen($fileName, "r");

        // ข้ามบรรทัดแรก (ส่วนหัว) ของไฟล์ CSV
        fgetcsv($file);

        // อ่านข้อมูลและแสดงผลข้อความที่อ่านได้
        while(($row = fgetcsv($file)) !== FALSE) {
            $API_KEY = 'Google Places API Key';

            // ตรวจสอบว่าข้อมูลในแต่ละคอลัมน์ไม่มีช่องว่าง
            if (!in_array('', $row, true)) {

                // คอลัมน์ที่ 0 เป็นข้อความที่ต้องการแสดง
                $placeName = $row[0];

                // สร้าง URL สำหรับ Place Search API โดยระบุประเทศไทย
                $search_url = 'https://maps.googleapis.com/maps/api/place/textsearch/json?query=' . urlencode($placeName) . '&type=lodging&region=th&key=' . $API_KEY;

                // สร้าง URL สำหรับ Place Search API
                // $search_url = 'https://maps.googleapis.com/maps/api/place/textsearch/json?query=' . urlencode($placeName) . '&type=lodging&key=' . $API_KEY;

                // ดึงข้อมูลจาก Place Search API
                $search_response = file_get_contents($search_url);
                $search_data = json_decode($search_response, true);

                // ตรวจสอบว่ามีผลลัพธ์หรือไม่
                if ($search_data['status'] == 'OK') {
                    // รับ place_id ของผลลัพธ์แรก
                    $place_id = $search_data['results'][0]['place_id'];

                    // สร้าง URL สำหรับ Place Details API
                    //fields=name,formatted_address,formatted_phone_number,opening_hours,photos,website,types,geometry&language=th
                    $details_url = 'https://maps.googleapis.com/maps/api/place/details/json?place_id=' . $place_id . '&fields=name,formatted_address,formatted_phone_number,opening_hours,photos,website,geometry&language=th&key=' . $API_KEY;

                    // ดึงข้อมูลจาก Place Details API
                    $details_response = file_get_contents($details_url);
                    $details_data = json_decode($details_response, true);

                    // ตรวจสอบว่ามีผลลัพธ์หรือไม่
                    if ($details_data['status'] == 'OK') {
                        // แสดงรายละเอียดสถานที่
                        $name = $details_data['result']['name'];
                        $address = $details_data['result']['formatted_address'];
                        $phone_number = $details_data['result']['formatted_phone_number'];
                        $opening_hours = $details_data['result']['opening_hours']['weekday_text'] ?? array(); // ใช้ ?? เพื่อกำหนดค่าเริ่มต้นว่าเป็นอาร์เรย์ว่าง หากไม่มีข้อมูลเวลาเปิด-ปิด
                        $photos = $details_data['result']['photos']?? array();
                        $website = $details_data['result']['website']?? array();
                        // รับค่าละติจูดและลองจิจูดของสถานที่
                        $latitude = $details_data['result']['geometry']['location']['lat'];
                        $longitude = $details_data['result']['geometry']['location']['lng'];

                        echo '<p>ชื่อ: ' . $name . '</p>';
                        echo '<p>place_id: ' . $place_id . '</p>';
                        echo '<p>จังหวัด: ' . $province . '</p>';
                        echo '<p>ที่อยู่: ' . $address . '</p>';
                        // แสดงละติจูดและลองจิจูด
                        echo 'Latitude: ' . $latitude . '<br>';
                        echo 'Longitude: ' . $longitude. '<br>';
                        echo '<p>เบอร์โทร: ' . $phone_number . '</p>';
                        // Display map https://www.google.com/maps/search/?api=1&query=โรงแรมอยู่ไหน
                        $map_url = 'https://www.google.com/maps/search/?api=1&query=' . $name;
                        echo 'map_url: <a href="' . $map_url . '">' . $map_url . '</a><br>';                       
                        // Display open
                        if (!empty($opening_hours)) {
                            echo 'เวลาเปิด-ปิด:<br>';
                            foreach ($opening_hours as $hour) {
                                echo '- ' . $hour . '<br>';
                            }
                        } else {
                            echo 'เวลาเปิด-ปิด: ไม่ทราบ<br>';
                        }
                        // Display photos
                        if (!empty($photos)) {
                            $first_photo_reference = $photos[0]['photo_reference'];
                            $first_photo_url = 'https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference=' . $first_photo_reference . '&key=' . $API_KEY;
                            echo 'Photo_url: <a href="' . $first_photo_url . '">' . $first_photo_url . '</a><br>';
                            // echo '<img src="' . $first_photo_url . '" alt="Place Photo"><br>';
                        }else {
                            echo 'Photo_url: No photos available for this place.<br>';
                        }
                        // Display website
                        if (!empty($website)){
                            echo 'Website: <a href="' . $website . '">' . $website . '</a><br>'; // Display website as a hyperlink
                        }else{
                            echo 'Website: No Website<br>';
                        }
                        echo '<p> ----------------------------------- </p>';
                    } else {
                        // หากไม่สามารถดึงข้อมูลจาก Place Details API ได้
                        echo "Error fetching place details: " . $details_data['status'];
                    }
                } else {
                    // หากไม่สามารถดึงข้อมูลจาก Place Search API ได้
                    echo "Error fetching place search results: " . $search_data['status'];
                }
            }
        }

        // ปิดไฟล์ CSV
        fclose($file);
    }
}
?>