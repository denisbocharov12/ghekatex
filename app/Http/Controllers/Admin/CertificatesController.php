<?php

namespace App\Http\Controllers\Admin;

use App\Data\Schemas\FieldSchema;
use App\Http\Requests\Admin\CertificateRequest;
use App\Managers\CertificateManager;
use App\Repositories\Contracts\CertificateRepositoryInterface;

class CertificatesController extends AdminResourceController
{
    public function __construct(CertificateManager $manager, CertificateRepositoryInterface $repository)
    {
        parent::__construct($manager, $repository);
    }

    protected function pagePrefix(): string
    {
        return 'Certificates';
    }

    protected function routePrefix(): string
    {
        return 'certificates';
    }

    protected function requestClass(): string
    {
        return CertificateRequest::class;
    }

    protected function schema(): FieldSchema
    {
        return new FieldSchema(
            translatable: ['name', 'issuer', 'description'],
            plain: ['number', 'issued_at', 'valid_until', 'sort_order', 'is_active'],
            casts: ['issued_at' => 'date', 'valid_until' => 'date', 'sort_order' => 'int', 'is_active' => 'bool'],
            singleMedia: ['image', 'document'],
        );
    }

    /** @return array<string, mixed> */
    protected function indexProps(): array
    {
        return [];
    }
}
