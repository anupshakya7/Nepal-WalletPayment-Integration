@extends('frontend.layout.web')
@section('title', 'Products Page')
@push('style')
<style>

</style>
@endpush
@section('content')
<section>
    <div class="container my-3">
        <h2>Products</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4 mt-1">
            @foreach($products as $product)
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{$product->name}}</h5>
                        <p class="card-text match-height">{{Str::limit($product->description,100)}}</p>
                        <a href="{{route('product.show',$product)}}" class="btn btn-primary float-end">Buy</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="pagination my-4 d-flex justify-content-center">
            {{$products->links()}}
        </div>
    </div>
</section>

@endsection