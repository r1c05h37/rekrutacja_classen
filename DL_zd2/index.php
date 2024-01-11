<?php
include('sql.php');

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Get data from the request
$json = file_get_contents("php://input");
$data = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $json), true );;

// Switch based on the request method
switch ($method) {
    case 'GET':
        // Handle GET request
        $sql = "SELECT * FROM restapi";
        $result = $db->query($sql);
        
        if ($result->num_rows > 0) {
            $rows = array();
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            echo json_encode($rows);
        } else {
            echo json_encode([]);
        }
        break;
        
    case 'POST':
        // Handle POST request
        $imie = $data['imie'];
        $nazwisko = $data['nazwisko'];

        if(!(isset($imie)) || !(isset($nazwisko)) || !(json_last_error() == 0)){
            break;
        }

        $sql = "INSERT INTO restapi (imie, nazwisko) VALUES ('$imie', '$nazwisko')";
        
        if ($db->query($sql) === TRUE) {
            echo json_encode(["message" => "Dodano!"]);
        } else {
            echo json_encode(["error" => "Error: " . $sql . "<br>" . $db->error]);
        }
        break;
        
    case 'PUT':
        // Handle PUT request
        $id = $_GET['id'];
        $imie = $data['imie'];
        $nazwisko = $data['nazwisko'];

        $sql = "UPDATE restapi SET imie='$imie', nazwisko='$nazwisko' WHERE id=$id";
        
        if ($db->query($sql) === TRUE) {
            echo json_encode(["message" => "Zmieniono!"]);
        } else {
            echo json_encode(["error" => "Error updating record: " . $db->error]);
        }
        break;
        
    case 'DELETE':
        // Handle DELETE request
        $id = $_GET['id'];

        $sql = "DELETE FROM restapi WHERE id=$id";
        
        if ($db->query($sql) === TRUE) {
            echo json_encode(["message" => "Zniszczono!"]);
        } else {
            echo json_encode(["error" => "Error deleting record: " . $db->error]);
        }
        break;
}

// Close the database connection
$db->close();

?>