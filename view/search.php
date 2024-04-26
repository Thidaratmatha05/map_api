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

    <!-- เรียกใช้ CSS ของ Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <br>
    <h5>พิมพ์ชื่อในการค้นหา</h5>
    <form action="" method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="txtKeyword" class="form-control" placeholder="ค้นหาโรงแรม" value="<?php echo isset($_GET['txtKeyword']) ? $_GET['txtKeyword'] : ''; ?>">
            <div class="input-group-append">
                <button type="submit" class="btn btn-primary">ค้นหา</button>
            </div>
        </div>
    </form>

    <?php
    // ตรวจสอบว่ามีคำค้นหาส่งมาหรือไม่
    if(isset($_GET['txtKeyword']) && !empty($_GET['txtKeyword'])) {
        // สร้างคำสั่ง SQL สำหรับค้นหาโรงแรม
        $strSQL = "SELECT * FROM hotels WHERE (name LIKE '%" . $_GET["txtKeyword"] . "%' OR address LIKE '%" . $_GET["txtKeyword"] . "%')";

        // ทำการ query คำสั่ง SQL
        $result = $con->query($strSQL);

        // ตรวจสอบผลลัพธ์ของคำสั่ง SQL
        if($result === false) {
            echo "Error: " . $con->error;

        } else {

            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if ($result->num_rows > 0) {
                // กำหนดจำนวนรายการต่อหน้า
                $items_per_page = 5;

                // รับค่าหมายเลขหน้าที่กำหนด
                $current_page = isset($_GET['page']) ? $_GET['page'] : 1;

                // คำนวณหน้าเริ่มต้นและสิ้นสุดของข้อมูลในหน้านั้นๆ
                $start = ($current_page - 1) * $items_per_page;
                $end = $start + $items_per_page;

                // ดึงข้อมูลที่จะแสดงในหน้านั้นๆ
                $display_data = $result->fetch_all(MYSQLI_ASSOC);

                // แสดงข้อมูล
                echo '<table class="table table-striped">';
                echo '<thead class="thead-dark">';
                echo '<tr>';
                echo '<th>#</th>';
                // echo '<th>place_id</th>';
                echo '<th>ชื่อ</th>';
                echo '<th>รูปภาพ</th>';
                // echo '<th>จังหวัด</th>';
                echo '<th>วันที่อัพโหลด</th>';
                echo '<th>แก้ไข/ลบ</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                // คำนวณหน้าและข้อมูลที่จะแสดง
                $total_items = count($display_data);
                $start = ($current_page - 1) * $items_per_page;
                $end = min($start + $items_per_page, $total_items);

                for ($i = $start; $i < min($end, count($display_data)); $i++) {
                    $row = $display_data[$i];                                   
                    // แสดงข้อมูลโรงแรมที่ค้นพบตามที่คุณต้องการ
                    echo "<tr>";
                    echo "<td>" . ($i + 1) . "</td>";
                    // echo "<td>" . $row["place_id"] . "</td>";
                    echo "<td>" . $row["name"] . "</td>";
                    echo "<td><img src='" . $row["first_photo_url"] . "' style='width: 100px; height: auto;' /></td>";
                    // echo "<td>" . $row["province"] . "</td>";
                    echo "<td>" . $row["uploadDate"] . "</td>";
                    // เพิ่มปุ่มแก้ไขและลบ
                    echo '<td><a href="edit.php?place_id=' . $row["place_id"] . '" class="btn btn-primary">แก้ไข</a>
                        <a href="delete.php?place_id=' . $row["place_id"] . '" class="btn btn-danger">ลบ</a></td>';
                    echo "</tr>";
                }
                echo '</tbody>';
                echo '</table>';

                // สร้างลิงก์ในการเลื่อนหน้า
                echo "<br>หน้า: ";
                $total_pages = ceil($total_items / $items_per_page);
                for ($page = 1; $page <= $total_pages; $page++) {
                    echo "<a href='?txtKeyword=".$_GET['txtKeyword']."&page=$page'>$page</a> ";
                }
            } else {
                echo "ไม่พบข้อมูลที่ตรงกับคำค้นหา";
            }

        }
    }
    ?>

</div>

</body>
</html>