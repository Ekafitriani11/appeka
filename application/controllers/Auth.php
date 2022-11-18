<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller 
{

	public function _construct()
	{
		parent::_construct();
		$this->load->library('form_validation');
	}

	public function index()
	{
		if($this->session->userdata('email')) {
			redirect('user');
		}
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required');
		if ($this->form_validation->run() == false) {

			$data['title'] = 'login pages'; //untuk membuat judul halaman
        	$this->load->view('templates/auth_header', $data);
			$this->load->view('auth/login');
        	$this->load->view('templates/auth_footer');
		}else{
			$this->_login();
		}	
	}
	private function _login()
	{
                $email = $this->input->post('email');
                $password = $this->input->post('password');
                $user = $this->db->get_where('user' , ['email' => $email])->row_array();
				
                //jika user ada
                if ($user) {
                        //jika user aktif
                        if ($user['is_active'] == 1) {
                                //cek password sudah benar
                                if (password_verify($password, $user['password'])) {
                                        $data = [
                                                'email' => $user['email'],
                                                'role_id' =>$user['role_id']
                                        ];
                                        $this->session->set_userdata($data);
										if ($user['role_id'] == 1 ) {
											redirect('admin');
										} else {
                                        	redirect('user');
										}
                                } else {
                                        $this->session->set_flashdata(
                                                'message',
                                                '<div class="alert alert-danger" role="alert"> Wrong password! </div>'
                                        );
                                        redirect('auth');
                                }
                        } else {
                                $this->session->set_flashdata(
                                        'message',
                                        '<div class="alert alert-danger" role="alert"> This email has not been activated! </div>'
                                );
                                redirect('auth');
                        }
                } else {
                        $this->session->set_flashdata(
                                'message',
                                '<div class="alert alert-danger" role="alert">Email is not registered! </div>'
                        );
                        redirect('auth');
                }
	}
	public function registration()
	{
		if($this->session->userdata('email')) {
			redirect('user');
		}
		$this->form_validation->set_rules('name', 'Name', 'required|trim');
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
		$this->form_validation->set_rules(
			'password1',
			'Password',
			'required|trim|min_length[5]|matches[password2]',
			[
				'matches' => 'password do not match',
				'min_length' => 'password to short'
			]
		);
		$this->form_validation->set_rules(
			'password2',
			'Password',
			'required|trim|matches[password1]'
		);

		if ($this->form_validation->run() == false) {

		$data['title'] = 'registration pages';
        $this->load->view('templates/auth_header', $data);
		$this->load->view('auth/registration');
        $this->load->view('templates/auth_footer');
		}else{
			//untuk menampilkan jika login sudah benar
			$email = $this->input->post('email', true);
				$data = [
					'name' => htmlspecialchars($this->input->post('name', true)),
					'email' => htmlspecialchars($email),
					'image' => 'default.jpg',
					'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
					'role_id' => 2,
					'is_active' => 0,
					'date_created' => time()
			];
			$token = base64_encode(random_bytes(32));
			$user_token = [
				'email' => $email,
				'token' => $token,
				'date_created' => time()
			];
			$this->db->insert('user', $data);
			$this->db->insert('user_token', $user_token);
			$this->_sendEmail($token, 'verify');
			$this->session->set_flashdata('message', '<div class="alert alert-success" 
			role="alert">Congratulations!you account has been created. Please Activate your Account</div>');
			redirect('auth');
		}
	}

	//function untuk mengirim verifikasi ke mail
	private function _sendEmail($token, $type)
	{
		$this->load->library('email');
		$config = array();
		$config ['protocol'] = 'smtp';
		$config ['smtp_host'] = 'ssl://smtp.googlemail.com';
		$config ['smtp_user'] = 'ffittriani89@gmail.com'; //email yang dirubah keamanannya
		$config ['smtp_pass'] = 'oxnebjqbkjqaxzjz'; //isikan password dr kelola akun gmail
		$config ['smtp_port'] = 465;
		$config ['mailtype'] = 'html';
		$config ['charset'] = 'utf-8';
		//$config = array(
		//	'protocol' => 'smtp';
		//	'smtp_host' => 'smtp.mailtrap.io',
		//	'smtp_port' => 2525,
		//	'smtp_user' => '51739a034cb411',
		//	'smtp_pass' => 'd0d6a0f025db3a',
		//	'crlf' => "\r\n",
		//	'newline' => "\r\n"
		//);
		$this->email->initialize($config) ;
		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");
		
		$this->email->from('ffittriani89@gmail.com', 'Web Programing SMKN8Jember');
		$this->email->to($this->input->post('email'));

	
		if ($type == 'verify') {
            $this->email->subject('Account Verivication');
            $this->email->message('Clik this link to reset your password : 
                <a href="' . base_url() . 'auth/verify?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '">Activated</a>');
        } else if ($type == 'forgot') {
            $this->email->subject('Reset Password');
            $this->email->message('Clik this link to verify you account :
                <a href="' . base_url() . 'auth/resetpassword?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '">Reset Password</a>');
        }
        if ($this->email->send()) {
            return true;
        } else {
            echo $this->email->print_debugger();
            die;
        }
    }
    public function verify()
    {
        $email = $this->input->get('email');
        $token = $this->input->get('token');

        $user = $this->db->get_where('user', ['email' => $email])->row_array();
        if ($user) {
            $user_token = $this->db->get_where('user_token', ['token' => $token])->row_array();

            if ($user_token) {
                if (time() - $user_token['date_created'] < (60 * 60 * 24)) {
                    $this->db->set('is_active', 1);
                    $this->db->where('email', $email);
                    $this->db->update('user');
                    $this->db->delete('user_token', ['email' => $email]);
                    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">' . $email . ' has been Activited!please login</div>');
                    redirect('auth');
                } else {
                    $this->db->delete('user', ['email' => $email]);
                    $this->db->delete('user_token', ['email' => $email]);
                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Account activation failed!token expired.</div>');
                    redirect('auth');
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert"Account activation failed!Wrong token</div>');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert"Account activation failed!Wrong email</div>');
            redirect('auth');
        }
    }

	public function logout() 
	{

		$this->session->unset_userdata('email');
		$this->session->unset_userdata('role_data');
		$this->session->set_flashdata(
			'message',
			'<div class="alert alert-success" role="alert"> You have been logged Out! </div>'
		);
		redirect('auth');
	}
	public function blocked()
	{
		$this->load->view('auth/blocked');
	}
	public function resetpassword()
	{
		$email = $this->input->get('email');
		$token = $this->input->get('token');
		$user = $this->db->get_where('user',['email'=>$email])->row_array();
		if ($user){
			$user_token = $this->db->get_where('user_token',['token'=>$token]);

		if ($user_token) {
			$this->session->set_userdata('reset_email',$email);
			$this->changePassword();
		} else {
			$this->session->set_flashdata('message','<div class="alert alert-danger" role="alert">Reset password failed!Wrong token!</div>');
			redirect('auth/forgotpassword');
		}
	} else {
		$this->session->set_flashdata('message','<div class="alert alert-danger" role="alert">Reset password failed!Wrong email!</div>');
			redirect('auth/forgotpassword');
		}
	}
	public function changePassword()
	{
		if (!$this->session->userdata('reset_email')) {
			redirect('auth');
		}
		$this->form_validation->set_rules('password1','Password','required|trim|main_leght[3]|matches[password2]');
		$this->form_validation->set_rules('password2','Repeat Password','required|trim|main_leght[3]|matches[password1]');
		if ($this->form_validation->run() == false) {
			$data['title'] = 'Change Password';
			$this->load->view('templates/auth_header',$data);
			$this->load->view('auth/change-password');
			$this->load->view('templates/auth_footer');
		} else {
			$password = hash($this->input->post('password1'), PASSWORD_DEFAULT);
			$email = $this->session->userdata('reset_email');

			$this->db->set('password',$password);
			$this->db->where('email',$email);
			$this->db->update('user');

			$this->session->unset->userdata('reset_email');
			$this->session->set_flashdata('message','<div class="alert alert-success" role="alert">Password has been Change!Please Login</div>');
			redirect('auth');
		}                                                                 
	}
	public function forgotpassword()
	{
		$this->form_validation->set_rules('email','Email','trim|required');
		if ($this->form_validation->run() == false){
			$data['title'] = 'Forgot passwod';
			$this->load->view('templates/auth_header',$data);
			$this->load->view('auth/forgot-password');
			$this->load->view('templates/auth_footer');
	 	} else {
			$email = $this->input->post('email');
			$user = $this->db->get_where('user',['email' => $email,'is_active'=> 1])->row_array();

			if ($user) {
				$token = base64_encode(random_bytes(32));
				$user_token =[
					'email' => $email,
					'token' => $token,
					'date_created' => time()
				];
				$this->db->insert('user_token',$user_token);
				$this->_sendEmail($token,'forgot');

				$this->session->set_flashdata('message','<div class="alert alert-success" role="alert">Please check your email to reset password!</div>');
				redirect('auth/forgotpassword');
			} else {
				$this->session->set_flashdata('message','<div class="alert alaert-danger role="alert>Email is not registeret or activated!</div>');
				redirect('auth/forgotpassword');
			}
		}
	}
}
