<?php

/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.8
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2016 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Email;

class Email_Driver_Sendgrid extends \Email_Driver
{
    /**
     * Last send response.
     *
     * @var object
     */
    protected $response;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $config)
    {
        $config['encode_headers'] = false;

        parent::__construct($config);
    }

    protected function build_email($recipient)
    {
        return new \SendGrid\Email($recipient['name']? $recipient['name']: '', $recipient['email']);
    }

    protected function build_emails($addresses)
    {
        $return = array();
        foreach ($addresses as $recipient) {
            $return[] = $this->build_email($recipient);
        }
        return $return;
    }

    /**
     * {@inheritdoc}
     */
    protected function _send()
    {
        $headers = $this->extra_headers;
        $from = $this->build_email($this->get_from());
        $subject = $this->get_subject();
        $to = $this->build_emails($this->get_to());
        $cc = $this->build_emails($this->get_cc());
        $bcc = $this->build_emails($this->get_bcc());
        $content = new \SendGrid\Content($this->config['is_html']? 'text/html': 'text/plain', $this->get_body());

        $mail = new \SendGrid\Mail(null, null, null, null);

        $mail->setFrom($from);
        $personalization = new \SendGrid\Personalization();
        foreach ($to as $email) {
            $personalization->addTo($email);
        }
        foreach ($cc as $email) {
            $personalization->addCc($email);
        }
        foreach ($bcc as $email) {
            $personalization->addBcc($email);
        }
        foreach ($headers as $name => $value) {
            $personalization->addHeader($name, $value);
        }
        $mail->addPersonalization($personalization);
        $mail->setSubject($subject);
        $mail->addContent($content);

        $apiKey = $this->config['sendgrid']['key'];
        $sg = new \SendGrid($apiKey);

        // https://github.com/sendgrid/sendgrid-php/blob/master/lib/helpers/mail/Mail.php
        // https://github.com/sendgrid/sendgrid-php/blob/master/examples/helpers/mail/example.php

        $response = $sg->client->mail()->send()->post($mail);
        $this->response = $response;
        // echo $response->statusCode();
        // print_r($response->headers());
        // echo $response->body();

        logger(\Fuel::L_DEBUG, "SendGrid response code {$response->statusCode()}");
        logger(\Fuel::L_DEBUG, "SendGrid response body {$response->body()}");

        return $response->statusCode() < 300;

        // // Get attachments
        // $attachments = array();

        // foreach ($this->attachments['attachment'] as $cid => $attachment)
        // {
        // 	$attachments[] = array(
        // 		'type'    => $attachment['mime'],
        // 		'name'    => $attachment['file'][1],
        // 		'content' => $attachment['contents'],
        // 	);
        // }

        // // Get inline images
        // $images = array();

        // foreach ($this->attachments['inline'] as $cid => $attachment)
        // {
        // 	if (\Str::starts_with($attachment['mime'], 'image/'))
        // 	{
        // 		$name = substr($cid, 4); // remove cid:

        // 		$images[] = array(
        // 			'type'    => $attachment['mime'],
        // 			'name'    => $name,
        // 			'content' => $attachment['contents'],
        // 		);
        // 	}
        // }

        // // Get reply-to addresses
        // if ( ! empty($this->reply_to))
        // {
        // 	$headers['Reply-To'] = static::format_addresses($this->reply_to);
        // }

        // $important = false;

        // if (in_array($this->config['priority'], array(\Email::P_HIGH, \Email::P_HIGHEST)))
        // {
        // 	$important = true;
        // }
    }
}
