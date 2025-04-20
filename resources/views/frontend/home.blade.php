@extends('frontend.layout.web')
@section('title', 'Home Page')
@push('style')
    <style>
        section {
            height: 90vh;
        }

        .carousel-inner .carousel-item img {
            height: 550px;
            object-fit: cover;
        }
    </style>
@endpush
@section('content')
    <section>
        <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="{{ asset('products/banner1.jpg') }}" class="d-block w-100" alt="Banner 1">
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('products/banner2.jpg') }}" class="d-block w-100" alt="Banner 2">
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('products/banner3.jpg') }}" class="d-block w-100" alt="Banner 3">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>
    <section>
        <div class="container">
            <h2>Products</h2>
            <div class="row row-cols-1 row-cols-md-3 g-4 mt-1">
                @foreach($products as $product)
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{$product->name}}</h5>
                            <p class="card-text match-height">{{Str::limit($product->description,100)}}</p>
                            <h6>Rs. {{$product->price}}</h6>
                            <a href="{{route('product.show',$product)}}" class="btn btn-primary float-end">Buy</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-center my-4">
                <a href="{{route('product.index')}}" class="btn btn-primary">View More</a>
            </div>
        </div>
    </section>

@endsection
