<?php

use App\Events\NotifyPushed;
use Carbon\Carbon;
use Dejurin\GoogleTranslateForFree;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpPresentation\IOFactory;
use setasign\Fpdi\Fpdi;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('test1', function () {
    $information = '15.4.2000';
    $birthday = Carbon::createFromTimeString($information . ' 0:0');
    $now = Carbon::now();

    dd($birthday->diffInYears($now));
});

Route::get('fire', function () {
    event(new NotifyPushed('Появилась новая заявка', [
        'application_id' => 1,
    ]));
});

Route::get('match', function () {
    $match = new \App\Services\MatchProcessorV2();

    $match->start(new \App\Models\Questionnaire);
});

Route::get('pptx', function () {
//    $oReader = IOFactory::createReader('PowerPoint2007');
    $file = Storage::disk('public')->path('pptx/questinnaire.pdf');
    $pdf = new FPDI('p');
    $pagecount = $pdf->setSourceFile($file);
    $tpl = $pdf->importPage(1);
    $pdf->AddPage();
    $pdf->useTemplate($tpl);

    $tpl = $pdf->importPage(2);
    $pdf->AddPage();
    $pdf->useTemplate($tpl);



    $pdf->Output();
//    $oReader->load($file);
});

Route::get('/countries.json', function () {
    $countries = \App\Models\Countries::get();

    return response()->json($countries);
});
Route::get('test', function () {
    $langs = \App\Models\Langs::get();

    foreach ($langs as $item) {
        \App\Models\Langs::where('id', $item['id'])->update([
            'nameRU' => mb_strtolower($item['nameRU']),
            'nameEN' => mb_strtolower($item['nameEN']),
        ]);
    }
});
