<?php
// Koneksi ke database
$servername = "localhost";
$username = "u742748395_lms_asn";
$password = "m@ilChip08";
$dbname = "u742748395_lms_asn";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$table = $_GET['table'] ?? ''; // Ambil parameter 'table' dari URL
$data = [];
$sql = "";

switch ($table) {
    case 'attendances':
        $sql = "SELECT 
                    a.id, a.attendance_date, a.status, a.user_id, a.course_id,
                    u.name AS user_name, u.email AS user_email, 
                    c.course_name 
                FROM attendances a
                JOIN users u ON a.user_id = u.id
                JOIN courses c ON a.course_id = c.id
                ORDER BY a.attendance_date DESC";
        break;
    case 'users':
        $sql = "SELECT * FROM users";
        break;
    case 'courses':
        $sql = "SELECT * FROM courses";
        break;
    default:
        // Jika tidak ada parameter tabel, kirimkan pesan error
        http_response_code(400);
        echo json_encode(['error' => 'Parameter tabel tidak valid atau tidak ada.']);
        $conn->close();
        exit();
}

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $data[] = $row;
  }
}

$conn->close();
echo json_encode($data);
?>