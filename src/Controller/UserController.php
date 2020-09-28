<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use App\Representation\Users;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractFOSRestController
{
    /**
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
        //$users = $userRepository->findBy(['client' => $client->getId()]);

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
     * @Rest\Get(
     *     path = "/api/users/{id}",
     *     name = "app_user_show",
     *     requirements={"id"="\d+"}
     * )
     * @param User $user
     * @return User
     * @throws HttpException
     */
    public function showAction(User $user)
    {
        if ($user->getClient()->getId() != $this->getUser()->getId()) {
            throw new HttpException(401, "Cet utilisateur ne fait pas parti de votre compte client");
        }
        return $user;
    }

    /**
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
     * @param ValidatorInterface $validator
     * @return User
     */
    public function createAction(User $user, EntityManagerInterface $manager, ConstraintViolationList $violations, ValidatorInterface $validator)
    {
        //voir pr refactoriser gestion erreurs champs vide et email unique..
        if (count($violations) || count($validator->validate($user))) {
            $message = 'Le Json contient des données incorrectes: ';
            foreach ($violations as $violation) {
                $message .= sprintf(
                    "Champ %s: %s ;",
                    $violation->getPropertyPath(),
                    $violation->getMessage()
                );
            }
            foreach ($validator->validate($user) as $error) {
                $message .= sprintf(
                    "Champ %s: %s ;",
                    $error->getPropertyPath(),
                    $error->getMessage()
                );

            }
            throw new HttpException(400, $message);
        }

        $user->setClient($this->getUser());
        $manager->persist($user);
        $manager->flush();

        return $user;
    }

    /**
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
     * @param ValidatorInterface $validator
     * @return User
     */
    public function updateAction(User $user, User $newUser, EntityManagerInterface $manager, ConstraintViolationList $violations, ValidatorInterface $validator)
    {
        if ($user->getClient()->getId() != $this->getUser()->getId()) {
            throw new HttpException(401, "Cet utilisateur ne fait pas parti de votre compte client");
        }

        //voir pr refactoriser gestion erreurs champs vide et email unique..
        if (count($violations) || count($validator->validate($user))) {
            $message = 'Le Json contient des données incorrectes: ';
            foreach ($violations as $violation) {
                $message .= sprintf(
                    "Champ %s: %s ;",
                    $violation->getPropertyPath(),
                    $violation->getMessage()
                );
            }
            foreach ($validator->validate($user) as $error) {
                $message .= sprintf(
                    "Champ %s: %s ;",
                    $error->getPropertyPath(),
                    $error->getMessage()
                );

            }
            throw new HttpException(400, $message);
        }

        $user->setPhone($newUser->getPhone());
        $user->setEmail($newUser->getEmail());
        $user->setName($newUser->getName());

        $manager->flush();

        return $user;
    }

    /**
     * @Rest\Delete(
     *     path="/api/users/{id}",
     *     name="app_user_delete",
     *     requirements={"id"="\d+"}
     * )
     * @Rest\View(statusCode=200)
     * @param User $user
     * @param EntityManagerInterface $manager
     */
    public function deleteAction(User $user, EntityManagerInterface $manager)
    {
        if ($user->getClient()->getId() != $this->getUser()->getId()) {
            throw new HttpException(401, "Cet utilisateur ne fait pas parti de votre compte client");
        }
        $manager->remove($user);
        $manager->flush();

        return;
    }

}