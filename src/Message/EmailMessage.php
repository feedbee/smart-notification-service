<?php

namespace Feedbee\SmartNotificationService\Message;

/**
 * RFC 2822 e-mail message
 * https://www.ietf.org/rfc/rfc2822.txt
 */
class EmailMessage implements MessageInterface
{
    /**
     * @var string
     */
    private $to;

    /**
     * @var string
     */
    private $cc;

    /**
     * @var string
     */
    private $bcc;

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $sender;

    /**
     * @var string
     */
    private $replyTo;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $body;

    /**
     * @var bool
     */
    private $isHtml = false;

    /**
     * @param string $to
     * @param string $cc
     * @param string $bcc
     * @param string $from
     * @param string $sender
     * @param string $replyTo
     * @param string $subject
     * @param string $body
     * @param bool $isHtml
     */
    public function __construct($to, $subject, $body, $isHtml, $from, $replyTo, $sender, $cc, $bcc)
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
        $this->isHtml = $isHtml;
        $this->from = $from;
        $this->replyTo = $replyTo;
        $this->sender = $sender;
        $this->cc = $cc;
        $this->bcc = $bcc;
    }

    /**
     * String message type identifier for mapping on delivery channel
     *
     * @return string
     */
    public function getMessageType()
    {
        return 'email';
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @param string $cc
     */
    public function setCc($cc)
    {
        $this->cc = $cc;
    }

    /**
     * @return string
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * @param string $bcc
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
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
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return string
     */
    public function getReplyTo()
    {
        return $this->replyTo;
    }

    /**
     * @param string $replyTo
     */
    public function setReplyTo($replyTo)
    {
        $this->replyTo = $replyTo;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return boolean
     */
    public function isHtml()
    {
        return $this->isHtml;
    }

    /**
     * @param boolean $isHtml
     */
    public function setIsHtml($isHtml)
    {
        $this->isHtml = $isHtml;
    }

    function __toString()
    {
        return "{$this->getTo()} → {$this->getSubject()}";
    }
}