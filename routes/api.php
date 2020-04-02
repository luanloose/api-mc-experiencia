<?php

Route::name('padrao.')->group(function () {
    // Rotas base para a API, usada para filtar convenio baseado no ID passado via JSON
    Route::post('/cupom', 'McDonalds@cupom')->name('cupom');

});

