<?php
class Auth extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
    }

    public function login()
    {
        $this->form_validation->set_rules('id', 'ID', 'required');
        $this->form_validation->set_rules('password', 'password', 'required');

        if ($this->form_validation->run() === FALSE)
        {
            $this->load->view('auth/login');
        } 
        else
        {
            $login = $this->auth_model->login();
            if ($login === FALSE)
            {
                echo "FAIL";
            }
            else
            {
                echo "OK";
            }
        }
    }

    public function logout()
    {
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('id');
        redirect('/board');
    }
    
    public function create_account()
    {
        $result = $this->auth_model->create_account();

        if ($result === TRUE)
        {
            echo "OK";
        }
        else
        {
            echo "FAIL";
        }
    }
}