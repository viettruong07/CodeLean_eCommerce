<?php

namespace App\Services\OrderDetail;

use App\Repositories\Order\OrderRepositoryInterface;
use App\Services\BaseService;

class OrderDetailService extends BaseService implements OrderDetailServiceInterface
{
    public $repository;
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->repository = $orderRepository;
    }
}
