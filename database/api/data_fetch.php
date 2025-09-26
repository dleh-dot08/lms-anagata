<?php
// Koneksi ke database
$servername = "localhost";
$username = "u742748395_lms_asn";
$password = "m@ilChip08";
$dbname = "u742748395_lms_asn";

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data
$sql = "SELECT * FROM attendances";
$result = $conn->query($sql);

$data = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Keluarkan data dalam format JSON
header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>