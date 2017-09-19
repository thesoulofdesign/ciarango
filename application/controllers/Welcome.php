<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index(){
		$this->load->view('welcome_message');
	}


    /**
     *Test Arango
     *
     *
     */
	public function arangotest(){
        //Load the ArangoDb library here or you can add it globally in Autoload
        $this->load->library('arango');

        //Create Arango Object
        $arango = new Arango();

        //Creating connection
        $arango->connect();

        //Creating a Collection 'example'
        $arango->createCollection('example');

        //Data array to store in the Collection 'users'
        $data = array(
            'name' => 'Sunil',
            'location' => 'Kolkata',
            'age' => 25,
            'sex' => 'male'
        );

        //Finally Inserting data into the collection 'example'
        $arango->insert('example',$data);
    }


}
