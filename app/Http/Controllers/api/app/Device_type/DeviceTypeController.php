<?php
namespace App\Http\Controllers\api\app\Device_type;

use App\Enum\DeviceTypeEnum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DeviceTypeController extends Controller
{
   public function index(){
    $device = DB::table('device_type')->select('id','name');
    return response()->json($device);

   }
}
