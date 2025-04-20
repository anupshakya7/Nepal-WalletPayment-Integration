<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
class OrderController extends Controller
{
    public function esewaPay(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'amount' => 'required'
        ]);
        $productId = $validatedData['product_id'];
        $amount = $validatedData['amount'];
        $transactionId = uniqid();
        $tax = 0;
        $totalAmount = $amount + $tax;
        $productCode = 'EPAYTEST';
        $secretKey = '8gBm/:&EnhH.1/q';

        $signedFields = 'total_amount,transaction_uuid,product_code';
        $message = "total_amount=$totalAmount,transaction_uuid=$transactionId,product_code=$productCode";
        $signature = base64_encode(hash_hmac('sha256', $message, $secretKey, true));

        session(['pending_transaction_uuid' => $transactionId]);

        Order::create([
            'transaction_uuid' => $transactionId,
            'amount' => $totalAmount,
            'product_code' => $productCode,
            'product_id' => $productId,
            'status' => 'PENDING',
            'payment_method' => 'Esewa',
        ]);

        return view('frontend.products.esewa.redirecting', [
            'amount' => $amount,
            'tax' => $tax,
            'totalAmount' => $totalAmount,
            'transactionId' => $transactionId,
            'productCode' => $productCode,
            'successUrl' => route('esewa.success'),
            'failUrl' => route('esewa.fail'),
            'signedFields' => $signedFields,
            'signature' => $signature,
        ]);
    }

    // public function redirectPage(){
    //     return view('frontend.products.esewa.redirecting');
    // }

    public function esewaSuccess(Request $request)
    {
        try {
            $encodedData = $request->get('data');
            $decodedJson = base64_decode($encodedData);
            $responseData = json_decode($decodedJson, true);

            $transactionUUID = $responseData['transaction_uuid'] ?? null;
            $status = $responseData['status'] ?? null;
            $transactionCode = $responseData['transaction_code'] ?? null;

            $signedFieldNames = explode(',', $responseData['signed_field_names']);
            $dataToSign = '';
            foreach ($signedFieldNames as $fields) {
                $dataToSign .= $fields . '=' . $responseData[$fields] . ',';
            }
            $dataToSign = rtrim($dataToSign, ',');

            $secretKey = '8gBm/:&EnhH.1/q';
            $expectedSignature = base64_encode(hash_hmac('sha256', $dataToSign, $secretKey, true));

            if ($expectedSignature !== $responseData['signature']) {
                return redirect()->route('esewa.fail')->with('error', 'Invalid Payment Response');
            }

            $order = Order::where('transaction_uuid', $transactionUUID)->first();

            if ($order) {
                if (strtolower($status) == 'complete') {
                    $order->update([
                        'status' => 'COMPLETED',
                        'reference_id' => $transactionCode
                    ]);

                    return view('frontend.products.status.success');
                } else {
                    $order->update([
                        'status' => 'CANCELLED',
                        'reference_id' => $transactionCode,
                    ]);
                    return redirect()->route('esewa.fail')->with('error', 'Payment Fail');
                }
            } else {
                return redirect()->route('esewa.fail')->with('error', 'Payment Record Not Found');
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('esewa.fail')->with('error', 'Something went wrong');
        }
    }

    public function esewaFail()
    {
        $transactionUUID = session('pending_transaction_uuid');

        if ($transactionUUID) {
            $order = Order::where('transaction_uuid', $transactionUUID)->first();

            if ($order && $order->status == Order::STATUS_PENDING) {
                $order->update([
                    'status' => Order::STATUS_FAILED,
                    'reference_id' => null
                ]);
            }
        }
        return view('frontend.products.status.fail')->with('error', 'Payment Failed or was Cancelled');
    }

    public function khaltiPay(Product $product)
    {
        $payload = [
            'return_url' => route('khalti.callback'),
            'website_url' => url('/'),
            'amount' => $product->price * 100,
            'purchase_order_id' => uniqid(),
            'purchase_order_name' => $product->name,
            'customer_info' => [
                "name" => "Test User",
                "email" => "test@gmail.com",
                "phone" => "9800000001",
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Key ' . config('services.khalti.key'),
            'Content-Type' => 'application/json',
        ])->post(config('services.khalti.base_url') . 'epayment/initiate/', $payload);

        $data = $response->json();
        session(['pending_transaction_uuid' => $data['pidx']]);
        
        if($response->successful()){
            Order::create([
                'transaction_uuid'=>$data['pidx'],
                'amount'=>$product->price,
                'product_code'=>'KHALTI-'.$product->id,
                'product_id'=>$product->id,
                'status'=>Order::STATUS_PENDING,
                'payment_method'=>'Khalti'
            ]);

            return redirect($data['payment_url']);
        }else{
            return redirect()->back()->with('error','Unable to initiate payment');
        }
    }

    public function khaltiCallBack(Request $request)
    {
        $pidx = $request->get('pidx');

        $response = Http::withHeaders([
            'Authorization'=>'Key '.config('services.khalti.key'),
            'Content-Type'=>'application/json'
        ])->post(config('services.khalti.base_url').'epayment/lookup/',[
            'pidx'=>$pidx
        ]);

        $data=$response->json();

        if($data && isset($data['status']) == 'Completed'){
            Order::where('transaction_uuid',$data['pidx'])->update([
                'status'=>Order::STATUS_COMPLETED,
                'reference_id'=>$data['transaction_id'],
                'updated_at'=>now()
            ]);

            return view('frontend.products.status.success');
        }else{
            return redirect()->route('khalti.fail');
        }
    }

    public function khaltiFail()
    {
        $transactionUUID = session('pending_transaction_uuid');

        if ($transactionUUID) {
            $order = Order::where('transaction_uuid', $transactionUUID)->first();

            if ($order && $order->status == Order::STATUS_PENDING) {
                $order->update([
                    'status' => Order::STATUS_FAILED,
                    'reference_id' => null
                ]);
            }
        }
        return view('frontend.products.status.fail')->with('error', 'Payment Failed or was Cancelled');
    }
}
