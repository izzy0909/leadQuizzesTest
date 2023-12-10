<?php
declare(strict_types = 1);

namespace AppBundle\Service\Product;

use AppBundle\Entity\Product;
use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\ProductRepository;
use AppBundle\Validator\Validator;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class UpdateProductService.
 */
class UpdateProductService
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
    private $productRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * UpdateProductService constructor.
     *
     * @param SerializerInterface $serializer
     * @param Validator           $validator
     * @param ProductRepository   $productRepository
     * @param CategoryRepository   $categoryRepository
     */
    public function __construct(
        SerializerInterface $serializer,
        Validator $validator,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->serializer         = $serializer;
        $this->validator          = $validator;
        $this->productRepository  = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param int    $productId
     * @param string $data
     *
     * @return array
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function execute(int $productId, string $data): array
    {
        $product = $this->productRepository->findOneBy(['id' => $productId]);

        if (!$product instanceof Product) {
            throw new EntityNotFoundException('Product does not exist.');
        }

        $dataArray = \json_decode($data, true);

        if (!$dataArray) {
            throw new BadRequestHttpException('Bad request.');
        }

        $category = $dataArray['category'] ? $this->categoryRepository->findOneBy(['name' => $dataArray['category']]) : null;
        unset($dataArray['category']);

        /** @var Product $product */
        $product = $this->serializer->deserialize(\json_encode($dataArray), Product::class, 'json', ['object_to_populate' => $product]);
        $product->setCategory($category);

        /** @var ConstraintViolationList $errors */
        $errors = $this->validator->validate($product);

        if ($errors->count() > 0) {
            throw new BadRequestHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        $this->productRepository->save($product);

        return [
            'status' => 'Success',
        ];
    }
}
