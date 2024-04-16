
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บันทึกข้อมูล</title>
</head>
<body>
    <h2>บันทึกข้อมูล</h2>
    <form action="" method="POST">
        <label for="name">ชื่อ:</label>
        <input type="text" id="name" name="name"><br><br>
        <label for="email">อีเมล:</label>
        <input type="email" id="email" name="email"><br><br>
        <button type="submit">บันทึก</button>
    </form>
</body>
</html>
<?php
include_once 'db.php'; 

if (isset($_POST["name"])&& isset($_POST['email'])) {

// รับค่าจากฟอร์ม
$name = $_POST['name'];
$email = $_POST['email'];

// เตรียมคำสั่ง SQL
$sql = "INSERT INTO users(name, email)VALUES('$name', '$email')";

// ทำการบันทึกข้อมูล
if (mysqli_query($conn, $sql)) {
    echo "บันทึกข้อมูลเรียบร้อยแล้ว";
} else {
    echo "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . mysqli_error($con);
}

// ปิดการเชื่อมต่อ
mysqli_close($con);
}
?>
