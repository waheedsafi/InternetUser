<?php
namespace App\Http\Controllers\api\app\Device_type;

use App\Enum\DeviceTypeEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DeviceTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $deviceTypes = collect(DeviceTypeEnum::cases())->map(fn($case) => [
            'id' => $case->value,
            'name' => $case->name,
        ]);

        return response()->json($deviceTypes);
    }
}
