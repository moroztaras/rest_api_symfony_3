<?php
namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * Update a Post entity.
     * @View(serializerEnableMaxDepthChecks=true)
     * @param Request $request
     * @param $entity
     * @return Response
     */
    public function putAction(Request $request, Post $entity)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $request->setMethod('PATCH'); //Treat all PUTs as PATCH
            $form = $this->createForm(new PostType(), $entity, array("method" => $request->getMethod()));
            $this->removeExtraFields($request, $form);
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->flush();

                return $entity;
            }

            return FOSView::create(array('errors' => $form->getErrors()), Codes::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return FOSView::create($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Partial Update to a Post entity.
     * @View(serializerEnableMaxDepthChecks=true)
     * @param Request $request
     * @param $entity
     * @return Response
     */
    public function patchAction(Request $request, Post $entity)
    {
        return $this->putAction($request, $entity);
    }

    /**
     * Delete a Post entity.
     * @View(statusCode=204)
     * @param Request $request
     * @param $entity
     * @internal param $id
     * @return Response
     */
    public function deleteAction(Request $request, Post $entity)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

            return null;
        } catch (\Exception $e) {
            return FOSView::create($e->getMessage(), Codes::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}