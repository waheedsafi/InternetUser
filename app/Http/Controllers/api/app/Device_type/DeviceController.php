<?php
namespace App\Http\Controllers;

use App\Enum\DeviceTypeEnum as EnumDeviceTypeEnum;
use App\Enums\DeviceTypeEnum;
use Illuminate\Http\JsonResponse;

class DeviceTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $deviceTypes = collect(EnumDeviceTypeEnum::cases())->map(fn($case) => [
            'id' => $case->value,
            'name' => $case->name,
        ]);

        return response()->json($deviceTypes);
    }
}
