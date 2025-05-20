<?php
class Errors extends CI_Controller
{
    public function general_error()
    {
        show_error("An error occurred", 500, "An Error Was Encountered");
        $this->load->view('errors/html/error_general');
    }
}