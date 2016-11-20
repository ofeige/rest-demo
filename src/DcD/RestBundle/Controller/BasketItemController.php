<?php

namespace DcD\RestBundle\Controller;

use DcD\RestBundle\Entity\Basket;
use DcD\RestBundle\Entity\BasketItem;
use DcD\RestBundle\Form\Type\BasketItemType;
use FOS\HttpCacheBundle\Configuration\Tag;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 */
class BasketItemController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @Rest\View
     * @Tag(expression="'basket-'~basketId")
     *
     * @param $basketId
     * @return array
     */
    public function cgetAction($basketId)
    {
        $items = $this->getBasketItemRepository()->getItems($basketId);

        return $items;
    }

    /**
     * @Rest\View
     * @Tag(expression="'basket-'~basketId")
     * @Tag(expression="'basketItem-'~itemId")
     *
     * @param $basketId
     * @param $itemId
     * @return array
     */
    public function getAction($basketId, $itemId)
    {
        $items = $this->getBasketItemRepository()->getItem($itemId);

        return $items;
    }

    /**
     * @Rest\View
     * @ParamConverter("basket", class="array", converter="fos_rest.request_body")
     * @Tag(expression="'basket-'~basketId")
     *
     * @param Request $request
     * @param $basketId
     * @return \FOS\RestBundle\View\View|\Symfony\Component\Form\Form
     */
    public function postAction(Request $request, $basketId)
    {
        $em = $this->getDoctrine()->getManager();
        $basket = $em->getRepository('RestBundle:Basket')->find($basketId);

        $basketItem = new BasketItem();
        $basketItem->setBasket($basket);

        $form = $this->createForm(BasketItemType::class, $basketItem );
        $form->submit($request->get('basket'), false);

        if ( $form->isValid() ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist( $basketItem );
            $em->flush();

            //avoid lazy loading
            $basketItem->setBasket(NULL);
            return $this->view($basketItem, Response::HTTP_CREATED);
        }

        return $form;
    }

    /**
     * @Rest\View()
     * @Tag(expression="'basket-'~basketId")
     * @Tag(expression="'basketItem-'~itemId")
     *
     * @param $basketId
     * @param $basketItemId
     * @return \FOS\RestBundle\View\View
     */
    public function deleteAction($basketId, $basketItemId)
    {
        $em = $this->getDoctrine()->getManager();
        $basketItem = $em->getRepository('RestBundle:BasketItem')->find($basketItemId);

        if(!$basketItem instanceof BasketItem) {
            throw $this->createNotFoundException('BasketItem not found');
        }

        $em->remove($basketItem);
        $em->flush();

        return $this->view(null, Response::HTTP_NO_CONTENT);
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
