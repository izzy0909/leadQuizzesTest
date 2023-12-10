<?php
declare(strict_types = 1);

namespace AppBundle\Service\Product;

use AppBundle\Entity\Product;
use AppBundle\Repository\ProductRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductService
{
    private $productRepository;
    private $validator;

    public function __construct(
        ProductRepository $productRepository,
        ValidatorInterface $validator
    ) {
        $this->productRepository = $productRepository;
        $this->validator = $validator;
    }

    /**
     * @return Product[]
     *
     * @throws \Exception
     */
    public function listProducts(): array
    {
        return $this->productRepository->findAllProducts()->getArrayResult();
    }

    /**
     * @param int $id
     *
     * @return array
     *
     * @throws NonUniqueResultException
     */
    public function getSingleProduct(int $id): array
    {
        return $this->productRepository->getSingleProduct($id);
    }

    /**
     * @param $id
     *
     * @return string
     *
     * @throws \Exception
     */
    public function removeProductById(int $id): void
    {
        $this->validateInt($id);
        $product = $this->productRepository->find($id);
        $this->checkIfProductIsFound($product);

        $this->productRepository->remove($product);
    }

    /**
     * @param $id
     *
     * @throws \Exception
     */
    private function validateInt(int $id): void
    {
        $intConstraint = new Assert\Type(['type' => 'integer']);

        $errors = $this->validator->validate(
            $id,
            $intConstraint
        );

        if ($errors->count() > 0) {
            throw new \Exception($errors[0]->getMessage());
        }
    }


    /**
     * @param Product|null $product
     */
    private function checkIfProductIsFound(?Product $product): void
    {
        if (empty($product)) {
            throw new NotFoundHttpException('Product not found.');
        }
    }
}
