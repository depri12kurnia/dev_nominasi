<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('ion_auth');
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login');
        }
        $this->load->helper('url');
        $this->load->library('form_validation');

        $this->load->model('M_log_user');
    }

    public function change_password()
    {
        $this->form_validation->set_rules('oldPassword', 'Old Password', 'required');
        $this->form_validation->set_rules('newPassword', 'New Password', 'required|min_length[8]');
        $this->form_validation->set_rules('confirmPassword', 'Confirm New Password', 'required|matches[newPassword]');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'Change Password';
            $data['content'] = 'auth/change_password';
            $this->load->view('layouts/adminlte3', $data);
        } else {
            $user = $this->ion_auth->user()->row(); // Get the current user
            $oldPassword = $this->input->post('oldPassword');
            $newPassword = $this->input->post('newPassword');

            // Verify the old password
            if ($this->ion_auth->login($user->email, $oldPassword)) {
                if ($this->ion_auth->change_password($user->email, $oldPassword, $newPassword)) {
                    $this->session->set_flashdata('message', 'Password changed successfully.');
                    // Mendapatkan user yang login
                    $user = $this->ion_auth->user()->row();
                    // Menyimpan log aktivitas login
                    $this->M_log_user->save_log($user->id, 'User Password changed successfully');

                    $this->logout();
                } else {
                    $this->session->set_flashdata('message', 'Unable to change password. Please try again.');
                }
            } else {
                $this->session->set_flashdata('message', 'Old password is incorrect.');
            }
            redirect('auth/change_password');
        }
    }

    public function logout()
    {
        $this->data['title'] = "Logout";

        // Mendapatkan user yang login
        $user = $this->ion_auth->user()->row();
        // Menyimpan log aktivitas login
        $this->M_log_user->save_log($user->id, 'User Logout');

        // log the user out
        $this->ion_auth->logout();


        // redirect them to the login page
        redirect('auth/login', 'refresh');
    }
}
