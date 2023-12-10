<?php
declare(strict_types = 1);

namespace AppBundle\Controller\Product;

use AppBundle\Service\Product\UpdateProductService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UpdateProductController.
 */
class UpdateProductController extends AbstractFOSRestController
{
    /**
     * @var UpdateProductService
     */
    private $updateProductService;

    /**
     * UpdateProductController constructor.
     *
     * @param UpdateProductService $updateProductService
     */
    public function __construct(
        UpdateProductService $updateProductService
    ) {
        $this->updateProductService = $updateProductService;
    }

    /**
     * @Route("/api/product/update/{id}", methods={"POST"})
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Update a single product by id."
     *  )
     *
     * @param int     $id
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateProduct(int $id, Request $request): JsonResponse
    {
        try {
            $this->updateProductService->execute($id, is_string($request->getContent()) ? $request->getContent() : '');

            return new JsonResponse(
                [
                    'updated' => true,
                ],
                JsonResponse::HTTP_OK
            );
        } catch (\Exception $exception) {
            return new JsonResponse("{$exception->getMessage()}", JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
