<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function esewaPay(Request $request){
        $validatedData = $request->validate([
            'product_id'=>'required|exists:products,id',
            'amount'=>'required'
        ]);
        $productId = $validatedData['product_id'];
        $amount = $validatedData['amount'];
        $transactionId = uniqid();
        $tax = 0;
        $totalAmount = $amount+$tax;
        $productCode = 'EPAYTEST';
        $secretKey = '8gBm/:&EnhH.1/q';
        
        $signedFields = 'total_amount,transaction_uuid,product_code';
        $message = "total_amount=$totalAmount,transaction_uuid=$transactionId,product_code=$productCode";
        $signature = base64_encode(hash_hmac('sha256',$message,$secretKey,true));

        session(['pending_transaction_uuid'=>$transactionId]);

        Order::create([
            'transaction_uuid'=>$transactionId,
            'amount'=>$totalAmount,
            'product_code'=>$productCode,
            'product_id'=>$productId,
            'status'=>'PENDING',
            'payment_method'=>'Esewa',
        ]);
        
        return view('frontend.products.esewa.redirecting',[
            'amount'=>$amount,
            'tax'=>$tax,
            'totalAmount'=>$totalAmount,
            'transactionId'=>$transactionId,
            'productCode'=>$productCode,
            'successUrl'=>route('esewa.success'),
            'failUrl'=>route('esewa.fail'),
            'signedFields'=>$signedFields,
            'signature'=>$signature,
        ]);
    }

    // public function redirectPage(){
    //     return view('frontend.products.esewa.redirecting');
    // }

    public function esewaSuccess(Request $request){
        try{
            $encodedData = $request->get('data');
            $decodedJson = base64_decode($encodedData);
            $responseData = json_decode($decodedJson,true);

            $transactionUUID = $responseData['transaction_uuid'] ?? null;
            $status = $responseData['status'] ?? null;
            $transactionCode = $responseData['transaction_code'] ?? null;

            $signedFieldNames = explode(',',$responseData['signed_field_names']);
            $dataToSign = '';
            foreach($signedFieldNames as $fields){
                $dataToSign .= $fields.'='.$responseData[$fields].',';
            }
            $dataToSign = rtrim($dataToSign,',');
          
            $secretKey = '8gBm/:&EnhH.1/q';
            $expectedSignature = base64_encode(hash_hmac('sha256',$dataToSign,$secretKey,true));

            if($expectedSignature !== $responseData['signature']){
                return redirect()->route('esewa.fail')->with('error','Invalid Payment Response');
            }

            $order = Order::where('transaction_uuid',$transactionUUID)->first();

            if($order){
                if(strtolower($status) == 'complete'){
                    $order->update([
                        'status'=>'COMPLETED',
                        'reference_id'=>$transactionCode
                    ]);

                    // return redirect()->route('esewa.status')->with('success','Payment Successful!!!');
                    return view('frontend.products.status.success');
                }else{
                    $order->update([
                        'status'=>'CANCELLED',
                        'reference_id'=>$transactionCode,
                    ]);
                    return redirect()->route('esewa.fail')->with('error','Payment Fail');
                }
            }else{
                return redirect()->route('esewa.fail')->with('error','Payment Record Not Found');
            }

        }catch(\Exception $e){
            Log::error($e->getMessage());
            return redirect()->route('esewa.fail')->with('error','Something went wrong');
        }
    }

    // public function status(){
    //     return view('frontend.products.esewa.status');
    // }

    public function esewaFail(){
        $transactionUUID = session('pending_transaction_uuid');

        if($transactionUUID){
            $order = Order::where('transaction_uuid',$transactionUUID)->first();

            if($order && $order->status == Order::STATUS_PENDING){
                $order->update([
                    'status'=>Order::STATUS_FAILED,
                    'reference_id'=>null
                ]);
            }
        }
        return view('frontend.products.status.fail')->with('error','Payment Failed or was Cancelled');
    }
}
