<?php

namespace Feedbee\SmartNotificationService\DeliveryChannel\Email;

use Feedbee\SmartNotificationService\DeliveryChannel\DeliveryChannelInterface;
use Feedbee\SmartNotificationService\DeliveryChannel\Exception\DeliveryFailed as DeliveryFailedException;
use Feedbee\SmartNotificationService\Message\MessageInterface;
use Feedbee\SmartNotificationService\Message\EmailMessage;

/**
 * Delivery channel to send e-mail using simple PHP's mail() function.
 * Not the best choice, but enough to start sending simple e-mails.
 * Better way is to use SMTP-server or SaaS API to send e-mails.
 * Only UTF-8 encoded bodies are supported.
 */
class PhpMail implements DeliveryChannelInterface
{
    public function sendMessage(MessageInterface $message)
    {
        if (!$message instanceof EmailMessage) {
            new \RuntimeException('PhpMail channel can process only messages of type '
                . '`Feedbee\SmartNotificationService\Message\EmailMessage`');
        }

        $headers = $this->setupMailHeaders($message);

        $result = mail($message->getTo(), $message->getSubject(), $message->getBody(), $headers);

        if ($result !== true) {
            throw new DeliveryFailedException;
        }
    }

    /**
     * Compiles SMTP headers to pass as $additionalHeaders parameter to mail() function
     *
     * @param EmailMessage $message
     * @return array
     */
    protected function setupMailHeaders(EmailMessage $message)
    {
        $headers = [];

        ($cc = $message->getCc()) && $headers[] = $this->compileHeader('Cc', $cc);
        ($bcc = $message->getBcc()) && $headers[] = $this->compileHeader('Bcc', $bcc);
        ($from = $message->getFrom()) && $headers[] = $this->compileHeader('From', $from);
        ($sender = $message->getSender()) && $headers[] = $this->compileHeader('Sender', $sender);
        ($replyTo = $message->getReplyTo()) && $headers[] = $this->compileHeader('Reply-To', $replyTo);

        if ($message->isHtml()) {
            $headers[] = $this->compileHeader('MIME-Version', '1.0');
            $headers[] = "Content-Type: text/html; charset=UTF-8";
        } else {
            $headers[] = "Content-Type: text/plain; charset=UTF-8";
        }

        return implode("\n", $headers);
    }

    /**
     * Creates SMTP Header line the simplest way.
     * To get fields encoding, wrapping and other cool features
     * inherit this class, overload this method and do it by yourself.
     * It's a hard work and it wasn't planned to code in bounds of this project.
     *
     * @param $name
     * @param $value
     * @return string
     */
    protected function compileHeader($name, $value)
    {
        return "{$name}: {$value}";
    }
}