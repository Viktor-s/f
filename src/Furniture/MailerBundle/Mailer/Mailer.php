<?php

namespace Furniture\MailerBundle\Mailer;

class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    private $swiftMailer;

    /**
     * @var \Twig_Environment $twig
     */
    private $twig;

    /**
     * @var string
     */
    private $defaultFromName;

    /**
     * @var string
     */
    private $defaultFromEmail;

    /**
     * Construct
     *
     * @param \Swift_Mailer     $swiftMailer
     * @param \Twig_Environment $twig
     * @param string            $defaultFromName
     * @param string            $defaultFromEmail
     */
    public function __construct(
        \Swift_Mailer $swiftMailer,
        \Twig_Environment $twig,
        $defaultFromName = 'Agenta',
        $defaultFromEmail = 'no-reply@agenta.solution'
    )
    {
        $this->swiftMailer = $swiftMailer;
        $this->twig = $twig;
        $this->defaultFromName = $defaultFromName;
        $this->defaultFromEmail = $defaultFromEmail;
    }

    /**
     * Send message to
     *
     * @param string $toEmail
     * @param string $subject
     * @param string $template
     * @param array  $context
     * @param string $toName
     * @param string $fromName
     * @param string $fromEmail
     *
     * @return int
     */
    public function send($toEmail, $subject, $template, array $context = [], $toName = null, $fromEmail = null, $fromName = null)
    {
        $message = $this->createMessage($toEmail, $subject, $template, $context, $toName, $fromEmail, $fromName);

        return $this->sendSwiftMessage($message);
    }

    /**
     * Create message
     *
     * @param string $toEmail
     * @param string $subject
     * @param string $template
     * @param array  $context
     * @param string $toName
     * @param string $fromName
     * @param string $fromEmail
     *
     * @return \Swift_Message
     */
    public function createMessage($toEmail, $subject, $template, array $context = [], $toName = null, $fromEmail = null, $fromName = null)
    {
        $message = $this->createMessageFromTemplate($template, $context);

        if ($fromEmail) {
            $message->setFrom($fromEmail, $fromName);
        }

        $message->setSubject($subject);
        $message->setTo($toEmail, $toName);

        return $message;
    }

    /**
     * Send swift message
     *
     * @param \Swift_Message $message
     *
     * @return int
     */
    public function sendSwiftMessage(\Swift_Message $message)
    {
        $this->prepareMessageBeforeSend($message);

        return $this->swiftMailer->send($message);
    }

    /**
     * Create message from template
     *
     * @param string $template
     * @param array  $parameters
     *
     * @return \Swift_Message
     */
    public function createMessageFromTemplate($template, array $parameters)
    {
        $template = $this->twig->loadTemplate($template);
        $content = $template->render($parameters);

        /** @var \Swift_Message $message */
        $message = $this->swiftMailer->createMessage();
        $message->setBody($content, 'text/html', 'UTF-8');

        return $message;
    }

    /**
     * Prepare message before send
     *
     * @param \Swift_Message $message
     */
    private function prepareMessageBeforeSend(\Swift_Message $message)
    {
        $this->prepareForDefaultFrom($message);
    }

    /**
     * Prepare for default from
     *
     * @param \Swift_Message $message
     */
    private function prepareForDefaultFrom(\Swift_Message $message)
    {
        if (!$message->getFrom()) {
            $message->setFrom($this->defaultFromEmail, $this->defaultFromName);
        }
    }
}
