<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GHNService
{
    protected $baseUrl;
    protected $token;
    protected $shopId;

    public function __construct()
    {
        $this->baseUrl = config('services.ghn.url');
        $this->token   = config('services.ghn.token');
        $this->shopId  = config('services.ghn.shop_id');
    }

    // Lấy phí vận chuyển
    public function calculateShippingFee($toDistrictId, $toWardCode, $weight = 1000, $serviceId = null)
    {
        // Nếu chưa có serviceId thì gọi API lấy
        if (!$serviceId) {
            $serviceId = $this->getServiceId($toDistrictId);
        }

        $response = Http::withHeaders([
            'Token'  => $this->token,
            'ShopId' => $this->shopId,
        ])->post($this->baseUrl . '/v2/shipping-order/fee', [
            'from_district_id' => 1450, // Kho của bạn
            'from_ward_code'   => '21010',
            'service_id'       => $serviceId,
            'to_district_id'   => $toDistrictId,
            'to_ward_code'     => $toWardCode,
            'weight'           => $weight, // gram
        ]);

        return $response->json();
    }

    // Lấy serviceId của GHN
    public function getServiceId($toDistrictId)
    {
        $response = Http::withHeaders([
            'Token'  => $this->token,
            'ShopId' => $this->shopId,
        ])->get($this->baseUrl . '/v2/shipping-order/available-services', [
            'shop_id'         => $this->shopId,
            'from_district'   => 1450, // Kho bạn
            'to_district'     => $toDistrictId,
        ]);

        $data = $response->json();
        return $data['data'][0]['service_id'] ?? null;
    }
}
