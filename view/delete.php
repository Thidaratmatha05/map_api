<!DOCTYPE html>
<html>
<head>
    <title>Delete Hotel</title>
    <!-- Link to Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #545b62;
            border-color: #545b62;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <?php
    // Include database configuration file
    include_once 'db.php'; 

    // Check if place_id is provided in the URL
    if(isset($_GET['place_id'])) {
        // Prevent SQL Injection using mysqli_real_escape_string function
        $place_id = mysqli_real_escape_string($con, $_GET['place_id']);

        // Select the hotel data to be deleted
        $sql = "SELECT * FROM hotels WHERE place_id = '$place_id'";
        $result = mysqli_query($con, $sql);

        if(mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $name = $row['name'];

            // Display the hotel data to be deleted along with a confirmation form
            echo "<h3>คุณต้องการลบโรงแรม $name หรือไม่?</h3>";
            echo '<form action="" method="post">';
            echo '<input type="hidden" name="place_id" value="' . $place_id . '">';
            echo '<input type="submit" name="confirm_delete" class="btn btn-primary" value="ยืนยัน">';
            // Add a button to go to the homepage
            echo '<a href="view.php" class="btn btn-secondary">Go to Homepage</a>';
            echo '</form>';
            
            // If the user confirms the deletion
            if(isset($_POST['confirm_delete'])) {
                // Prepare SQL query for deletion
                $delete_sql = "DELETE FROM hotels WHERE place_id = '$place_id'";
                // Execute the deletion query
                if(mysqli_query($con, $delete_sql)) {
                    echo "<div class='alert alert-success'>รายการ $name ถูกลบเรียบร้อยแล้ว</div>";
                } else {
                    echo "<div class='alert alert-danger'>เกิดข้อผิดพลาดในการลบรายการ: " . mysqli_error($con) . "</div>";
                }
            }
        } else {
            echo "<div class='alert alert-warning'>ไม่พบข้อมูลโรงแรมที่ต้องการลบ</div>";
        }
    } else {
        // If place_id is not provided in the URL
        echo "<div class='alert alert-warning'>ไม่พบรหัสสถานที่ที่ต้องการลบ</div>";
    }

    // Close database connection
    mysqli_close($con);
    ?>
</div>

</body>
</html>
