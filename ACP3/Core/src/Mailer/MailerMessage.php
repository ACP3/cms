<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Mailer;

class MailerMessage
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
     * @var string|array
     */
    private $from;
    /**
     * @var string|array
     */
    private $replyTo;
    /**
     * @var string
     */
    private $sender;
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
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return $this
     */
    public function setSubject(string $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return $this
     */
    public function setBody(string $body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function getHtmlBody()
    {
        return $this->htmlBody;
    }

    /**
     * @return $this
     */
    public function setHtmlBody(string $htmlBody)
    {
        $this->htmlBody = $htmlBody;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrlWeb()
    {
        return $this->urlWeb;
    }

    /**
     * @return $this
     */
    public function setUrlWeb(string $urlWeb)
    {
        $this->urlWeb = $urlWeb;

        return $this;
    }

    /**
     * @return string
     */
    public function getMailSignature()
    {
        return $this->mailSignature;
    }

    /**
     * @return $this
     */
    public function setMailSignature(string $mailSignature)
    {
        $this->mailSignature = $mailSignature;

        return $this;
    }

    public function getFrom(): array|string
    {
        return $this->from;
    }

    /**
     * @return $this
     */
    public function setFrom(array|string $from)
    {
        $this->from = $from;

        return $this;
    }

    public function getReplyTo(): array|string
    {
        return $this->replyTo;
    }

    /**
     * @return $this
     */
    public function setReplyTo(array|string $replyTo)
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    /**
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param string $sender
     *
     * @return $this
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    public function getRecipients(): array|string
    {
        return $this->recipients;
    }

    /**
     * @return $this
     */
    public function setRecipients(array|string $recipients)
    {
        $this->recipients = $recipients;

        return $this;
    }

    /**
     * @return bool
     */
    public function isBcc()
    {
        return $this->bcc;
    }

    /**
     * @return $this
     */
    public function setBcc(bool $bcc)
    {
        $this->bcc = $bcc;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @return $this
     */
    public function setAttachments(array $attachments)
    {
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * @return $this
     */
    public function addAttachment(string $attachment)
    {
        $this->attachments[] = $attachment;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return $this
     */
    public function setTemplate(string $template)
    {
        $this->template = $template;

        return $this;
    }
}
