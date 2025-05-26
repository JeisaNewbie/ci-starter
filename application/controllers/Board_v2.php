<?php
class Board_v2 extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('board_model_v2');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('string');
    }

    public function index($num = 10, $page = 1)
    {
        // $this->output->enable_profiler(TRUE);
        $search = $this->input->get('search');
        $where = $search !== '' ? array('title LIKE ' => '%' . $search . '%') : NULL;

        $data['board'] = $this->board_model_v2->get_board_index($search, $num, $page);
        $data['pages'] = $this->get_pages($num, $page, 'board_ns', $where);
        $data['search'] = $search;
        $data['num'] = $num;

        $this->load->view('board_v2/index', $data);
    }

    public function view($id = NULL)
    {
        $this->output->enable_profiler(TRUE);
        $data['board'] = $this->board_model_v2->get_board_view($id);

        if (empty($data['board'])) {
            show_404();
        }

        $this->load->view('board_v2/view', $data);
    }

    public function create_account()
    {
        $result = $this->board_model_v2->create_account();

        if ($result === TRUE) {
            echo "OK";
        } else {
            echo "FAIL";
        }
    }

    public function login()
    {
        $this->form_validation->set_rules('id', 'ID', 'required');
        $this->form_validation->set_rules('password', 'password', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('board_v2/login');
        } else {
            $login = $this->board_model_v2->login();
            if ($login === NULL) {
                echo "FAIL";
            } else {
                $this->session->set_userdata($login);
                echo "OK";
            }
        }
    }

    public function logout()
    {
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('id');
        redirect('/board_v2');
    }

    public function id_check()
    {
        if ($this->session->userdata('id') !== NULL) {
            return TRUE;
        }
        $this->form_validation->set_message('id_check', '로그인이 필요합니다.');
        return FALSE;
    }

    public function create()
    {
        $this->form_validation->set_rules('id', 'Id', 'callback_id_check');
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('content', 'content', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('board_v2/create');
        } else {

            $this->board_model_v2->set_board();

            redirect('board_v2');
        }
    }

    public function set_content($id = NULL)
    {
        $insert_id = $this->board_model_v2->set_content($id);
        if ($insert_id === FALSE)
        {
            redirect('board_v2/view/'. $id);
        }
        redirect('board_v2/view/' .  $insert_id);
    }

    public function get_comment($id, $num = 20, $page = 1)
    {
        $data['comments'] = $this->board_model_v2->get_comment($id, $num, $page);

        $data['pages'] = $this->get_pages($num, $page, 'comment', array('board_ns_id' => $id));
        $data['num'] = $this->board_model_v2->get_data_num('comment', array('board_ns_id' => $id));

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
        $result = $this->board_model_v2->delete_board($id);
        if ($result === TRUE) {
            echo "OK";
        } else {
            echo "FAIL";
        }
    }

    private function get_pages($num = 10, $page = 1, $table, $where = NULL, $search = NULL)
    {
        $page_num = $this->board_model_v2->get_page_num($num, $table, $where);
        $page_num = $page_num === 0 ? 1 : $page_num;
        $quotient = (int)($page / 10);
        $mod = $page % 10;

        $data['start_page'] = $mod == 0 ? $quotient * 10 + 1 - 10 : $quotient * 10 + 1;
        $data['end_page'] = $mod == 0 ? $quotient * 10 : $quotient * 10 + 10;
        $data['end_page'] = $data['end_page'] > $page_num ? $page_num : $data['end_page'];

        $data['before'] = ($page - 10 < 1 ? 1 : $page - 10);
        $data['after'] = ($page + 10 > $page_num ? $page_num : $page + 10);

        return $data;
    }
}
