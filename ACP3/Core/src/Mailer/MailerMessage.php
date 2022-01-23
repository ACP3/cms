<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Mailer;

class MailerMessage
{
    private string $subject = '';

    private string $body = '';

    private string $htmlBody = '';

    private string $urlWeb = '';

    private string $mailSignature = '';

    private string|array|null $from = null;

    private string|array|null $replyTo = null;

    private string $sender = '';

    /**
     * @var array<array{email: string, name: string}>|string[]|string|null
     */
    private string|array|null $recipients = null;

    private bool $bcc = false;
    /**
     * @var string[]
     */
    private array $attachments = [];

    private string $template = '';

    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return static
     */
    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return static
     */
    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getHtmlBody(): string
    {
        return $this->htmlBody;
    }

    /**
     * @return static
     */
    public function setHtmlBody(string $htmlBody): self
    {
        $this->htmlBody = $htmlBody;

        return $this;
    }

    public function getUrlWeb(): string
    {
        return $this->urlWeb;
    }

    /**
     * @return static
     */
    public function setUrlWeb(string $urlWeb): self
    {
        $this->urlWeb = $urlWeb;

        return $this;
    }

    public function getMailSignature(): string
    {
        return $this->mailSignature;
    }

    /**
     * @return static
     */
    public function setMailSignature(string $mailSignature): self
    {
        $this->mailSignature = $mailSignature;

        return $this;
    }

    public function getFrom(): array|string|null
    {
        return $this->from;
    }

    /**
     * @return static
     */
    public function setFrom(array|string $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function getReplyTo(): array|string|null
    {
        return $this->replyTo;
    }

    /**
     * @return static
     */
    public function setReplyTo(array|string $replyTo): self
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    /**
     * @return static
     */
    public function setSender(string $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @return array<array{email: string, name: string}>|string[]|string|null
     */
    public function getRecipients(): array|string|null
    {
        return $this->recipients;
    }

    /**
     * @param array<array{email: string, name: string}>|string[]|string $recipients
     *
     * @return static
     */
    public function setRecipients(array|string $recipients): self
    {
        $this->recipients = $recipients;

        return $this;
    }

    public function isBcc(): bool
    {
        return $this->bcc;
    }

    /**
     * @return $this
     */
    public function setBcc(bool $bcc): self
    {
        $this->bcc = $bcc;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @return static
     */
    public function setAttachments(array $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * @return static
     */
    public function addAttachment(string $attachment): self
    {
        $this->attachments[] = $attachment;

        return $this;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return static
     */
    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }
}
