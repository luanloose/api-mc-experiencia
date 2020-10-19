@extends('layouts.app')

@section('title')
    <title>{{ config('app.name', 'Laravel') }} - Cupom</title>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">{{ __('Cupom') }}</div>
                    <div class="card-body" align="center">
                        <img src="{{ $src }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
