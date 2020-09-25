<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(
     *     path = "/api/users",
     *     name = "app_user_list"
     * )
     * @param UserRepository $userRepository
     * @return \App\Entity\User[]
     */
    public function listAction(UserRepository $userRepository)
    {
        $client = $this->getUser();
        $users = $userRepository->findBy(['client' => $client->getId()]);
        return $users;
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
        $clientId = $this->getUser()->getId();
        if($user->getClient()->getId() != $clientId){
            throw new HttpException(401, "Cet utilisateur ne fait pas parti de votre compte client");
        }
        return $user;
    }

}