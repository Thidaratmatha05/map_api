<?php
// โหลดไฟล์การกำหนดค่าฐานข้อมูล
include_once 'db.php'; 
?>

<!DOCTYPE html>
<html>
<head>
    <title>แสดงข้อมูล</title>
    <!-- เรียกใช้ CSS ของ Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</head>
<body>

<!-- ใส่ข้อมูลที่ดึงมาจากฐานข้อมูลที่นี่ -->
<?php 
    // ดึงข้อมูลจากตาราง hotels โดยรวมชื่อจังหวัดจากตาราง province
    //$sql = "SELECT * FROM hotels";
    $sql = "SELECT hotels.*, province, nameprovince
    FROM hotels 
    INNER JOIN province_province ON hotels.province = province_province.idprovince";
    $result = $con->query($sql);
    // นับจำนวนแถวที่ได้จากการค้นหา
    $total_rows = mysqli_num_rows($result);
?>

<div class="container">
    <br>
    <?php echo '<p class="text-center"> จำนวนข้อมูลทั้งหมด : '.$total_rows.'</p>'; ?>

    <br>

    <br>
    <h2>ข้อมูลที่ดึงมาจากฐานข้อมูล</h2> 
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <!-- <th>place_id</th> -->
                <th>ชื่อ</th>
                <th>รูปภาพ</th>
                <th>จังหวัด</th>
                <th>วันที่อัพโหลด</th>
                <th>แก้ไข/ลบ</th>
            </tr>
        </thead>
    <tbody> 

<?php
    
    // ตรวจสอบว่ามีข้อมูลหรือไม่
    if ($result->num_rows > 0) {
    // วนลูปแสดงผลข้อมูล
        $count = 1; // กำหนดตัวแปรเพื่อเก็บลำดับของแถว
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $count++ . "</td>";
            // echo "<td>". $row["place_id"]. "</td>";
            echo "<td>". $row["name"]. "</td>";
            echo "<td><img src='". $row["first_photo_url"]. "' style='width: 100px; height: auto;' /></td>";
            echo "<td>". $row["nameprovince"]. "</td>";
            echo "<td>". $row["uploadDate"]. "</td>";
            // เพิ่มปุ่มแก้ไขและลบ
            echo '<td><a href="edit.php?place_id='. $row["place_id"]. '" class="btn btn-primary">แก้ไข</a>
            <a href="delete.php?place_id='. $row["place_id"]. '" class="btn btn-danger">ลบ</a></td>';
            echo "</tr>";
        } 
    } else {
        echo "<tr><td colspan='5'>ไม่พบข้อมูล</td></tr>";
    }
?>
    </tbody>
    </table>
</div>

</body>
</html>
