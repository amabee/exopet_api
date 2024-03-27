<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
class Pet
{

    function login($json)
    {
        include ("conn.php");
        $json = json_decode($json, true);
        $sql = "SELECT `username`, `password` FROM `user` WHERE username = :username AND password = :password";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":username", $json['username']);
        $stmt->bindParam(":password", $json['password']);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($row);
        } else {
            echo json_encode(array('error' => 'Invalid Credentials'));
        }
    }

    function signup($json)
    {
        include ("conn.php");
        $json = json_decode($json, true);

        $check_sql = "SELECT COUNT(*) AS count FROM `user` WHERE username = :username";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bindParam(":username", $json['username']);
        $check_stmt->execute();
        $result = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            echo json_encode(array('error' => 'Username already exists'));
        } else {
            $insert_sql = "INSERT INTO `user` (`username`, `password`) VALUES (:username, :password)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bindParam(":username", $json['username']);
            $insert_stmt->bindParam(":password", $json['password']);
            $insert_stmt->execute();
            if ($insert_stmt->rowCount() > 0) {
                echo json_encode(array('success' => 'User registered successfully'));
            } else {
                echo json_encode(array('error' => 'Failed to register user'));
            }
        }


    }

    function editPet($json)
    {
        include ("conn.php");
        $json = json_decode($json, true);

        // Update pet in the database
        $update_sql = "UPDATE `pets` SET `petname` = :new_petname, `petspecies` = :new_petspecies, `pettype` = :new_pettype WHERE `id` = :pet_id";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bindParam(":pet_id", $json['pet_id']);
        $update_stmt->bindParam(":new_petname", $json['new_petname']);
        $update_stmt->bindParam(":new_petspecies", $json['new_petspecies']);
        $update_stmt->bindParam(":new_pettype", $json['new_pettype']);
        $update_stmt->execute();

        // Check if the update was successful
        if ($update_stmt->rowCount() > 0) {
            echo json_encode(array('success' => 'Pet updated successfully'));
        } else {
            echo json_encode(array('error' => 'Failed to update pet'));
        }
    }

    function deletePet($json)
    {
        include ("conn.php");
        $json = json_decode($json, true);

        // Delete pet from the database
        $delete_sql = "DELETE FROM `pets` WHERE `id` = :pet_id";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bindParam(":pet_id", $json['pet_id']);
        $delete_stmt->execute();

        // Check if the deletion was successful
        if ($delete_stmt->rowCount() > 0) {
            echo json_encode(array('success' => 'Pet deleted successfully'));
        } else {
            echo json_encode(array('error' => 'Failed to delete pet'));
        }
    }

    function addPet($json)
    {
        include ("conn.php");
        $json = json_decode($json, true);

        $insert_sql = "INSERT INTO `pets` (`petname`, `petspecies`, `pettype`) VALUES (:petname, :petspecies, :pettype)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bindParam(":petname", $json['petname']);
        $insert_stmt->bindParam(":petspecies", $json['petspecies']);
        $insert_stmt->bindParam(":pettype", $json['pettype']);
        $insert_stmt->execute();

        if ($insert_stmt->rowCount() > 0) {
            echo json_encode(array('success' => 'Pet added successfully'));
        } else {
            echo json_encode(array('error' => 'Failed to add pet'));
        }
    }
    function getPet()
    {
        include ("conn.php");

        $select_sql = "SELECT * FROM `pets`";
        $select_stmt = $conn->prepare($select_sql);
        $select_stmt->execute();
        $row = $select_stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($row);
    }
    function getLastFed($json)
    {
        include ("conn.php");
        $json = json_decode($json, true);
        // Retrieve last fed data from the database
        $select_sql = "SELECT * FROM `feeding` WHERE `petid` = :pet_id";
        $select_stmt = $conn->prepare($select_sql);
        $select_stmt->bindParam(":pet_id", $json['pet_id']);
        $select_stmt->execute();

        // Check if the pet exists
        if ($select_stmt->rowCount() > 0) {
            $row = $select_stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($row);
        } else {
            echo json_encode(array('error' => 'Pet not found'));
        }
    }

    function addLastFed($json)
    {
        include ("conn.php");
        $json = json_decode($json, true);

        $insert_sql = "INSERT INTO `feeding` (`petid`, `lastfed`, `typeof_food`) VALUES (:petid, :lastfed, :food)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bindParam(":petid", $json['petid']);
        $insert_stmt->bindParam(":lastfed", $json['lastfed']);
        $insert_stmt->bindParam(":food", $json['food']);
        $insert_stmt->execute();

        if ($insert_stmt->rowCount() > 0) {
            echo json_encode(array('success' => 'Last fed data added successfully'));
        } else {
            echo json_encode(array('error' => 'Failed to add last fed data'));
        }
    }

    function addShedding($json)
    {
        include ("conn.php");
        $json = json_decode($json, true);

        $insert_sql = "INSERT INTO `shed` (`petid`, `lastshed`) VALUES (:petid, :lastshed)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bindParam(":petid", $json['petid']);
        $insert_stmt->bindParam(":lastshed", $json['lastshed']);
        $insert_stmt->execute();

        if ($insert_stmt->rowCount() > 0) {
            echo json_encode(array('success' => 'Shedding data added successfully'));
        } else {
            echo json_encode(array('error' => 'Failed to add shedding data'));
        }
    }

    function getSheddingData($json)
    {
        include ("conn.php");
        $json = json_decode($json, true);
        $select_sql = "SELECT `id`, `lastshed` FROM `shed` WHERE `petid` = :pet_id";
        $select_stmt = $conn->prepare($select_sql);
        $select_stmt->bindParam(":pet_id", $json["pet_id"]);
        $select_stmt->execute();

        if ($select_stmt->rowCount() > 0) {
            $shedding_data = $select_stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($shedding_data);
        } else {
            echo json_encode(array('error' => 'Shedding data not found'));
        }
    }
    function addBreedData($json)
    {
        include ("conn.php");
        $json = json_decode($json, true);

        $insert_sql = "INSERT INTO `breed` (`petid`, `bdate`, `cdate`) VALUES (:petid, :bdate, :cdate)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bindParam(":petid", $json['petid']);
        $insert_stmt->bindParam(":bdate", $json['bdate']);
        $insert_stmt->bindParam(":cdate", $json['cdate']);
        $insert_stmt->execute();

        if ($insert_stmt->rowCount() > 0) {
            echo json_encode(array('success' => 'Breed date data added successfully'));
        } else {
            echo json_encode(array('error' => 'Failed to add breed date data'));
        }
    }

    function getBreedData($json)
    {
        include ("conn.php");
        $json = json_decode($json, true);
        $select_sql = "SELECT `id`, `btade`, `cdate` FROM `breed` WHERE `petid` = :pet_id `bdate` = :bdate, `cdate` ' :cdate";
        $select_stmt = $conn->prepare($select_sql);
        $select_stmt->bindParam(":pet_id", $json["pet_id"]);
        $select_stmt->execute();

        if ($select_stmt->rowCount() > 0) {
            $shedding_data = $select_stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($shedding_data);
        } else {
            echo json_encode(array('error' => 'Breeding data not found'));
        }
    }

}



$api = new Pet();

if ($_SERVER["REQUEST_METHOD"] == "GET" || $_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset ($_REQUEST['operation']) && isset ($_REQUEST['json'])) {
        $operation = $_REQUEST['operation'];
        $json = $_REQUEST['json'];

        switch ($operation) {
            case 'login':
                echo $api->login($json);
                break;
            case 'signup':
                echo $api->signup($json);
                break;
            case 'addpet':
                echo $api->addPet($json);
                break;
            case 'updatepet':
                echo $api->editPet($json);
                break;
            case 'deletepet':
                echo $api->deletePet($json);
                break;
            case 'getpets':
                echo $api->getPet();
                break;
            case 'getlastfed':
                echo $api->getLastFed($json);
                break;
            case 'addlastfed':
                echo $api->addLastFed($json);
                break;
            case 'getlastshed':
                echo $api->getSheddingData($json);
                break;
            case 'addlastshed':
                echo $api->addShedding($json);
                break;
                case 'getbreed':
                    echo $api->getSheddingData($json);
                    break;
                case 'addbreed':
                    echo $api->addShedding($json);
                    break;
            default:
                echo json_encode(["error" => "Invalid operation"]);
                break;
        }
    } else {
        echo json_encode(["error" => "Missing parameters"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
?>