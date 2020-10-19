@extends('layouts.app')

@section('title')
    <title>{{ config('app.name', 'Laravel') }} - Cupom</title>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Preencha para garantir') }}</div>

                    <div class="card-body">
                        <form name="form1" method="POST" action="{{ route('cupom') }}">
                            @csrf

                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right">{{ __('Dados Pessoais') }}</label>
                            </div>

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control" name="name" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="lastname"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Last Name') }}</label>

                                <div class="col-md-6">
                                    <input id="lastname" type="text" class="form-control" name="last_name" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email"
                                    class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="text" class="form-control" name="email" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="age" class="col-md-4 col-form-label text-md-right">{{ __('Age') }}</label>

                                <div class="col-md-6">
                                    <input id="age" type="number" class="form-control" name="age" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-md-4 col-form-label text-md-right">{{ __('Dados da Nota Fiscal') }}</label>
                            </div>

                            <div class="form-group row">
                                <label for="cnpj" class="col-md-4 col-form-label text-md-right">{{ __('CNPJ') }}</label>

                                <div class="col-md-6">
                                    <input id="cnpj" type="text" class="form-control" name="cnpj" maxlength="18" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="nf"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Numero Da nota') }}</label>

                                <div class="col-md-6">
                                    <input id="nf" type="number" class="form-control" name="nf" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="date"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Data da compra') }}</label>

                                <div class="col-md-6">
                                    <input id="date" type="date" class="form-control" name="date" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="time"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Hora da compra') }}</label>

                                <div class="col-md-6">
                                    <input id="time" type="time" class="form-control" name="time" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="text" class="col-md-4 col-form-label text-md-right">{{ __('Opinião') }}</label>

                                <div class="col-md-6">
                                    <input id="text" minlength="120" type="text" class="form-control" name="text" required>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Enviar') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
