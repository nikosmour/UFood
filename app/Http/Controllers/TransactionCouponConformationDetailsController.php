<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionConfirmationCouponRequest;
use App\Models\Academic;
use Illuminate\Http\JsonResponse;

class TransactionCouponConformationDetailsController extends Controller
{

    /**
     * Handle the incoming request.
     */
    public function __construct()
    {
        $this->middleware('auth:academics,entryStaffs,couponStaffs,cardApplicationStaffs');

    }

    public function __invoke(TransactionConfirmationCouponRequest $request): JsonResponse
    {
        $receiver = Academic::whereAcademicId($request['receiver_id'])->get(['name', 'status'])->first();
        $receiver->name = $this->stringToSecret($receiver->name);
        return response()->json(
            ["data" => $receiver, "success" => true]
        );
    }

    private function stringToSecret(string $string = NULL): ?string
    {
        if (!$string) {
            return NULL;
        }
        $length = strlen($string);
        $visibleCount = (int)round($length / 4);
        $hiddenCount = $length - ($visibleCount * 2);
        return substr($string, 0, $visibleCount) . str_repeat('*', $hiddenCount) . substr($string, ($visibleCount * -1), $visibleCount);
    }
}
