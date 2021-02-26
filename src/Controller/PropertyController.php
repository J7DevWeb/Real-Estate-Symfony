<?php

namespace App\Controller; // permet d'autocharger par defaut

use App\Entity\Contact;
use App\Entity\Property;
use App\Entity\PropertySearch;
use App\Form\ContactType;
use App\Form\PropertySearchType;
use App\Notification\ContactNotification;
use App\Repository\PropertyRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PropertyController extends AbstractController
{
    /**
     * @var PropertyRepository
     */
    private $repository;

    public function __construct(PropertyRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/biens", name="property.index")
     */
    public function index(Request $request): Response
    {
        $search = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $search);
        $form->handleRequest($request);
        $properties = $this->repository->findAllVisible($search);

        if ($properties == []) {
            $properties = $this->repository->findLatest();
        }

        return $this->render("property/index.html.twig", ['current_menu' => 'properties', 'properties' => $properties, 'form' => $form->createView()]);
    }

    /**
     * @Route("/biens/{slug}-{id}", name="property.show", requirements={"slug": "[a-z0-9\-]*"})
     */
    public function show(Property $property, string $slug, Request $request, ContactNotification $notification): Response
    {

        if ($property->getSlug() !== $slug) {
            return $this->redirectToRoute('property.show', ['id' => $property->getId(), 'slug' => $property->getSlug()], 301);
        }

        $contact = new Contact();
        $contact->setProperty($property);
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notification->notify($contact);
            $this->addFlash('success', 'Votre email à bien été envoyé');
            return $this->redirectToRoute('property.show', ['id' => $property->getId(), 'slug' => $property->getSlug()]);
        }

        return $this->render("property/show.html.twig", ['property' => $property, 'current_menu' => 'properties', 'form' => $form->createView()]);
    }
}
