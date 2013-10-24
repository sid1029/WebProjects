<?php
require_once 'API.class.php';

class UberAPI extends API
{
    public function __construct($request, $origin) {
        parent::__construct($request);

        /*
        * username : uberappuser
        * db : uberapp
        */
    }

    /**
     * Get a list of locations GET locations/
	 * Create a location POST locations/
	 * Get a location GET locations/:id/
	 * Update a location PUT locations/:id/
	 * Delete a location DELETE locations/:id/
     */
    protected function locations()
    {
    	$mysqlLink = new mysqli("localhost", "uberappuser", "ubersecretpass", "uberapp");
		if ($mysqlLink->connect_errno) {
		    echo "Failed to connect to MySQL: (" . $mysqlLink->connect_errno . ") " . $mysqlLink->connect_error;
		}

        switch ($this->method) {
        	case 'GET':
        		// GET request for a specific ID
		        if (count($this->args) > 0)
	        	{
	        		$query = "SELECT * FROM favlocs WHERE id = '" . $this->args[0] . "'";
	        		$res = $mysqlLink->query($query);
	        		$location;
					if ($row = $res->fetch_assoc()) {
					    $location = array("id" => $row['id'], "name" => $row['name'], "lat" => $row['lat'], "lng" => $row['lng'], "address" => $row['address']);
					}
					$res->free();
					$mysqlLink->close();
					return $location;
	        	}
	        	else
	        	{
	        		$res = $mysqlLink->query("SELECT * FROM favlocs");
	        		$locations = array();
					while ($row = $res->fetch_assoc()) {
					    array_push($locations, array("id" => $row['id'], "name" => $row['name'], "lat" => $row['lat'], "lng" => $row['lng'], "address" => $row['address']));
					}
					return $locations;
	        	}
        		break;

        	case 'POST':
        		$location = null;
        		$insertID = null;

        		// Generate a UUID from mysql.
	        	$idRes = $mysqlLink->query($mysqlLink->real_escape_string("SELECT UUID()"));
	        	if ($idRes)
	        		$insertID = $idRes->fetch_array()[0];

	        	// insert row using the new UUID
	        	$query = "INSERT INTO favlocs (id,name,lat,lng,address) VALUES ('". $insertID ."', '".$this->request['name']."', '".$this->request['lat']."', '".$this->request['lng']."', '".$this->request['address']."');";
	        	if ($mysqlLink->query($query))
	        		$this->request['id'] = $insertID;
	        	return $location = $this->request;
        		break;

    		case 'PUT':
    			if (count($this->args) > 0)
	        	{
	        		$locations[$this->args[0]] = json_decode($_PUT['location'], true);
	        	}
	        	return $locations[$this->args[0]];
    			break;

    		case 'DELETE':
    			if (count($this->args) > 0)
	        	{
	        		if (isset($locations[$this->args[0]]))
	        			unset($locations[$this->args[0]]);
	        	}
	        	return null;
    			break;
        }
    }
 }

?>