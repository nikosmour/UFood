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
            $receiver
        );
    }

    private function stringToSecret(string $string = NULL): ?string
    {
        if (!$string) {
            return NULL;
        }
        $array = preg_split('/\s+/', $string);
        $temp = '';
        foreach ($array as $string) {
            $length = mb_strlen($string, 'UTF-8');
            $visibleCount = (int)round($length / 3);
            $hiddenCount = $length - ($visibleCount * 2);
            $temp = $temp . ' ' . mb_substr($string, 0, $visibleCount, 'UTF-8') . str_repeat('*', $hiddenCount) . mb_substr($string, ($visibleCount * -1), $visibleCount, 'UTF-8');
        }
        return $temp;
    }
}
