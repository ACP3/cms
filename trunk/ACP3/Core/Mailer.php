<?php

namespace ACP3\Core;

use ACP3\Core\Helpers\StringFormatter;
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
    private $bcc = false;
    /**
     * @var array
     */
    private $attachments = [];
    /**
     * @var string
     */
    private $template = '';
    /**
     * @var \PHPMailer
     */
    private $phpMailer;
    /**
     * @var \ACP3\Core\View
     */
    private $view;
    /**
     * @var \ACP3\Core\Config
     */
    private $systemConfig;

    /**
     * @param \ACP3\Core\View   $view
     * @param \ACP3\Core\Config $systemConfig
     */
    public function __construct(
        View $view,
        Config $systemConfig
    )
    {
        $this->view = $view;
        $this->systemConfig = $systemConfig;
    }

    /**
     * @param string|array $from
     *
     * @return $this
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @param string $mailSignature
     *
     * @return $this
     */
    public function setMailSignature($mailSignature)
    {
        $this->mailSignature = $mailSignature;

        return $this;
    }

    /**
     * @param string $htmlText
     *
     * @return $this
     */
    public function setHtmlBody($htmlText)
    {
        $this->htmlBody = $htmlText;

        return $this;
    }

    /**
     * @param string $urlWeb
     *
     * @return $this
     */
    public function setUrlWeb($urlWeb)
    {
        $this->urlWeb = $urlWeb;

        return $this;
    }

    /**
     * @param bool $bcc
     *
     * @return $this
     */
    public function setBcc($bcc)
    {
        $this->bcc = (bool)$bcc;

        return $this;
    }

    /**
     * @param string $subject
     *
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @param string $body
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @param array|string $to
     *
     * @return $this
     */
    public function setRecipients($to)
    {
        $this->recipients = $to;

        return $this;
    }

    /**
     * @param string $attachment
     *
     * @return $this
     */
    public function setAttachments($attachment)
    {
        $this->attachments[] = $attachment;

        return $this;
    }

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

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
            $this->configure();

            $this->phpMailer->Subject = $this->subject;

            if (is_array($this->from) === true) {
                $this->phpMailer->SetFrom($this->from['email'], $this->from['name']);
            } else {
                $this->phpMailer->SetFrom($this->from);
            }

            $this->_generateBody();

            // Add attachments to the E-mail
            if (count($this->attachments) > 0) {
                foreach ($this->attachments as $attachment) {
                    if (!empty($attachment) && is_file($attachment)) {
                        $this->phpMailer->addAttachment($attachment);
                    }
                }
            }

            if (!empty($this->recipients)) {
                return $this->bcc === true ? $this->_sendBcc() : $this->_sendTo();
            }

            return false;
        } catch (\phpmailerException $e) {
            Logger::error('mailer', $e->getMessage());
            return false;
        } catch (\Exception $e) {
            Logger::error('mailer', $e->getMessage());
            return false;
        }
    }

    /**
     * Generates the E-mail body
     *
     * @return $this
     */
    private function _generateBody()
    {
        if (!empty($this->htmlBody) && !empty($this->template)) {
            $mail = [
                'charset' => 'UTF-8',
                'title' => $this->subject,
                'body' => $this->htmlBody,
                'signature' => $this->_getHtmlSignature(),
                'url_web_view' => $this->urlWeb
            ];
            $this->view->assign('mail', $mail);

            $htmlDocument = new InlineStyle($this->view->fetchTemplate($this->template));
            $htmlDocument->applyStylesheet($htmlDocument->extractStylesheets());

            $this->phpMailer->msgHTML($htmlDocument->getHTML());

            // Fallback for E-mail clients which don't support HTML E-mails
            if (!empty($this->body)) {
                $this->phpMailer->AltBody = $this->_decodeHtmlEntities($this->body . $this->_getTextSignature());
            } else {
                $this->phpMailer->AltBody = $this->phpMailer->html2text($this->htmlBody . $this->_getHtmlSignature(), true);
            }
        } else {
            $this->phpMailer->Body = $this->_decodeHtmlEntities($this->body . $this->_getTextSignature());
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
                $formatter = new StringFormatter();
                return $formatter->nl2p($this->mailSignature);
            }
            return $this->mailSignature;
        }
        return '';
    }

    /**
     *
     * @param $data
     *
     * @return string
     */
    private function _decodeHtmlEntities($data)
    {
        return html_entity_decode($data, ENT_QUOTES, 'UTF-8');
    }

    /**
     * @return string
     */
    private function _getTextSignature()
    {
        if (!empty($this->mailSignature)) {
            return "\n-- \n" . $this->phpMailer->html2text($this->mailSignature, true);
        }
        return '';
    }

    /**
     * Special sending logic for bcc only E-mails
     *
     * @return bool
     */
    private function _sendBcc()
    {
        if (is_array($this->recipients) === false || isset($this->recipients['email']) === true) {
            $this->recipients = [$this->recipients];
        }

        foreach ($this->recipients as $recipient) {
            set_time_limit(10);

            $this->_addRecipients($recipient, true);
        }

        return $this->phpMailer->send();
    }

    /**
     * Adds multiple recipients to the to be send email
     *
     * @param      $recipients
     * @param bool $bcc
     *
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
     * @param        $email
     * @param string $name
     * @param bool   $bcc
     *
     * @return $this
     */
    private function _addRecipient($email, $name = '', $bcc = false)
    {
        if ($bcc === true) {
            $this->phpMailer->addBCC($email, $name);
        } else {
            $this->phpMailer->addAddress($email, $name);
        }

        return $this;
    }

    /**
     * Special sending logic for E-mails without bcc addresses
     *
     * @return bool
     */
    private function _sendTo()
    {
        if (is_array($this->recipients) === false || isset($this->recipients['email']) === true) {
            $this->recipients = [$this->recipients];
        }

        foreach ($this->recipients as $recipient) {
            set_time_limit(20);
            $this->_addRecipients($recipient);
            $this->phpMailer->send();
            $this->phpMailer->clearAllRecipients();
        }

        return true;
    }

    /**
     * Resets the currently set mailer values back to there default values
     * @return $this
     */
    public function reset()
    {
        $this->subject = '';
        $this->body = '';
        $this->htmlBody = '';
        $this->urlWeb = '';
        $this->mailSignature = '';
        $this->from = '';
        $this->recipients = null;
        $this->bcc = false;
        $this->attachments = [];
        $this->template = '';

        if ($this->phpMailer) {
            $this->phpMailer->clearAllRecipients();
            $this->phpMailer->clearAttachments();
        }

        return $this;
    }

    /**
     * Initializes PHPMailer and sets the basic configuration parameters
     *
     * @return $this
     */
    private function configure()
    {
        if ($this->phpMailer === null) {
            $this->phpMailer = new \PHPMailer(true);

            $settings = $this->systemConfig->getSettings();

            if (strtolower($settings['mailer_type']) === 'smtp') {
                $this->phpMailer->set('Mailer', 'smtp');
                $this->phpMailer->Host = $settings['mailer_smtp_host'];
                $this->phpMailer->Port = $settings['mailer_smtp_port'];
                $this->phpMailer->SMTPSecure = in_array($settings['mailer_smtp_security'], ['ssl', 'tls']) ? $settings['mailer_smtp_security'] : '';
                if ((bool)$settings['mailer_smtp_auth'] === true) {
                    $this->phpMailer->SMTPAuth = true;
                    $this->phpMailer->Username = $settings['mailer_smtp_user'];
                    $this->phpMailer->Password = $settings['mailer_smtp_password'];
                }
            } else {
                $this->phpMailer->set('Mailer', 'mail');
            }
            $this->phpMailer->CharSet = 'UTF-8';
            $this->phpMailer->Encoding = '8bit';
            $this->phpMailer->WordWrap = 76;
        }

        return $this;
    }
}
