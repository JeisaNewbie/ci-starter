<?php
class Board_v2 extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('board_model_v2');
        $this->load->helper('html');
        $this->load->library('form_validation');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('string');
    }

    public function index($num = 10, $page = 1)
    {
        $data['board'] = $this->board_model_v2->get_board_index($num, $page);

        $data['page_num'] = $this->board_model_v2->get_total_page($num, 'board_ns', NULL);
        $data['num'] = $num;
        $this->load->view('board_v2/index', $data);
    }

    public function view($id = NULL)
    {
        $data['board'] = $this->board_model_v2->get_board_view($id);
        
        if (empty($data['board']))
        {
            show_404();
        }

        $this->load->view('board_v2/view', $data);
    }

    public function create() 
    {
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('content', 'content', 'required');

        if ($this->form_validation->run() === FALSE)
        {
            $this->load->view('board_v2/create');
        }
        else
        {
            $this->board_model_v2->set_board();
            redirect('board_v2');
        }
    }

    public function set_content($id = NULL)
    {
        $insert_id = $this->board_model_v2->set_content($id);
        redirect('board_v2/view/' .  $insert_id);
    }

    public function get_comment($id, $num = 20, $page = 1)
    {
        $data['comments'] = $this->board_model_v2->get_comment($id, $num, $page);

        $data['page_num'] = $this->board_model_v2->get_total_page($num, 'comment', array('board_ns_id' => $id));
        $data['num'] = $num;
        log_message('error', "get_comment");
        
        /* Ajax 응답 */
        // return $this->output->;
        // echo $data;
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }

    public function set_comment($id)
    {
        $this->board_model_v2->set_comment($id);
        echo "OK";
        // redirect('board_v2/view/' . $id);
    }

    public function delete($id = NULL)
    {
        /* TODO: 유저 권한 확인 */
        $this->board_model_v2->delete_board($id);
        redirect('board_v2');
    }
}