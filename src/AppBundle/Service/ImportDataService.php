<?php
declare(strict_types = 1);

namespace AppBundle\Service;

use AppBundle\Service\Product\CreateProductService;
use AppBundle\Service\User\CreateUserService;
use AppBundle\Validator\Validator;
use http\Exception\BadUrlException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class ImportDataService.
 */
class ImportDataService
{
    const PRODUCT_KEY = 'products';
    const USER_KEY = 'users';

    /**
     * @var CreateProductService
     */
    private $createProductService;

    /**
     * @var CreateUserService
     */
    private $createUserService;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * ImportDataService constructor.
     *
     * @param CreateProductService $createProductService
     * @param CreateUserService    $createUserService
     * @param Validator            $validator
     */
    public function __construct(
        CreateProductService $createProductService,
        CreateUserService $createUserService,
        Validator $validator
    ) {
        $this->createProductService = $createProductService;
        $this->createUserService    = $createUserService;
        $this->validator            = $validator;
    }

    /**
     * @param string $url
     *
     * @return array
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function execute(string $url): array
    {
        /** @var ConstraintViolationList $errors */
        $errors = $this->validator->validate($url, new Url());

        if ($errors->count() > 0) {
            throw new BadUrlException(json_encode($this->validator->parseErrors($errors)));
        }

        $client = HttpClient::create();
        $response = $client->request('GET', $url);

        if (Response::HTTP_OK !== $response->getStatusCode()) {
            throw new BadRequestHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        $dataForImport = \json_decode($response->getContent(), true);

        $this->importProducts($dataForImport);
        $this->importUsers($dataForImport);

        return [
            'status' => 'Success',
        ];
    }

    /**
     * @param array $dataForImport
     */
    private function importProducts($dataForImport): void
    {
        $productsData = isset($dataForImport[self::PRODUCT_KEY]) ? $dataForImport[self::PRODUCT_KEY] : null;

        foreach ($productsData as $product) {
            $this->createProductService->execute($product);
        }
    }

    /**
     * @param array $dataForImport
     */
    private function importUsers($dataForImport): void
    {
        $usersData = isset($dataForImport[self::USER_KEY]) ? $dataForImport[self::USER_KEY] : null;

        foreach ($usersData as $user) {
            $this->createUserService->execute($user);
        }
    }
}
