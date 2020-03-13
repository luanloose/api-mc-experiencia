<?php

Route::name('padrao.')->group(function () {
    // Rotas base para a API, usada para filtar convenio baseado no ID passado via JSON
    Route::post('/consultar', 'RedirecionarConvenio@redirecionar')->name('consultar');
    Route::post('/autorizar', 'RedirecionarConvenio@redirecionar')->name('autorizar');
    Route::post('/cancelar', 'RedirecionarConvenio@redirecionar')->name('cancelar');
    Route::post('/elegibilidade', 'RedirecionarConvenio@redirecionar')->name('elegibilidade');
    Route::post('/carencia', 'RedirecionarConvenio@redirecionar')->name('carencia');
    Route::post('/calculo', 'RedirecionarConvenio@redirecionar')->name('calculo');
});

// Retorna funcoes disponiveis em cada convenio
Route::get('/unimedrio', 'MostrarRotasDisponiveis@unimedrio')->name('unimedrio');
Route::get('/rotas', 'MostrarRotasDisponiveis@padrao')->name('unimedrio');
Route::get('/convenios/{id?}', 'MostrarDisponiveis@convenios')->name('convenios');
