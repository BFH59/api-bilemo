<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
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