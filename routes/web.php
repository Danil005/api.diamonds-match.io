<?php

use App\Events\NotifyPushed;
use Carbon\Carbon;
use Dejurin\GoogleTranslateForFree;
use Illuminate\Support\Facades\Route;
use Intervention\Image\Facades\Image;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\Media;
use PhpOffice\PhpPresentation\Slide\AbstractBackground;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Font;
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
    $oReader = IOFactory::createReader('PowerPoint2007');
    $file = Storage::disk('public')->path('pptx/questinnaire.pptx');

    $background = Storage::disk('public')->path('pptx/BackgroundFirst.jpg');
    $backgroundWhite = Storage::disk('public')->path('pptx/Font.png');
    $logo = Storage::disk('public')->path('pptx/logo.png');

    $objPHPPowerPoint = new PhpPresentation();
    $oDocumentLayout = new DocumentLayout();
    $oDocumentLayout->setDocumentLayout(DocumentLayout::LAYOUT_A4, false);

    $objPHPPowerPoint = $objPHPPowerPoint->setLayout($oDocumentLayout);
    $currentSlide = $objPHPPowerPoint->getActiveSlide();
    $shape = $currentSlide->createDrawingShape();
    $shape->setName('Background')
        ->setPath($background)
        ->setWidthAndHeight(796.661608, 1126.707)
        ->setOffsetX(0)
        ->setOffsetY(0);

    $shape = $currentSlide->createDrawingShape();
    $shape->setName('WhiteBackground')
        ->setPath($backgroundWhite)
        ->setWidthAndHeight(796.661608, 1126.707)
        ->setOffsetX(0)
        ->setOffsetY(0);

    $shape = $currentSlide->createDrawingShape();
    $shape->setName('Logo')
        ->setPath($logo)
        ->setWidthAndHeight(208.28019645001, 97.1215488)
        ->setOffsetX(294.02031375001)
        ->setOffsetY(65.2535406);

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(87.63702255)
        ->setWidth(522.02832480002)
        ->setOffsetX(137.3359401)
        ->setOffsetY(196.89876495001);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );

    $textRun = $shape->createTextRun('ПРЕДСТАВЬТЕ СЕБЕ БЕЗУПРЕЧНЫЕ ОТНОШЕНИЯ');
    $textRun->getFont()->setName('Georgia Pro Cond')
        ->setSize(24)
        ->setColor( new Color( 'FF464C53' ) );


    $shape = $currentSlide->createRichTextShape()
        ->setHeight(74.73806685)
        ->setWidth(522.02832480002)
        ->setOffsetX(137.3359401)
        ->setOffsetY(265.94611605001);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );

    $textRun = $shape->createTextRun('Позвольте нам создать их!');
    $textRun->getFont()->setName('Marianna')
        ->setSize(40)
        ->setColor( new Color( 'FFD2B690' ) );


    $background2 = Storage::disk('public')->path('pptx/Background2.jpg');
    $backgroundWhite2 = Storage::disk('public')->path('pptx/white.png');
    $shapeUp = Storage::disk('public')->path('pptx/shape_up.png');
    $shapeCircle = Storage::disk('public')->path('pptx/shape_circle.png');
    $objPHPPowerPoint->createSlide();

    $currentSlide = $objPHPPowerPoint->getSlide(1);
    $shape = $currentSlide->createDrawingShape();
    $shape->setName('Background')
        ->setPath($background2)
        ->setWidthAndHeight(796.661608, 1126.707)
        ->setOffsetX(0)
        ->setOffsetY(0);

    $shape = $currentSlide->createDrawingShape();
    $shape->setName('BackgroundWhite')
        ->setPath($backgroundWhite2)
        ->setWidthAndHeight(796.661608, 1126.707)
        ->setOffsetX(0)
        ->setOffsetY(0);

    $shape = $currentSlide->createDrawingShape();
    $shape->setName('ShapeUp')
        ->setPath($shapeUp)
        ->setWidthAndHeight(796.70020500003, 40.2143913)
        ->setOffsetX(0)
        ->setOffsetY(0);

    $shape = $currentSlide->createDrawingShape();
    $shape->setName('Logo')
        ->setPath($logo)
        ->setWidthAndHeight(163.13385150001, 76.25559105)
        ->setOffsetX(316.78317675001)
        ->setOffsetY(72.8411616);

    $shape = $currentSlide->createDrawingShape();
    $shape->setName('ShapeCircle')
        ->setPath($shapeCircle)
        ->setWidthAndHeight(17.4515283, 17.4515283)
        ->setOffsetX(389.62433835001)
        ->setOffsetY(162.37508940001);

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(81.1875447)
        ->setWidth(508.37060700002)
        ->setOffsetX(144.164799)
        ->setOffsetY(170.72147250001);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );

    $textRun = $shape->createTextRun('Кандидаты для Вас');
    $textRun->getFont()->setName('Marianna')
        ->setSize(40)
        ->setColor( new Color( 'FFD2B690' ) );
    $oMedia = new Media();

    // 250,77087405001 - расстояние фоток по ширине
    $row = 1;
    $columns = 1;
    for($i = 0; $i < $row; $i++) {
        for($j = 0; $j < $columns; $i++) {
            $shape = $currentSlide->createDrawingShape();
            $shape->setName('ShapeCircle')

                ->setPath($shapeCircle)
                ->setWidthAndHeight(17.4515283, 17.4515283)
                ->setOffsetX(389.62433835001)
                ->setOffsetY(162.37508940001);
        }
    }


    $oWriterPPTX = IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
    $oWriterPPTX->save(__DIR__ . "/sample.pptx");


//    $file = Storage::disk('public')->path('questionnaire/photos/sign_7daff3f55cccc05843e1e590937745bd/bce3252864dfb4ebefed7b07800c697a.png');
//    $objPHPPowerPoint = new PhpPresentation();
//
//// Create slide
//    $currentSlide = $objPHPPowerPoint->getActiveSlide();
//
//    $shape = $currentSlide->setBackground('');
//// Create a shape (drawing)
//    $shape = $currentSlide->createDrawingShape();
//    $shape->setName('PHPPresentation logo')
//        ->setDescription('PHPPresentation logo')
//        ->setPath($file)
//        ->setHeight(36)
//        ->setOffsetX(10)
//        ->setOffsetY(10);
//    $shape->getShadow()->setVisible(true)
//        ->setDirection(45)
//        ->setDistance(10);
//
//// Create a shape (text)
//    $shape = $currentSlide->createRichTextShape()
//        ->setHeight(300)
//        ->setWidth(600)
//        ->setOffsetX(170)
//        ->setOffsetY(180);
//    $shape->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
//    $textRun = $shape->createTextRun('Thank you for using PHPPresentation!');
//    $textRun->getFont()->setBold(true)
//        ->setSize(60)
//        ->setColor( new Color( 'FFE06B20' ) );
//
//    $oWriterPPTX = IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
//    $oWriterPPTX->save(__DIR__ . "/sample.pptx");c
//    $oReader->load($file);
});

Route::get('circle', function() {

    $image = Storage::disk('public')->path('pptx/BackgroundFirst.jpg');

    $img = Image::make($image)->resize(300, 200);

    return $img->response('jpg');
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
