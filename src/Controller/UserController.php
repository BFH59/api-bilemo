<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use App\Representation\Users;
use App\Service\ConstraintsChecker;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationList;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class UserController extends AbstractFOSRestController
{
    /**
     * @OA\Response(
     *     response=200,
     *     description="Récupére la liste des utilisateurs",
     *     @OA\JsonContent(
     *     type="array",
     *     @OA\Items(ref=@Model(type=User::class))
     *      )
     *)
     *
     * @Security(name="Bearer")
     * @Rest\Get(
     *     path = "/api/users",
     *     name = "app_user_list"
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
     *     description="Nombre maximum d'utilisateurs par page"
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
     * @param UserRepository $userRepository
     * @param ParamFetcherInterface $paramFetcher
     * @return Users
     */
    public function listAction(UserRepository $userRepository, ParamFetcherInterface $paramFetcher)
    {
        $client = $this->getUser();

        $pager = $userRepository->search(
            $client,
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset'),
            $paramFetcher->get('page')
        );

        return new Users($pager);
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Récupére le détail d'un utilisateur",
     *     @OA\JsonContent(
     *     type="array",
     *     @OA\Items(ref=@Model(type=User::class))
     *      )
     *)
     *
     * @Security(name="Bearer")
     * @Rest\Get(
     *     path = "/api/users/{id}",
     *     name = "app_user_show",
     *     requirements={"id"="\d+"}
     * )
     * @param User $user
     * @return User
     * @throws HttpException
     * @IsGranted("user_view", subject="user", message="Cet utilisateur ne fait pas parti de votre liste.")
     */
    public function showAction(User $user)
    {
        return $user;
    }

    /**
     * @OA\Response(
     *     response=201,
     *     description="Permet de créer un utilisateur rattaché au client connecté. Les champs nom, email et phone sont obligatoires",
     *     @OA\JsonContent(
     *     type="array",
     *     @OA\Items(ref=@Model(type=User::class))
     *      )
     *)
     * @Security(name="Bearer")
     * @Rest\Post(
     *     path="/api/users",
     *     name="app_user_create"
     * )
     * @Rest\View(statusCode=201)
     * @ParamConverter(
     *     "user",
     *     converter="fos_rest.request_body",
     *     options={"validator"={"groups"="Create"}}
     * )
     * @param User $user
     * @param EntityManagerInterface $manager
     * @param ConstraintViolationList $violations
     * @param ConstraintsChecker $checker
     * @return User
     */
    public function createAction(User $user, EntityManagerInterface $manager, ConstraintViolationList $violations, ConstraintsChecker $checker)
    {

        $checker->checkRequest($violations);

        $user->setClient($this->getUser());
        $manager->persist($user);
        $manager->flush();

        return $user;
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Permet de modifier un utilisateur rattaché au client connecté. Les champs nom, email et phone sont obligatoires",
     *)
     * @Security(name="Bearer")
     * @Rest\Put(
     *     path="/api/users/{id}",
     *     name="app_user_update",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View(statusCode=200)
     * @ParamConverter(
     *     "newUser",
     *     converter="fos_rest.request_body",
     *     options={"validator"={"groups"="Update"}}
     *
     * )
     * @param User $user
     * @param User $newUser
     * @param EntityManagerInterface $manager
     * @param ConstraintViolationList $violations
     * @param ConstraintsChecker $checker
     * @return User
     * @IsGranted("user_update", subject="user", message="Cet utilisateur ne fait pas parti de votre liste")
     */
    public function updateAction(User $user, User $newUser, EntityManagerInterface $manager, ConstraintViolationList $violations, ConstraintsChecker $checker)
    {

        $checker->checkRequest($violations);

        $user->setPhone($newUser->getPhone());
        $user->setEmail($newUser->getEmail());
        $user->setName($newUser->getName());

        $manager->flush();

        return $user;
    }

    /**
     *
     * @OA\Response(
     *     response=200,
     *     description="Permet de supprimer un utilisateur rattaché au client connecté.",
     *)
     * @Security(name="Bearer")
     * @Rest\Delete(
     *     path="/api/users/{id}",
     *     name="app_user_delete",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View(statusCode=200)
     * @param User $user
     * @param EntityManagerInterface $manager
     * @IsGranted("user_delete", subject="user", message="Cet utilisateur ne fait pas parti de votre liste")
     *
     */
    public function deleteAction(User $user, EntityManagerInterface $manager)
    {
        $manager->remove($user);
        $manager->flush();

        return;
    }


}