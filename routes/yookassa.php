<?php

/**
 * Route Yookassa Laravel
 *
 */
Route::prefix(env('YOOKASSA_PREFIX_ROUTE', 'yookassa'))->group(function() {

    /**
     * Redirect for Oauth
     */
    Route::get('oauth', function() {
        $yookassa = new \Fiks\YooKassa\YooKassaApi();

        return $yookassa->oauth()->redirect();
    });

    /**
     * Read Webhook Events
     */
    Route::get('webhook', function() {
        $yookassa = new \Fiks\YooKassa\YooKassaApi();

        $yookassa->webhook()->read(request());
    });

    /**
     * Redirect Payment
     */
    $redirect_uri = env('YOOKASSA_REDIRECT', '');

    if(str_contains($redirect_uri, 'http://') || str_contains($redirect_uri, 'https://')) {
        $redirect_uri = preg_replace('/(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]/', '', $redirect_uri);
        $redirect_uri = str_replace('http://', '', $redirect_uri);
        $redirect_uri = str_replace('https://', '', $redirect_uri);
    }

    Route::get($redirect_uri, function() {
        $yookassa = new \Fiks\YooKassa\YooKassaApi();

        $yookassa->checkPayment(request()->get('uniq_id'), function($payment, $invoice) {
            echo "Успешная оплата";
        }, function($payment, $invoice) {
            echo "Произошла ошибка оплаты";
        });
    });
});
