<?php

namespace DcD\RestBundle\Controller;

use DcD\RestBundle\Entity\Basket;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class BasketController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @Rest\View
     */
    public function cgetAction()
    {
        $baskets = $this->getDoctrine()
            ->getRepository('RestBundle:Basket')
            ->findAll();

        return array('baskets' => $baskets);
    }

    /**
     * @Rest\View
     */
    public function getAction($id)
    {
        $basket = $this->getDoctrine()
            ->getRepository('RestBundle:Basket')
            ->find($id);

        if (!$basket instanceof Basket) {
            throw new NotFoundHttpException('Basket not found');
        }

        return array('baskets' => $basket);
    }

    /**
     * @Rest\View
     * @ParamConverter("basket", class="array", converter="fos_rest.request_body")
     */
    public function postAction(Request $request)
    {
        $basket = new \DcD\RestBundle\Entity\Basket();

        $form = $this->createForm(\DcD\RestBundle\Form\Type\BasketType::class, $basket );
        $form->submit($request->get('basket'));
        $form->handleRequest( $request );

        if ( $form->isValid() ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist( $basket );
            $em->flush();

            return $this->redirectView(
                $this->generateUrl(
                    'get_basket',
                    array( 'id' => $basket->getId() )
                ),
                \Symfony\Component\HttpFoundation\Response::HTTP_CREATED
            );
        }

        return array(
            'errors' => $form->getErrors()
        );
    }

    /**
     * @Rest\View()
     * @ParamConverter("basket", class="array", converter="fos_rest.request_body")
     */
    public function putAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $basket = $em->getRepository('RestBundle:Basket')->find($id);

        $form = $this->createForm(\DcD\RestBundle\Form\Type\BasketType::class, $basket);
        $form->submit( $request->get('basket'), false );

        if ($form->isValid()) {
            $em->persist($form->getData());
            $em->flush();

            return $this->redirectView(
                $this->generateUrl(
                    'get_basket',
                    array( 'id' => $basket->getId() )
                ),
                \Symfony\Component\HttpFoundation\Response::HTTP_ACCEPTED
            );
        }

        return array(
            'errors' => $form->getErrors()
        );
    }
    /**
     * @Rest\View()
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $basket = $em->getRepository('RestBundle:Basket')->find($id);

        $em->remove($basket);
        $em->flush();

        return $this->view(null, \Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT);

    }
}
