<?php
class Auth extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login()
    {
        log_message('error', 'login/index');
        $this->form_validation->set_rules('id', 'ID', 'required');
        $this->form_validation->set_rules('password', 'password', 'required');

        if ($this->form_validation->run() === FALSE)
        {
            $this->load->view('auth/login');
        } 
        else
        {
            $login = $this->board_model_v2->login();
            if ($login === NULL)
            {
                echo "FAIL";
            }
            else
            {
                $this->session->set_userdata($login);
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
        $result = $this->board_model_v2->create_account();

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