<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;

class PaymentController extends Controller
{

    /**
     * Create a payment request to VNPay
     *
     * @param  Request  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
     */
    public function createPayment(Request $request, $orderId)
    {
        $order = \App\Models\Order::findOrFail($orderId);

        if ($order->total_amount <= 0) {
            return redirect()->back()->with('error', 'Số tiền đơn hàng không hợp lệ');
        }

        $vnp_TmnCode = config('vnpay.vnp_tmncode');
        $vnp_HashSecret = config('vnpay.vnp_hashsecret');
        $vnp_Url = config('vnpay.vnp_url');
        $vnp_Returnurl = config('vnpay.vnp_returnurl');

        $vnp_TxnRef = $order->code;
        $vnp_OrderInfo = "Thanh toan don hang #" . $order->code;
        $vnp_OrderType = 'billpayment';

        $vnp_Amount = (int) (round($order->total_amount * 100));

        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip();

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        ];

        $inputData = array_filter($inputData, function ($value) {
            return $value !== '' && $value !== null;
        });

        ksort($inputData);

        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        Log::info('VNPay Hash Debug', [
            'hashData' => $hashData,
            'secret' => substr($vnp_HashSecret, 0, 10) . '...' // Chỉ log 10 ký tự đầu
        ]);

        $vnpSecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        $paymentUrl = $vnp_Url . "?" . http_build_query($inputData, '', '&') . '&vnp_SecureHash=' . $vnpSecureHash;

