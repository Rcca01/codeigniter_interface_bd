<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	function __construct(){
		parent:: __construct();
		$this->load->model('welcome_model');
	}

	public function index()
	{
		$table1 = new Welcome_model();
		$table1->inicialize('table1');
		echo $table1->get_table();

		$table2 = new Welcome_model();
		$table2->inicialize('table2');
		echo $table2->get_table();

		$this->load->view('welcome_message');
	}
}
