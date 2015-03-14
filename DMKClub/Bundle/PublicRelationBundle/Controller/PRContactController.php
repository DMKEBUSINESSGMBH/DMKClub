<?php

namespace DMKClub\Bundle\PublicRelationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DMKClubPublicRelationBundle:Default:index.html.twig', array('name' => $name));
    }
}
