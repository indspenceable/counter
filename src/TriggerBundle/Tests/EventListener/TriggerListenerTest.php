<?php

namespace TriggerBundle\Tests\EventListener;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use TriggerBundle\EventListener\TriggerListener;
use PipelineBundle\PipelineUpdateEvent;


class TriggerListenerTest extends WebTestCase
{
  protected $mailer;
  protected $twig;
  protected $triggerListener;

  protected function setUp() {
    $this->mailer = $this
      ->getMockBuilder('Swift_Mailer')
      ->disableOriginalConstructor()
      ->getMock();
    $this->twig = $this->getMockBuilder('Twig_Environment')->getMock();
    $this->triggerListener = new TriggerListener($this->twig, $this->mailer);
  }

  # FWIW this degree of testing is probably excessive for this task,
  # but whatever.

  public function testBananaTriggerCausesMailerToSend() {
    $event = new PipelineUpdateEvent('bananas', 3);
    $event->setEndCount(-3);

    $this->mailer
      ->expects($this->once())
      ->method('send')
      ->with($this->isInstanceOf('\Swift_Message'));

    $this->triggerListener->checkBananasCount($event);
  }

  public function testBananaTriggerCausesNoMailerToSendWhenAscending() {
    $event = new PipelineUpdateEvent('bananas', -3);
    $event->setEndCount(6);

    $this->mailer
      ->expects($this->never())
      ->method('send');

    $this->triggerListener->checkBananasCount($event);
  }

  public function testNoBananaTriggerCausesNoMailerToSend() {
    $event = new PipelineUpdateEvent('bananas', 3);
    $event->setEndCount(6);

    $this->mailer
      ->expects($this->never())
      ->method('send');

    $this->triggerListener->checkBananasCount($event);
  }

  public function testOtherStringsDoNotTriggerBananas() {
    $event = new PipelineUpdateEvent('burnanas', 3);
    $event->setEndCount(-6);

    $this->mailer
      ->expects($this->never())
      ->method('send');

    $this->triggerListener->checkBananasCount($event);
  }


  public function testMessagesTriggerCausesMailerToSendAtTen() {
    $event = new PipelineUpdateEvent('messages', 3);
    $event->setEndCount(13);

    $this->mailer
      ->expects($this->once())
      ->method('send')
      ->with($this->isInstanceOf('\Swift_Message'));

    $this->triggerListener->checkMessagesCount($event);
  }

  public function testMessagesTriggerCausesMailerToSendAtOneHundred() {
    $event = new PipelineUpdateEvent('messages', 94);
    $event->setEndCount(106);

    $this->mailer
      ->expects($this->once())
      ->method('send')
      ->with($this->isInstanceOf('\Swift_Message'));

    $this->triggerListener->checkMessagesCount($event);
  }

  public function testMessagesSendOnlyOneMessage() {
    $event = new PipelineUpdateEvent('messages', 4);
    $event->setEndCount(106);

    $this->mailer
      ->expects($this->once())
      ->method('send')
      ->with($this->isInstanceOf('\Swift_Message'));

    $this->triggerListener->checkMessagesCount($event);
  }

  public function testMessagesTriggerUsuallyCausesNoMail() {
    $event = new PipelineUpdateEvent('messages', 3);
    $event->setEndCount(6);

    $this->mailer
      ->expects($this->never())
      ->method('send');

    $this->triggerListener->checkMessagesCount($event);
  }
}
