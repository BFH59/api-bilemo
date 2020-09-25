<?php


namespace App\Controller;


use App\Entity\Phone;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

class PhoneController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(
     *     path = "/phones/{id}",
     *     name = "app_phone_show",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View()
     * @param Phone $phone
     * @return Phone
     */
    public function showAction(Phone $phone)
    {
        return $phone;
    }
}