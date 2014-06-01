<?php

namespace ACP3\Core;

use InlineStyle\InlineStyle;

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
    private $urlWeb = '';
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
    private $recipients;
    /**
     * @var bool
     */
    private $bcc;
    /**
     * @var int
     */
    private $bccCount;
    /**
     * @var array
     */
    private $attachments = array();
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
    public function __construct(View $view = null, $bcc = false, $bccCount = 50)
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

        $this->bcc = $bcc;
        $this->bccCount = $bccCount;
    }

    /**
     * @param string|array $from
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
     * @param string $urlWeb
     * @return $this
     */
    public function setUrlWeb($urlWeb)
    {
        $this->urlWeb = $urlWeb;

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
    public function setRecipients($to)
    {
        $this->recipients = $to;

        return $this;
    }

    /**
     * @param string $attachment
     */
    public function setAttachments($attachment)
    {
        $this->attachments[] = $attachment;
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
            if (empty($recipients['email']) === false && empty($recipients['name']) === false) {
                $this->_addRecipient($recipients['email'], $recipients['name'], $bcc);
            } else {
                foreach ($recipients as $recipient) {
                    if (is_array($recipient) === true) {
                        $this->_addRecipient($recipient['email'], $recipient['name'], '', $bcc);
                    } else {
                        $this->_addRecipient($recipient, '', $bcc);
                    }
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
            return "\n-- \n" . $this->mailer->html2text($this->mailSignature, true);
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
            $mail = array(
                'charset' => 'UTF-8',
                'title' => $this->subject,
                'body' => $this->htmlBody,
                'signature' => $this->_getHtmlSignature(),
                'url_web_view' => $this->urlWeb
            );
            $this->view->assign('mail', $mail);

            $htmlDocument = new InlineStyle($this->view->fetchTemplate($this->template));
            $htmlDocument->applyStylesheet($htmlDocument->extractStylesheets());

            $this->mailer->msgHTML($htmlDocument->getHTML());

            // Fallback for E-mail clients which don't support HTML E-mails
            if (!empty($this->body)) {
                $this->mailer->AltBody = $this->_decodeHtmlEntities($this->body . $this->_getTextSignature());
            } else {
                $this->mailer->AltBody = $this->mailer->html2text($this->htmlBody . $this->_getHtmlSignature(), true);
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

            if (is_array($this->from) === true) {
                $this->mailer->SetFrom($this->from['email'], $this->from['name']);
            } else {
                $this->mailer->SetFrom($this->from);
            }

            $this->_generateBody();

            // Add attachments to the E-mail
            if (count($this->attachments) > 0) {
                foreach ($this->attachments as $attachment) {
                    if (!empty($attachment) && is_file($attachment)) {
                        $this->mailer->addAttachment($attachment);
                    }
                }
            }

            if (!empty($this->recipients)) {
                return $this->bcc === true ? $this->_sendBcc() : $this->_sendTo();
            }

            return false;
        } catch (\phpmailerException $e) {
            Logger::log('mailer', 'error', $e->getMessage());
            return false;
        } catch (\Exception $e) {
            Logger::log('mailer', 'error', $e->getMessage());
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

        if (is_array($this->recipients) === false || isset($this->recipients['email']) === true) {
            $this->recipients = array($this->recipients);
        }

        $c_recipients = count($this->recipients);

        foreach ($this->recipients as $recipient) {
            // First collect some addresses
            if ($i < $this->bccCount) {
                $this->_addRecipients($recipient, true);
                ++$i;
            }

            // If enough addresses have been collected, perform a bulk mail sending
            if ($i % $this->bccCount === 0 || $i === $c_recipients) {
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
        if (is_array($this->recipients) === false || isset($this->recipients['email']) === true) {
            $this->recipients = array($this->recipients);
        }

        foreach ($this->recipients as $recipient) {
            $this->_addRecipients($recipient);
            $this->mailer->send();
        }

        return true;
    }

}