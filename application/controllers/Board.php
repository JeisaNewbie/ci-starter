<?php
class Board extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('board_model');
        $this->load->helper('html');
        $this->load->library('form_validation');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('string');
    }

    public function index($num = 10, $page = 1)
    {
        $data['board'] = $this->board_model->get_board(NULL, NULL, $num, $page);
        $data['page_num'] = $this->board_model->get_total_page($num);
        $data['num'] = $num;
        $this->load->view('board/index', $data);
    }

    public function view($id = NULL, $group_id = NULL)
    {
        $data['board'] = $this->board_model->get_board($id, $group_id);
        
        if (empty($data['board']))
        {
            show_404();
        }

        $this->load->view('board/view', $data);
    }

    public function create() 
    {
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('content', 'content', 'required');

        if ($this->form_validation->run() === FALSE)
        {
            $this->load->view('board/create');
        }
        else
        {
            $this->board_model->set_board();
            redirect('board');
        }
    }

    public function set_comment($id = NULL, $group_id = NULL)
    {
        $insert_id = $this->board_model->set_content($id, $group_id);
        redirect('board/view/' .  $insert_id . '/' . $group_id);
    }

    public function delete($id = NULL)
    {
        /* TODO: 유저 권한 확인 */
        $this->board_model->delete_board($id);
        redirect('board');
    }
}