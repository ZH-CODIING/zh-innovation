<?php


namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\ProjectDemo;
use App\Models\Package;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
 * استعراض جميع المدفوعات
 */
public function index()
{
    $payments = Payment::with(['project', 'package'])->latest()->get();

    return response()->json([
        'status' => 200,
        'message' => 'تم جلب جميع المدفوعات بنجاح',
        'data' => $payments
    ]);
}

/**
 * استعراض دفع واحد فقط
 */
public function show($id)
{
    $payment = Payment::with(['project', 'package'])->find($id);

    if (!$payment) {
        return response()->json([
            'status' => 404,
            'message' => 'الدفع غير موجود'
        ], 404);
    }

    return response()->json([
        'status' => 200,
        'message' => 'تم جلب بيانات الدفع بنجاح',
        'data' => $payment
    ]);
}

    /**
     * إنشاء دفع جديد
     */
    public function makePayment(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects_demo,id',
            'package_id' => 'required|exists:packages,id',
            'buyer_name' => 'required|string|max:255',
            'buyer_email' => 'nullable|email',
            'buyer_phone' => 'nullable|string|max:20',
        ]);

        $package = Package::find($validated['package_id']);

        $payment = Payment::create([
            'project_id' => $validated['project_id'],
            'package_id' => $validated['package_id'],
            'buyer_name' => $validated['buyer_name'],
            'buyer_email' => $validated['buyer_email'] ?? null,
            'buyer_phone' => $validated['buyer_phone'] ?? null,
            'amount' => $package->price,
            'status' => 'pending', // ممكن بعد الدفع الفعلي يتغير لـ completed
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'تم إنشاء الدفع بنجاح',
            'data' => $payment
        ], 201);
    }
}
