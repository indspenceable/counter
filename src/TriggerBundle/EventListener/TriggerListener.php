<?php

namespace TriggerBundle\EventListener;

use PipelineBundle\PipelineUpdateEvent;

class TriggerListener
{
    protected $twig;
    protected $mailer;

    public function __construct(\Twig_Environment $twig, \Swift_Mailer $mailer)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
    }

  public function checkBananasCount(PipelineUpdateEvent $event) {
    if ($event->getThingName() == 'bananas' &&
        $event->getStartCount() > 0 &&
        $event->getEndCount() <= 0) {
      $message = $this->buildMessage('daniel.patrick.spencer@gmail.com',
        'from@example.com',
        'Bananas threshold alert',
        $this->twig->render('TriggerBundle:Default:bananas.html.twig',
          array('startCount' => $event->getStartCount(),
            'endCount' => $event->getEndCount()))
        );
      $this->mailer->send($message);
    }
  }

  public function checkMessagesCount(PipelineUpdateEvent $event) {
    $startCountDigits = strlen((string) $event->getStartCount());
    $endCountDigits = strlen((string) $event->getEndCount());

    if ($event->getThingName() == 'messages' &&
        $startCountDigits < $endCountDigits) {
      $message = $this->buildMessage('daniel.patrick.spencer@gmail.com',
        'from@example.com',
        'Messages threshold alert',
        $this->twig->render('TriggerBundle:Default:bananas.html.twig',
          array('startCount' => $event->getStartCount(),
            'endCount' => $event->getEndCount()))
        );
      $this->mailer->send($message);
    }
  }

  private function buildMessage($to, $from, $subject, $body) {
    return \Swift_Message::newInstance()
      ->setSubject($subject)
      ->setFrom($from)
      ->setTo($to)
      ->setBody($body, 'text/html');
  }
}
