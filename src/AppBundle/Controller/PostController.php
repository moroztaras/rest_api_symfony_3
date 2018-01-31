<?php
namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\Post;
use AppBundle\Form\PostType;

class PostController extends FOSRestController
{
    /**
     * Create a Post entity.
     * @View(statusCode=201, serializerEnableMaxDepthChecks=true)
     * @param Request $request
     * @return Response
     *
     */
    public function postAction(Request $request)
    {
        $entity = new Post();
        $form = $this->createForm(new PostType(), $entity, array("method" => $request->getMethod()));
        $this->removeExtraFields($request, $form);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $entity;
        }

        return FOSView::create(array('errors' => $form->getErrors()), Codes::HTTP_INTERNAL_SERVER_ERROR);
    }
}