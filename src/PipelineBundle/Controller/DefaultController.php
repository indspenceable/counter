<?php

namespace PipelineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/{thing}/{delta}/{direction}", requirements={"id" = "\d+"})
     * @Method({"GET", "POST"})
     */
    public function updateThing($thing, $delta, $direction)
    {

    }

    private function ensureThingExists($thing) {

    }
}
