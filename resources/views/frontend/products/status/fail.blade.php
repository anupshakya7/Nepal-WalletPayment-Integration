@extends('frontend.layout.web')
@section('title', 'Payment Failed')
@push('style')
<style>

</style>
@endpush
@section('content')
<section>
    <div class="container my-3">
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Well done!</h4>
            <p>Payment Fail</p>
          </div>
    </div>
</section>

@endsection