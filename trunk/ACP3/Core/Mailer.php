<?php

namespace ACP3\Core;

/**
 * Class Email
 * @package ACP3\Core
 */
class Mailer
{
    /**
     * @var string
     */
    private $subject = '';
    /**
     * @var string
     */
    private $body = '';
    /**
     * @var string
     */
    private $htmlBody = '';
    /**
     * @var string
     */
    private $mailSignature = '';
    /**
     * @var string
     */
    private $from = '';
    /**
     * @var string|array
     */
    private $to;
    /**
     * @var string|array
     */
    private $bcc;
    /**
     * @var int
     */
    private $bccCount;
    /**
     * @var string
     */
    private $template = '';
    /**
     * @var \PHPMailer
     */
    private $mailer;
    /**
     * @var View
     */
    private $view;

    /**
     * Initializes PHPMailer and sets the basic configuration parameters
     */
    public function __construct(View $view = null, $bccCount = 50)
    {
        $this->view = $view;
        $this->mailer = new \PHPMailer(true);

        if (strtolower(CONFIG_MAILER_TYPE) === 'smtp') {
            $this->mailer->set('Mailer', 'smtp');
            $this->mailer->Host = CONFIG_MAILER_SMTP_HOST;
            $this->mailer->Port = CONFIG_MAILER_SMTP_PORT;
            $this->mailer->SMTPSecure = CONFIG_MAILER_SMTP_SECURITY === 'ssl' || CONFIG_MAILER_SMTP_SECURITY === 'tls' ? CONFIG_MAILER_SMTP_SECURITY : '';
            if ((bool)CONFIG_MAILER_SMTP_AUTH === true) {
                $this->mailer->SMTPAuth = true;
                $this->mailer->Username = CONFIG_MAILER_SMTP_USER;
                $this->mailer->Password = CONFIG_MAILER_SMTP_PASSWORD;
            }
        } else {
            $this->mailer->set('Mailer', 'mail');
        }
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->Encoding = '8bit';
        $this->mailer->WordWrap = 76;

        $this->bccCount = $bccCount;
    }

    /**
     * @param array|string $bcc
     * @return $this
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;

        return $this;
    }

    /**
     * @param string $from
     * @return $this
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @param string $mailSignature
     * @return $this
     */
    public function setMailSignature($mailSignature)
    {
        $this->mailSignature = $mailSignature;

        return $this;
    }

    /**
     * @param string $htmlText
     * @return $this
     */
    public function setHtmlBody($htmlText)
    {
        $this->htmlBody = $htmlText;

        return $this;
    }

    /**
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @param string $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @param array|string $to
     * @return $this
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Adds multiple recipients to the to be send email
     *
     * @param $recipients
     * @param bool $bcc
     * @return $this
     */
    private function _addRecipients($recipients, $bcc = false)
    {
        if (is_array($recipients) === true) {
            foreach ($recipients as $recipient) {
                if (is_array($recipient) === true) {
                    $this->_addRecipient($recipient['email'], $recipient['name'], '', $bcc);
                } else {
                    $this->_addRecipient($recipient, '', $bcc);
                }
            }
        } else {
            $this->_addRecipient($recipients, '', $bcc);
        }

        return $this;
    }

    /**
     * Adds a single recipient to the to be send email
     *
     * @param $email
     * @param string $name
     * @param bool $bcc
     * @return $this
     */
    private function _addRecipient($email, $name = '', $bcc = false)
    {
        if ($bcc === true) {
            $this->mailer->addBCC($email, $name);
        } else {
            $this->mailer->addAddress($email, $name);
        }

        return $this;
    }

    /**
     * @return string
     */
    private function _getHtmlSignature()
    {
        if (!empty($this->mailSignature)) {
            if ($this->mailSignature === strip_tags($this->mailSignature)) {
                return Functions::nl2p($this->mailSignature);
            }
            return $this->mailSignature;
        }
        return '';
    }

    /**
     * @return string
     */
    private function _getTextSignature()
    {
        if (!empty($this->mailSignature)) {
            return "\n-- \n" . strip_tags($this->mailSignature);
        }
        return '';
    }

    /**
     *
     * @param $data
     * @return string
     */
    private function _decodeHtmlEntities($data)
    {
        return html_entity_decode($data, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generates the E-mail body
     *
     * @return $this
     */
    private function _generateBody()
    {
        if (!empty($this->htmlBody) && !empty($this->template)) {
            $htmlBody = $this->htmlBody . $this->_getHtmlSignature();

            $mail = array(
                'charset' => 'UTF-8',
                'title' => $this->subject,
                'body' => $htmlBody
            );
            $this->view->assign('mail', $mail);

            $this->mailer->msgHTML($this->view->fetchTemplate($this->template));

            // Fallback for E-mail clients which don't support HTML E-mails
            if (!empty($this->body)) {
                $this->mailer->AltBody = $this->_decodeHtmlEntities($this->body . $this->_getTextSignature());
            } else {
                $this->mailer->AltBody = $this->mailer->html2text($htmlBody, true);
            }
        } else {
            $this->mailer->Body = $this->_decodeHtmlEntities($this->body . $this->_getTextSignature());
        }

        return $this;
    }

    /**
     * Sends the email
     *
     * @return bool
     */
    public function send()
    {
        try {
            $this->mailer->Subject = $this->subject;
            $this->mailer->SetFrom($this->from);

            $this->_generateBody();

            if (!empty($this->bcc)) {
                return $this->_sendBcc();
            } elseif (!empty($this->to)) {
                return $this->_sendTo();
            }

            return false;
        } catch (\phpmailerException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Special sending logic for bcc only E-mails
     *
     * @return bool
     */
    private function _sendBcc()
    {
        $i = 0;

        if (is_array($this->bcc) === false) {
            $this->bcc = array($this->bcc);
        }

        $c_bcc = count($this->bcc);

        foreach ($this->bcc as $recipient) {
            // First collect some addresses
            if ($i < $this->bccCount) {
                $this->_addRecipients($recipient, true);
                ++$i;
            }

            // If enough addresses have been collected, perform a bulk mail sending
            if ($i % $this->bccCount === 0 || $i === $c_bcc) {
                $this->mailer->send();
            }
        }

        return true;
    }

    /**
     * Special sending logic for E-mails without bcc addresses
     *
     * @return bool
     */
    private function _sendTo()
    {
        foreach ($this->to as $recipient) {
            $this->_addRecipients($recipient);
            $this->mailer->send();
        }

        return true;
    }

}