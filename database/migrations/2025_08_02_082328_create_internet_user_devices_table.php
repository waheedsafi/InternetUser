 <?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('internet_user_devices', function (Blueprint $table) {
            $table->unsignedBigInteger('internet_user_id');
            $table->unsignedBigInteger('device_type_id');
             $table->string('mac_address')->nullable();
            $table->timestamps();
            $table->foreign('internet_user_id')->references('id')->on('internet_users')->onDelete('cascade');
            $table->foreign('device_type_id')->references('id')->on('device_types')->onDelete('cascade');
             $table->primary(['internet_user_id', 'device_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internet_user_devices');
    }
};
