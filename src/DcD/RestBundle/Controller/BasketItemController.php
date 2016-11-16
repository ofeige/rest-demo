<?php

namespace DcD\RestBundle\Controller;

use DcD\RestBundle\Entity\Basket;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

/**
 */
class BasketItemController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @Rest\View
     */
    public function cgetAction($basketId)
    {
        $items = $this->getBasketItemRepository()->getItems($id);

        return $items;
    }

    /**
     * @Rest\View
     */
    public function getAction($basketId, $itemId)
    {
        $items = $this->getBasketItemRepository()->getItem($itemId);

        return $items;
    }

    /**
     * @Rest\View
     * @ParamConverter("basket", class="array", converter="fos_rest.request_body")
     */
    public function postAction(Request $request, $basketId)
    {
        $em = $this->getDoctrine()->getManager();
        $basket = $em->getRepository('RestBundle:Basket')->find($basketId);

        $basketItem = new \DcD\RestBundle\Entity\BasketItem();
        $basketItem->setBasket($basket);

        $form = $this->createForm(\DcD\RestBundle\Form\Type\BasketItemType::class, $basketItem );
        $form->submit($request->get('basket'), false);

        if ( $form->isValid() ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist( $basketItem );
            $em->flush();

            //avoid lazzy loading
            $basketItem->setBasket(NULL);
            return $this->view($basketItem, \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);
        }

        return $form;
    }

    /**
     * @Rest\View()
     */
    public function deleteAction($basketId, $basketItemId)
    {
        $em = $this->getDoctrine()->getManager();
        $basketItem = $em->getRepository('RestBundle:BasketItem')->find($basketItemId);

        $em->remove($basketItem);
        $em->flush();

        return $this->view(null, \Symfony\Component\HttpFoundation\Response::HTTP_NO_CONTENT);
    }

    /**
     * @return \DcD\RestBundle\Repository\BasketItemRepository
     */
    private function getBasketItemRepository()
    {
        return $this->getDoctrine()
            ->getRepository('RestBundle:BasketItem');
    }
}
