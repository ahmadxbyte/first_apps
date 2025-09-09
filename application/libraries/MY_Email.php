<?php

defined('BASEPATH') or exit('No direct script access allowed');


class MY_Email extends CI_Email
{
    private $CI;

    public function __construct()
    {
        parent::__construct();
        $this->CI = &get_instance();
        $this->CI->load->database();
    }

    public function send_my_email($to, $subject, $message, $pdf)
    {
        $CI = &get_instance();

        $smtp_apps = $CI->db->query("SELECT * FROM web_setting WHERE id = 1")->row();

        $config = [
            'mailtype'      => 'html',
            'charset'       => 'utf-8',
            'protocol'      => 'smtp',
            'smtp_host'     => 'smtp.gmail.com',
            'smtp_user'     => $smtp_apps->email,
            'smtp_pass'     => $smtp_apps->kode_email,
            'smtp_crypto'   => 'ssl',
            'smtp_port'     => 465,
            'crlf'          => "\r\n",
            'newline'       => "\r\n"
        ];

        $this->clear();
        $this->initialize($config);
        $this->from($smtp_apps->email, $smtp_apps->nama);
        $this->to($to);
        $this->subject($subject);
        $this->message($message);
        $this->attach($pdf);

        if ($this->send()) {
            return true;
        } else {
            return false;
        }
    }
}
