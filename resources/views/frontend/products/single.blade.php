@extends('frontend.layout.web')
@section('title')
    @push('style')
        <style>
            .match-height {
                display: flex;
                justify-content: center;
            }

            .payment .card{
                padding: 5px;
            }

            .payment .card:hover{
                transform: scale(1.01);
                transition: 0.3s ease;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            }
        </style>
    @endpush
@section('content')
    <section>
        <div class="container my-3">
            <h2>Products</h2>
            <div class="row row-cols-1 row-cols-md-3 g-4 mt-1">
                <div class="col-md-9">
                    <div class="card p-3 my-3">
                        <h2>{{ $product->name }}</h2>
                        <p>{{ $product->description }}</p>
                        <h6>Rs. {{$product->price}}</h6>
                    </div>
                    <div class="card p-3 my-3 payment">
                        <h5>Select Payment Method</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <a href="javascript:void(0)" onclick="document.getElementById('esewa-form').submit();">
                                    <div class="card match-height" style="cursor: pointer;">
                                        <img src="{{ asset('products/esewa.webp') }}" alt="Esewa">
                                    </div>
                                </a>
                                <form id="esewa-form" action="{{route('esewa.pay')}}" method="POST" style="display:none;">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{$product->id}}">
                                    <input type="hidden" name="amount" value="{{$product->price}}">
                                </form>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('khalti.pay',$product) }}">
                                    <div class="card match-height">
                                        <img src="{{ asset('products/khalti.png') }}" alt="Esewa">
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3">
                        <h4>Related Products</h4>
                        @foreach ($relateds as $related)
                            <a href="{{ route('product.show', $related) }}" style="text-decoration: none;">
                                <div class="card p-3 my-2">
                                    <h4>{{ $related->name }}</h4>
                                    <p>{{ Str::limit($related->description, 50) }}</p>
                                    <h6>Rs. {{$related->price}}</h6>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@push('script')
<script>
function handleEsewaPay(){
    var path = "https://rc-epay.esewa.com.np/api/epay/main/v2/form";

    var formData = {
       amount:@json($product->price),
       tax_amount:0,
       total_amount:@json($product->price),
       transaction_uuid:@json($product->price),
       product_code:@json($product->price),
       product_service_charge:@json($product->price),
       product_delivery_charge:@json($product->price),
       success_url:@json($product->price),
       failure_url:@json($product->price),
       signed_field_names:@json($product->price),
       signature:@json($product->price),
    };

    console.log(formData);
    var form = document.createElement("form");
    form.setAttribute("method","POST");
    form.setAttribute("action",path);

    var hiddenField = document.createElement('input');
    hiddenField.setAttribute('type','hidden');
    hiddenField.setAttribute('name', key);
    hiddenField.setAttribute('value',formData(key));
}
</script>
@endpush
