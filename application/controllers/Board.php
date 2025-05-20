<?php
class Board extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('board_model');
        $this->load->helper('html');
    }

    public function index()
    {
       

        $data['board'] = $this->board_model->get_board();

        $this->load->view('board/index', $data);
        $this->load->view('templates/footer');
    }

    public function view($group_id = NULL)
    {
        $data['board_item'] = $this->board_model->get_board($group_id);
        
        if (empty($data['board_item']))
        {
                show_404();
        }

        $data['title'] = $data['board_item']['title'];

        $this->load->view('board/view', $data);
        $this->load->view('templates/footer');
    }

    public function create() 
    {

        $this->load->helper('form');
        $this->load->helper('url');
        
        $this->load->library('form_validation');

        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('content', 'content', 'required');

        if ($this->form_validation->run() === FALSE)
        {
            $this->load->view('board/create');
            $this->load->view('templates/footer');
        }
        else
        {
            $this->board_model->set_board();
            redirect('board');
        }
        
    }
}