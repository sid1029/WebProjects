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
        $mysqli = new mysqli("localhost", "uberappuser", "ubersecretpass", "uberapp");
		if ($mysqli->connect_errno) {
		    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		}
		echo $mysqli->host_info . "\n";
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
		$locations = array(
				array("name"=>"Graham F. Henson","lat"=>"12.63367","lng"=>"123.84274","address"=>"2435 Eu Ave"),
				array("name"=>"Adele M. Stuart","lat"=>"-45.40987","lng"=>"-31.44555","address"=>"P.O. Box 792, 9320 Volutpat Rd."),
				array("name"=>"Eve X. Snow","lat"=>"-37.04044","lng"=>"-134.36535","address"=>"6720 Iaculis Rd."),
				array("name"=>"Jin D. Gomez","lat"=>"-37.62478","lng"=>"141.25152","address"=>"235-8953 Magna. Ave"),
				array("name"=>"Kimberly Y. Mccullough","lat"=>"39.20517","lng"=>"-88.39943","address"=>"Ap #664-9454 Nisi Ave"),
				array("name"=>"Kylie J. Martinez","lat"=>"44.01258","lng"=>"116.98117","address"=>"P.O. Box 302, 2212 Faucibus. St."),
				array("name"=>"Daria W. Gordon","lat"=>"-43.88828","lng"=>"109.87719","address"=>"Ap #905-5965 Curabitur Road")
			);

        switch ($this->method) {
        	case 'GET':
        		// GET request for a specific ID
		        if (count($this->args) > 0)
	        	{
	        		return $locations[$this->args[0]];
	        	}
	        	else
					return $locations;
        		break;

        	case 'POST':
				$location = $_POST;
	        	// Persist to DB. get last inserted ID.
	        	$location['id'] = 12;
                array_push($locations, $location);
	        	return $location;
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