        return redirect($paymentUrl);
    }

    /**
     * Handle the return response from VNPay
     */
    public function paymentReturn(Request $request)
    {
        $vnp_HashSecret = config('vnpay.vnp_hashsecret');
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';

        if (empty($vnp_SecureHash)) {
            return redirect()->route('user.cart.index')->with('error', 'Thiếu chữ ký xác thực!');
        }

        unset($inputData['vnp_SecureHash']);

        $inputData = array_filter($inputData, function ($value) {
            return $value !== '' && $value !== null;
        });

        ksort($inputData);

        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        Log::info('VNPay Return Hash Debug', [
            'hashData' => $hashData,
            'calculated_hash' => $secureHash,
            'received_hash' => $vnp_SecureHash,
            'match' => ($secureHash === $vnp_SecureHash)
        ]);

        if ($secureHash === $vnp_SecureHash) {
            if ($request->vnp_ResponseCode == '00') {
                $order = \App\Models\Order::where('code', $request->vnp_TxnRef)->first();

                if (!$order) {
                    return redirect()->route('user.cart.index')->with('error', 'Không tìm thấy đơn hàng!');
                }

                if ($order->status !== 'completed' && $order->status !== 'confirmed') {
                    $order->status = 'confirmed';
                    $order->save();

                    try {
                        if ($order->user && !empty($order->user->email)) {
                            Mail::to($order->user->email)->send(new InvoiceMail($order));
                        }
                    } catch (\Exception $e) {
                        Log::error('Lỗi gửi mail hóa đơn VNPay: ' . $e->getMessage());
                    }
                }

                return redirect()->route('user.checkout.success', $order->code)
                    ->with('success', 'Thanh toán thành công!');
            } else {
                Log::warning('VNPay Payment Failed', [
                    'response_code' => $request->vnp_ResponseCode,
                    'order_id' => $request->vnp_TxnRef
                ]);

                return redirect()->route('user.cart.index')
                    ->with('error', 'Thanh toán thất bại! Mã lỗi: ' . $request->vnp_ResponseCode);
            }
        } else {
            Log::error('VNPay Hash Mismatch', [
                'calculated' => $secureHash,
                'received' => $vnp_SecureHash,
                'hashData' => $hashData
            ]);

            return redirect()->route('user.cart.index')->with('error', 'Sai chữ ký hash!');
        }
    }

    public function createMomoPayment($orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->total_amount <= 0) {
            return redirect()->back()->with('error', 'Số tiền đơn hàng không hợp lệ');
        }

        $endpoint = config('momo.endpoint');
        $partnerCode = config('momo.partner_code');
        $accessKey = config('momo.access_key');
        $secretKey = config('momo.secret_key');

        $orderInfo = "Thanh toan don hang #" . $order->code;
        $amount = (string) round($order->total_amount);
        $orderIdMomo = $order->code . "_" . time(); // Thêm time() để đảm bảo orderId gửi lên MoMo là duy nhất
        $redirectUrl = route('payment.momo.return');
        $ipnUrl = route('payment.momo.return');
        $extraData = "";
        $requestId = time() . "";
        $requestType = "captureWallet"; // Thanh toán qua ứng dụng (quét QR)

        $rawHash = "accessKey=" . $accessKey .
            "&amount=" . $amount .
            "&extraData=" . $extraData .
            "&ipnUrl=" . $ipnUrl .
            "&orderId=" . $orderIdMomo .
            "&orderInfo=" . $orderInfo .
            "&partnerCode=" . $partnerCode .
            "&redirectUrl=" . $redirectUrl .
            "&requestId=" . $requestId .
            "&requestType=" . $requestType;

        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => "Test Store",
            'storeId' => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderIdMomo,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        ];

        try {
            $response = Http::post($endpoint, $data);
            $json = $response->json();

            if (isset($json['resultCode']) && $json['resultCode'] == 0) {
                // Chuyển hướng người dùng sang trang thanh toán của MoMo
                return redirect($json['payUrl']);
            } else {
                Log::error('Momo Create Error: ' . json_encode($json));
                return redirect()->route('user.cart.index')->with('error', 'Lỗi tạo thanh toán MoMo: ' . ($json['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Momo Exception: ' . $e->getMessage());
            return redirect()->route('user.cart.index')->with('error', 'Lỗi kết nối đến cổng MoMo');
        }
    }


    public function momoReturn(Request $request)
    {
        $partnerCode = config('momo.partner_code');
        $accessKey = config('momo.access_key');
        $secretKey = config('momo.secret_key');

        $orderId = $request->orderId;
        $requestId = $request->requestId;
        $amount = $request->amount;
        $orderInfo = $request->orderInfo;
        $orderType = $request->orderType;
        $transId = $request->transId;
        $resultCode = $request->resultCode;
        $message = $request->message;
        $payType = $request->payType;
        $responseTime = $request->responseTime;
        $extraData = $request->extraData;
        $momoSignature = $request->signature;

        // Tính toán lại chữ ký để đối chiếu
        $rawHash = "accessKey=" . $accessKey .
            "&amount=" . $amount .
            "&extraData=" . $extraData .
            "&message=" . $message .
            "&orderId=" . $orderId .
            "&orderInfo=" . $orderInfo .
            "&orderType=" . $orderType .
            "&partnerCode=" . $partnerCode .
            "&payType=" . $payType .
            "&requestId=" . $requestId .
            "&responseTime=" . $responseTime .
            "&resultCode=" . $resultCode .
            "&transId=" . $transId;

        $partnerSignature = hash_hmac("sha256", $rawHash, $secretKey);

        // Kiểm tra chữ ký bảo mật
        if (hash_equals($partnerSignature, (string) $momoSignature)) {
            if ($resultCode == '0') {
                // Tách lấy order code gốc
                $parts = explode('_', $orderId);
                $originalCode = $parts[0];

                $order = Order::where('code', $originalCode)->first();

                if (!$order) {
                    return redirect()->route('user.cart.index')->with('error', 'Không tìm thấy đơn hàng');
                }
                if ($order->status !== 'completed' && $order->status !== 'confirmed') {
                    $order->status = 'confirmed';
                    $order->save();

                    // Gửi email xác nhận
                    try {
                        if ($order->user && !empty($order->user->email)) {
                            Mail::to($order->user->email)->send(new InvoiceMail($order));
                        }
                    } catch (\Exception $e) {
                        Log::error('Lỗi gửi mail hóa đơn MoMo: ' . $e->getMessage());
                    }
                }
                return redirect()->route('user.checkout.success', $order->code)->with('success', 'Thanh toán MoMo thành công!');

            } else {
                return redirect()->route('user.cart.index')->with('error', 'Thanh toán thất bại hoặc đã bị hủy: ' . $message);
            }
        }

        Log::error('Momo Signature Mismatch', [
            'orderId' => $orderId,
            'received' => $momoSignature,
        ]);

        return redirect()->route('user.cart.index')->with('error', 'Chữ ký MoMo không hợp lệ!');
    }
}