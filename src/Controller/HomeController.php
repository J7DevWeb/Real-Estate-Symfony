<?php

namespace App\Controller; // permet d'autocharger par defaut

use App\Repository\PropertyRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends  AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(PropertyRepository $repository): Response
    {
        $properties = $repository->findLatest();
        return $this->render("pages/home.html.twig", ['current_menu' => 'main', 'properties' => $properties]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about(PropertyRepository $repository): Response
    {
        $properties = $repository->findLatest();
        return $this->render("pages/about.html.twig");
    }
}
