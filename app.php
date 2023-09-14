<?php

class Database
{
    private $host = "127.0.0.1";
    private $username = "root";
    private $password = "root";
    private $dbname = "inventory_management";
    protected $conn;

    public function __construct()
    {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function __destruct()
    {
        $this->conn->close();
    }
}

class Request extends Database
{
    public function createRequest($user, $items)
    {
        $result = null;

        if (!empty($items)) {
            $sql = "INSERT INTO requests (requested_by, requested_on, items) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $date = date('Y-m-d');
            $stmt->bind_param("sss", $user, $date, $items);
            $stmt->execute();
            $result = $this->conn->insert_id;
            $stmt->close();
        }
        return $result;
    }

    public function readRequests()
    {
        return $this->conn->query('SELECT * FROM requests')->fetch_all(MYSQLI_ASSOC);
    }

    public function readRequestByID($ID)
    {
        $data = null;
        $sql = 'SELECT * FROM requests where id = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID);

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $data = $result->fetch_assoc();
            }
        }
        $stmt->close();

        return $data;
    }

    public function updateRequest($id, $items)
    {
        $result = null;

        if (!empty($items)) {
            $sql = "UPDATE requests set items = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("si", $items, $id);
            $result = $stmt->execute();
            $stmt->close();
        }
        return $result;
    }

    public function deleteRequest($id)
    {
        $sql = "DELETE FROM requests WHERE id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}

class Item extends Database
{
    public function readItems($IDs = [])
    {
        $sql = 'SELECT * FROM items';

        if (!empty($IDs)) {
            $sql .= ' WHERE id IN (' . implode(',', $IDs) . ')';
        }
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function getItemTypeMap(): array
    {
        return ['1' => 'Office Supply', '2' => 'Equipment', '3' => 'Furniture'];
    }

}

class Summary extends Database
{
    public function createSummary($requestes)
    {
        $result = null;

        if (!empty($requestes)) {
            $sql = "INSERT INTO summary (requested_by, ordered_on, items) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            if ($stmt) {
                $date = date('Y-m-d');

                foreach ($requestes as $user => $items) {
                    $stmt->bind_param("sss", $user, $date, $items);
                    if ($stmt->execute() === false) {
                        echo "Error: " . $stmt->error;
                        break; // Stop the loop on the first error
                    }
                }

                $result = true;
                $stmt->close();
            } else {
                // Error in preparing the statement
                echo "Error: " . $this->conn->error;
            }
        }
        return $result;
    }

}


//----------------------------------------------------------------
//define
const DATE_FORMAT = 'YYYY-MM-DD';


// App Controller to handle CRUD operations

class App
{

    private $item;
    private $request;
    private $summary;

    public function __construct()
    {
        $this->item = new Item();
        $this->request = new Request();
        $this->summary = new Summary();
    }

    public function run()
    {
        if (($action = $_GET['action'] ?? null) && method_exists($this, $action)) {
            $this->$action();
        }
    }

    private function sendResponse($statusCode, $data)
    {
        http_response_code($$statusCode);
        header("Content-Type: application/json");

        echo json_encode($data);
    }

    private function getItems()
    {
        $this->sendResponse(
            200,
            ['items' => $this->item->readItems(), 'itemTypes' => $this->item->getItemTypeMap()]
        );
    }

    private function getRequests()
    {
        $requests = $this->request->readRequests();
        $allItems = $this->item->readItems();
        $allItems = array_combine(array_column($allItems, 'id'), $allItems);
        $itemAndType = $this->item->getItemTypeMap();

        $responseData = [];
        foreach ($requests as $request) {
            $requestedItems = json_decode($request['items'], true);

            $items = [];
            foreach ($requestedItems as $requestedItem) {
                $items[] = $allItems[$requestedItem[0]]['item'];
            }

            $responseData[] = [
                'id' => $request['id'],
                'user' => $request['requested_by'],
                'items' => implode(', ', $items),
                'type' => $itemAndType[$requestedItems[0][1]]
            ];
        }

        $this->sendResponse(200, ['data' => $responseData]);
    }

    private function addRequest()
    {
        $response = ['message' => 'Unable to add request', 'data' => null];

        if (($user = $_POST['user'] ?? null) && ($items = $_POST['items'] ?? null)) {
            $items = $this->item->readItems($items);

            if (!empty($items)) {

                $formattedItems = null;

                foreach ($items as $item) {
                    $formattedItems[] = [$item['id'], $item['item_type']];
                }

                $requestID = $this->request->createRequest($user, json_encode($formattedItems));

                $response = ['message' => 'Successfully added request', 'data' => $requestID];
            } else {
                $response['message'] = 'Requested items not found';
            }


        }

        $this->sendResponse(200, $response);
    }

    private function updateRequest()
    {
        $response = ['message' => 'Unable to update request', 'data' => null];

        if (($id = $_POST['id'] ?? null) && ($items = $_POST['items'] ?? null)) {
            $items = $this->item->readItems($items);

            if (!empty($items)) {

                $formattedItems = null;

                foreach ($items as $item) {
                    $formattedItems[] = [$item['id'], $item['item_type']];
                }

                $requestID = $this->request->updateRequest($id, json_encode($formattedItems));

                $response = ['message' => 'Successfully updated the request', 'data' => $requestID];
            } else {
                $response['message'] = 'Requested items not found';
            }


        }

        $this->sendResponse(200, $response);
    }

    private function getRequestDetails()
    {

        $this->sendResponse(
            200,
            ['message' => 'Request details', 'data' => $this->request->readRequestByID($_GET['id'])]
        );
    }


    private function deleteRequest()
    {
        $response = ['message' => 'Unable to delete request', 'data' => null];


        if ($id = $_POST['id'] ?? null) {
            $requestDetails = $this->request->readRequestByID($id);

            if (!empty($requestDetails)) {

                $this->request->deleteRequest($id);

                $response = ['message' => 'Successfully deleted the request', 'data' => $id];
            } else {
                $response['message'] = 'Requested details not found';
            }
        }

        $this->sendResponse(200, $response);
    }

    private function orderRequests()
    {
        $response = ['message' => 'Unable to order requests', 'data' => null];

        $requests = $this->request->readRequests();

        if (!empty($requests)) {
            $consolidatedRequests = [];

            $tmpRequest = [];
            foreach ($requests as $request) {
                $items = json_decode($request['items'], true);
                $itemType = $items[0][1];
                $consolidatedItems = array_column($items, 0);


                if (isset($tmpRequest[$request['requested_by']][$itemType])) {
                    $userRequest = $tmpRequest[$request['requested_by']][$itemType];
                    $userRequest[] = $consolidatedItems;

                    $tmpRequest[$request['requested_by']][$itemType] = [$itemType, $userRequest];
                } else {
                    $tmpRequest[$request['requested_by']][$itemType][] = [$itemType, $consolidatedItems];
                }


                $consolidatedRequests[$request['requested_by']] = json_encode(array_values($tmpRequest[$request['requested_by']]));
            }

            $data = $this->summary->createSummary($consolidatedRequests);

            $response = ['message' => 'Successfully ordered requests', 'data' => $consolidatedRequests];

        } else {
            $response['message'] = 'Nothing to order';
        }

        $this->sendResponse(200, $response);
    }

}

//Start the app
$app = new App();
$app->run();
