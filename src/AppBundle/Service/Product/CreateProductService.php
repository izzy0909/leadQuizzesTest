<?php
declare(strict_types = 1);

namespace AppBundle\Service\Product;

use AppBundle\Entity\Product;
use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\ProductRepository;
use AppBundle\Validator\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class CreateProductService.
 */
class CreateProductService
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
     * CreateProductService constructor.
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

        $dataArray = $data;
        if (is_string($data)) {
            $dataArray = \json_decode($data, true);
        }

        $category = $dataArray['category'] ? $this->categoryRepository->findOneBy(['name' => $dataArray['category']]) : null;
        unset($dataArray['category']);

        /** @var Product $product */
        $product = $this->serializer->deserialize(\json_encode($dataArray), Product::class, 'json');
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
