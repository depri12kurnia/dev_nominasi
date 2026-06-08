<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Page_errors extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['title'] = 'Error 404 | Access Denied !!';
        $this->load->view('errors/admin_error404', $data);
    }
}
