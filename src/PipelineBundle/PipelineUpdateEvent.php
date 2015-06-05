<?php

namespace PipelineBundle;

use Symfony\Component\EventDispatcher\Event;

class PipelineUpdateEvent extends Event
{
  private $name;
  private $startCount;
  private $endCount;

  public function __construct($name, $count) {
    $this->name = $name;
    $this->startCount = $count;
  }

  public function setEndCount($count) {
    $this->endCount = $count;
  }

  public function getThingName() {
    return $this->name;
  }

  public function getStartCount() {
    return $this->startCount;
  }

  public function getEndCount() {
    return $this->endCount;
  }
}

