<?php


namespace App\Controller;


use App\Entity\Phone;
use App\Repository\PhoneRepository;
use App\Representation\Phones;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class PhoneController extends AbstractFOSRestController
{
    /**
     * @OA\Response(
     *     response=200,
     *     description="Récupére les détails d'un téléphone",
     *     @OA\JsonContent(
     *     type="array",
     *     @OA\Items(ref=@Model(type=Phone::class))
     *      )
     *)
     *
     * @Security(name="Bearer")
     * @Rest\Get(
     *     path = "/api/phones/{id}",
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

    /**
     * @OA\Response(
     *     response=200,
     *     description="Récupére la liste des téléphones",
     *     @OA\JsonContent(
     *     type="array",
     *     @OA\Items(ref=@Model(type=Phone::class))
     *      )
     *)
     *
     * @Security(name="Bearer")
     * @Rest\Get(
     *     path = "/api/phones",
     *     name = "app_phone_list"
     * )
     * @Rest\QueryParam(
     *     name="keyword",
     *     requirements="[a-zA-Z0-9]",
     *     nullable=true,
     *     description="Mot clé à rechercher"
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     description="asc",
     *     description="Trier par asc ou desc"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="10",
     *     description="Nombre maximum de téléphones par page"
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="0",
     *     description="Pagination offset"
     * )
     * @Rest\QueryParam(
     *     name="page",
     *     requirements="\d+",
     *     default="1",
     *     description="Page à consulter"
     * )
     * @Rest\View()
     * @param ParamFetcherInterface $paramFetcher
     * @param PhoneRepository $phoneRepository
     * @return Phones
     */
    public function listAction(ParamFetcherInterface $paramFetcher, PhoneRepository $phoneRepository)
    {
        $pager = $phoneRepository->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset'),
            $paramFetcher->get('page')
        );

        return new Phones($pager);
    }
}