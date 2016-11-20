<?php

namespace DcD\RestBundle\Controller;

use DcD\RestBundle\Entity\Basket;
use DcD\RestBundle\Form\Type\BasketType;
use FOS\HttpCacheBundle\Configuration\Tag;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class BasketController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @Rest\View()
     * @Tag("basket")
     */
    public function cgetAction()
    {
        $baskets = $this->getDoctrine()
            ->getRepository('RestBundle:Basket')
            ->getBaskets();

        return $baskets;
    }

    /**
     * @Rest\View()
     * @Tag(expression="'basket-'~id")
     *
     * @param $id
     * @return array
     */
    public function getAction($id)
    {
        $basket = $this->getDoctrine()
            ->getRepository('RestBundle:Basket')
            ->getBasket($id);

        if(empty($basket)) {
            throw $this->createNotFoundException('Basket not found');
        }

        return $basket;
    }

    /**
     * @Rest\View()
     * @ParamConverter("basket", class="array", converter="fos_rest.request_body")
     *
     * @Tag("basket")
     *
     * @param Request $request
     * @return \FOS\RestBundle\View\View|\Symfony\Component\Form\Form
     */
    public function postAction(Request $request)
    {
        $basket = new Basket();

        $form = $this->createForm(BasketType::class, $basket );
        $form->submit($request->get('basket'));
        $form->handleRequest( $request );

        if ( $form->isValid() ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist( $basket );
            $em->flush();

            return $this->view($this->getAction($basket->getId()), Response::HTTP_CREATED);
        }

        return $form;
    }

    /**
     * @Rest\View()
     * @ParamConverter("basket", class="array", converter="fos_rest.request_body")
     *
     * @Tag("basket")
     * @Tag(expression="'basket-'~id")
     *
     * @param Request $request
     * @param $id
     * @return \FOS\RestBundle\View\View|\Symfony\Component\Form\Form
     */
    public function putAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $basket = $em->getRepository('RestBundle:Basket')->find($id);

        $form = $this->createForm(BasketType::class, $basket);
        $form->submit( $request->get('basket'), false );

        if ($form->isValid()) {
            $em->persist($basket);
            $em->flush();

            return $this->view($this->getAction($basket->getId()), Response::HTTP_ACCEPTED);
        }

        return $form;
    }

    /**
     * @Rest\View()
     *
     * @Tag("basket")
     * @Tag(expression="'basket-'~id")
     *
     * @param $id
     * @return \FOS\RestBundle\View\View
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $basket = $em->getRepository('RestBundle:Basket')->find($id);

        $em->remove($basket);
        $em->flush();

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}
