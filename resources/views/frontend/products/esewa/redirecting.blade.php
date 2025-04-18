<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirection to Esewa...</title>
</head>
<body onload="document.forms['esewaForm'].submit();">
        <form action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST" name="esewaForm">
            <input type="hidden" id="amount" name="amount" value="{{$amount}}" required>
            <input type="hidden" id="tax_amount" name="tax_amount" value ="{{$tax}}" required>
            <input type="hidden" id="total_amount" name="total_amount" value="{{$totalAmount}}" required>
            <input type="hidden" id="transaction_uuid" name="transaction_uuid" value="{{$transactionId}}" required>
            <input type="hidden" id="product_code" name="product_code" value ="{{$productCode}}" required>
            <input type="hidden" id="product_service_charge" name="product_service_charge" value="0" required>
            <input type="hidden" id="product_delivery_charge" name="product_delivery_charge" value="0" required>
            <input type="hidden" id="success_url" name="success_url" value="{{$successUrl}}" required>
            <input type="hidden" id="failure_url" name="failure_url" value="{{$failUrl}}" required>
            <input type="hidden" id="signed_field_names" name="signed_field_names" value="{{$signedFields}}" required>
            <input type="hidden" id="signature" name="signature" value="{{$signature}}" required>
            <p>Redirecting...<p>
        </form>
</body>
</html>