<?php
declare(strict_types = 1);

namespace AppBundle\Service\User;

use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\ProductRepository;
use AppBundle\Validator\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class CreateUserService.
 */
class CreateUserService
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var ProductRepository
     */
    private $userRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * CreateUserService constructor.
     *
     * @param SerializerInterface $serializer
     * @param Validator           $validator
     * @param ProductRepository   $userRepository
     * @param CategoryRepository   $categoryRepository
     */
    public function __construct(
        SerializerInterface $serializer,
        Validator $validator,
        ProductRepository $userRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->serializer         = $serializer;
        $this->validator          = $validator;
        $this->userRepository  = $userRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param string|array $data
     *
     * @return array
     *
     * @throws BadRequestHttpException
     */
    public function execute($data): array
    {
        if (!$data) {
            throw new BadRequestHttpException('Bad request.');
        }

        /** @var User $user */
        $user = $this->serializer->deserialize(\json_encode($data), User::class, 'json');
        $user->setUsername($data['email']);
        $user->setPassword(uniqid());

        /** @var ConstraintViolationList $errors */
        $errors = $this->validator->validate($user);

        if ($errors->count() > 0) {
            throw new BadRequestHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        $this->userRepository->save($user);

        return [
            'status' => 'Success',
        ];
    }
}
