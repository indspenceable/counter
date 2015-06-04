<?php

namespace PipelineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;

use PipelineBundle\Entity\Thing;

class DefaultController extends Controller
{
    /**
     * @Route("/{name}/{delta}/{direction}")
     * @Method({"GET", "POST"})
     */
    public function updateThing($name, $delta, $direction)
    {
      $response = new JsonResponse();

      if (! preg_match("/\d+/", $delta)) {
        $response->setData(array(
          "success" => false,
          "error" => "invalid count ".$delta,
        ));
        return $response;
      }
      if (! preg_match("/(up|down)/", $direction)) {
        $response->setData(array(
          "success" => false,
          "error" => "invalid direction ".$direction,
        ));
        return $response;
      }

      $thing = $this->ensureThingExistsAndFetch($name);

      if ($direction == "up") {
        $thing->setCount($thing->getCount() + intval($delta));
      } else {
        # direction == down
        $thing->setCount($thing->getCount() - intval($delta));
      }

      $em = $this->getDoctrine()->getManager();
      $em->persist($thing);
      $em->flush();

      $response = new JsonResponse();
      $response->setData(array(
        'success' => true,
        'name' => $thing->getName(),
        'count' => $thing->getCount(),
      ));

      return $response;
    }

    # I'm not aware of a findOrCreate method to do this
    private function ensureThingExistsAndFetch($name) {
      $thing = $this->getDoctrine()
        ->getRepository('PipelineBundle:Thing')
        ->findOneByName($name);

      if (!$thing) {
        $thing = new Thing();
        $thing->setName($name);
        $thing->setCount(0);
      }

      return $thing;
    }
}
