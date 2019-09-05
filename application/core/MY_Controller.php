
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Send email
     * @param array/string $to_email
     * @param string $subject
     * @param string $message
     * @param [] $attachments
     * @param string $bcc
     */
    public function send_email($to_email, $subject = '', $message = '', $attachments = [], $bcc = '')
    {
        // Load email library
        $this->load->library('email');

        // Email Configuration
        $config = [
            'mailtype'  => 'html',
            'protocol'  => 'smtp',
            'smtp_host' => getenv('SMTP_HOST'),
            'smtp_user' => getenv('SMTP_USER'),
            'smtp_pass' => getenv('SMTP_PASS'),
            'smtp_port' => getenv('SMTP_PORT'),
            'charset'   => 'utf8',
            'crlf'      => "\r\n",
            'newline'   => "\r\n"
        ];

        $from_email = getenv('EMAIL_FROM');

        $this->email->initialize($config);

        $this->email->clear();

        $this->email->from($from_email, 'WayCool');

        // Array or string
        $this->email->to($to_email);

        if(!empty($bcc))
            $this->email->bcc($bcc);


        $this->email->subject($subject);
        $this->email->message($message);

        if(!empty($attachments)){
            if(is_string($attachments)){
                $str_attachment = $attachments;
                $attachments    = [];
                $attachments[]  = $str_attachment;
            }
            foreach($attachments as $attachment){
                $this->email->attach($attachment);
            }
        }

        return $this->email->send();
    }
}