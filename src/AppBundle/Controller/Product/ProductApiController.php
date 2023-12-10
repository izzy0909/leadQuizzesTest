<?php
declare(strict_types = 1);

namespace AppBundle\Controller\Product;

use AppBundle\Service\Product\ProductService;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductApiController extends FOSRestController
{
    private $productService;

    /**
     * ProductApiController constructor.
     *
     * @param ProductService $productService
     */
    public function __construct(
        ProductService $productService
    ) {
        $this->productService = $productService;
    }

    /**
     * @Route("/api/product/list")
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Retrieve list of products."
     *  )
     */
    public function getAllProducts(): JsonResponse
    {
        try {
            $products = $this->productService->listProducts();

            return new JsonResponse(
                [
                    'products' => $products
                ],
                JsonResponse::HTTP_OK
            );
        } catch (\Exception $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/product/get/{id}", methods={"GET"})
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Retrieve a single product by id."
     *  )
     * @param int $id
     *
     * @return JsonResponse
     */
    public function getSingleProducts(int $id): JsonResponse
    {
        try {
            $product = $this->productService->getSingleProduct($id);

            return new JsonResponse(
                [
                    'product' => $product
                ],
                JsonResponse::HTTP_OK
            );
        } catch (\Exception $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_BAD_REQUEST);
        }
    }


    /**
     * @Route("/api/product/remove/{id}", methods={"GET"})
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Remove a single product by id."
     *  )
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function removeProduct(int $id): JsonResponse
    {
        try {
            $this->productService->removeProductById($id);

            return new JsonResponse(
                [
                    'deleted' => true,
                ],
                JsonResponse::HTTP_OK
            );
        } catch (NotFoundHttpException $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}