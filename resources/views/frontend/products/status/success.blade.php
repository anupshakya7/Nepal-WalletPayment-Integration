@extends('frontend.layout.web')
@section('title', 'Payment Success')
@push('style')
<style>

</style>
@endpush
@section('content')
<section>
    <div class="container my-3">
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Well done!</h4>
            <p>Payment Successful!!!</p>
          </div>
    </div>
</section>

@endsection