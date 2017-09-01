<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Mailer;

use ACP3\Core\Helpers\StringFormatter;
use ACP3\Core\View;
use InlineStyle\InlineStyle;
use PHPMailer\PHPMailer\PHPMailer;

class MessageProcessor
{
    /**
     * @var StringFormatter
     */
    private $stringFormatter;
    /**
     * @var View
     */
    private $view;
    /**
     * @var InlineStyle
     */
    private $inlineStyle;

    /**
     * MessageProcessor constructor.
     * @param InlineStyle $inlineStyle
     * @param StringFormatter $stringFormatter
     * @param View $view
     */
    public function __construct(
        InlineStyle $inlineStyle,
        StringFormatter $stringFormatter,
        View $view
    ) {
        $this->stringFormatter = $stringFormatter;
        $this->view = $view;
        $this->inlineStyle = $inlineStyle;
    }

    /**
     * Parses and generates the E-mail subject and body
     *
     * @param PHPMailer $phpMailer
     * @param MailerMessage $message
     * @return void
     */
    public function process(PHPMailer $phpMailer, MailerMessage $message)
    {
        $phpMailer->Subject = $this->encodeSubject($message->getSubject());

        if (!empty($message->getTemplate())) {
            $this->processHtmlEmail($phpMailer, $message);
        } else {
            $phpMailer->Body = $this->decodeHtmlEntities(
                $message->getBody() . $this->getTextSignature($phpMailer, $message->getMailSignature())
            );
        }
    }

    /**
     * @param string $subject
     * @return string
     */
    private function encodeSubject(string $subject): string
    {
        return "=?utf-8?b?" . base64_encode($this->decodeHtmlEntities($subject)) . "?=";
    }

    /**
     * @param string $data
     * @return string
     */
    private function decodeHtmlEntities(string $data): string
    {
        return html_entity_decode($data, ENT_QUOTES, 'UTF-8');
    }

    /**
     * @param PHPMailer $phpMailer
     * @param MailerMessage $message
     */
    private function processHtmlEmail(PHPMailer $phpMailer, MailerMessage $message)
    {
        $mail = [
            'charset' => 'UTF-8',
            'title' => $message->getSubject(),
            'body' => !empty($message->getHtmlBody()) ?
                $message->getHtmlBody() :
                $this->stringFormatter->nl2p(htmlspecialchars($message->getBody())),
            'signature' => $this->getHtmlSignature($message->getMailSignature()),
            'url_web_view' => $message->getUrlWeb()
        ];
        $this->view->assign('mail', $mail);

        $this->inlineStyle->loadHTML($this->view->fetchTemplate($message->getTemplate()));
        $this->inlineStyle->applyStylesheet($this->inlineStyle->extractStylesheets());

        $phpMailer->msgHTML($this->inlineStyle->getHTML());

        // Fallback for E-mail clients which don't support HTML E-mails
        if (!empty($message->getBody())) {
            $phpMailer->AltBody = $this->decodeHtmlEntities(
                $message->getBody() . $this->getTextSignature($phpMailer, $message->getMailSignature())
            );
        } else {
            $phpMailer->AltBody = $phpMailer->html2text(
                $message->getHtmlBody() . $this->getHtmlSignature($message->getMailSignature()),
                true
            );
        }
    }

    /**
     * @param string $signature
     * @return string
     */
    private function getHtmlSignature(string $signature): string
    {
        if (!empty($signature)) {
            if ($signature === strip_tags($signature)) {
                return $this->stringFormatter->nl2p($signature);
            }
            return $signature;
        }
        return '';
    }

    /**
     * @param PHPMailer $phpMailer
     * @param string $signature
     * @return string
     */
    private function getTextSignature(PHPMailer $phpMailer, string $signature): string
    {
        if (!empty($signature)) {
            return "\n-- \n" . $phpMailer->html2text($signature, true);
        }
        return '';
    }
}
