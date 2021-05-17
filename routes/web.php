<?php

use App\Events\NotifyPushed;
use Carbon\Carbon;
use Dejurin\GoogleTranslateForFree;
use Illuminate\Support\Facades\Route;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;
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

Route::get('pass', function () {
    echo Hash::make('12345');
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
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $textRun = $shape->createTextRun('ПРЕДСТАВЬТЕ СЕБЕ БЕЗУПРЕЧНЫЕ ОТНОШЕНИЯ');
    $textRun->getFont()->setName('Georgia Pro Cond')
        ->setSize(24)
        ->setColor(new Color('FF464C53'));


    $shape = $currentSlide->createRichTextShape()
        ->setHeight(74.73806685)
        ->setWidth(522.02832480002)
        ->setOffsetX(137.3359401)
        ->setOffsetY(265.94611605001);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $textRun = $shape->createTextRun('Позвольте нам создать их!');
    $textRun->getFont()->setName('Marianna')
        ->setSize(40)
        ->setColor(new Color('FFD2B690'));


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
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $textRun = $shape->createTextRun('Кандидаты для Вас');
    $textRun->getFont()->setName('Marianna')
        ->setSize(40)
        ->setColor(new Color('FFD2B690'));

    // 250,77087405001 - расстояние фоток по ширине
    $girls = [
        [
            'id' => 1,
            'name' => 'Екатерина',
            'age' => '21 год',
            'photo' => 'pptx/70f86bf6abaa314e43fb142f6c0b5957.jpg',
        ],
        [
            'id' => 2,
            'name' => 'Екатерина',
            'age' => '21 год',
            'photo' => 'pptx/70f86bf6abaa314e43fb142f6c0b5957.jpg',
        ],
        [
            'id' => 3,
            'name' => 'Екатерина',
            'age' => '24 год',
            'photo' => 'pptx/70f86bf6abaa314e43fb142f6c0b5957.jpg',
        ],
        [
            'id' => 4,
            'name' => 'Екатерина',
            'age' => '21 год',
            'photo' => 'pptx/70f86bf6abaa314e43fb142f6c0b5957.jpg',
        ],
        [
            'id' => 5,
            'name' => 'Екатерина',
            'age' => '23 год',
            'photo' => 'pptx/70f86bf6abaa314e43fb142f6c0b5957.jpg',
        ],
        [
            'id' => 6,
            'name' => 'Екатерина',
            'age' => '25 год',
            'photo' => 'pptx/70f86bf6abaa314e43fb142f6c0b5957.jpg',
        ]
    ];

    $forPhoto = Storage::disk('public')->path('pptx/forPhoto.png');



    $offsetX = 47.42263125;
    $offsetY = 289.08836010001;


    $offsetXFor = 32.6267703;
    $offsetYFor = 383.93362260001;

    $offsetXName = 73.22054265;
    $offsetYName = 481.81393350002;

    $offsetXYear = 73.22054265;
    $offsetYYear = 523.92523005002;

    $row = 1;
    $elms = 0;
    foreach ($girls as $key => $item) {
        $girlPhoto = Storage::disk('public')->path($item['photo']);

        $ims = new \App\Utils\Img();

        list($w, $s, $source_type) = getimagesize($girlPhoto);
        $ims->create($w, $s, true);

        $img2 = new \App\Utils\Img($girlPhoto);
        $img2->circleCrop();
        $ims->merge($img2, 0, 0);



        $shape = $currentSlide->createDrawingShape();
        $shape->setName('ForPhoto_' . $item['id'])
            ->setPath($forPhoto)
            ->setWidthAndHeight(224.21420055001, 211.69462590001)
            ->setOffsetX($offsetXFor)
            ->setOffsetY($offsetYFor);

        $shape = $currentSlide->createDrawingShape();
        $shape->setName('GirlPhoto_' . $item['id'])
            ->setPath($ims->render())
            ->setWidthAndHeight(189.69052500001, 194.62247865001)
            ->setOffsetX($offsetX)
            ->setOffsetY($offsetY);

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(42.11129655)
            ->setWidth(143.02665585)
            ->setOffsetX($offsetXName)
            ->setOffsetY($offsetYName);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


        $textRun = $shape->createTextRun($item['name']);
        $textRun->getFont()->setName('Georgia Pro Cond')
            ->setSize(20)
            ->setColor(new Color('FF464C53'));

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(29.21234085)
            ->setWidth(143.02665585)
            ->setOffsetX($offsetXYear)
            ->setOffsetY($offsetYYear);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


        $textRun = $shape->createTextRun($item['age']);
        $textRun->getFont()->setName('Yu Gothic UI Light')
            ->setSize(12)
            ->setColor(new Color('FF464C53'));

        $offsetX += 257.59973295;
        $offsetXFor += 257.979114;
        $offsetXYear += 257.59973295;
        $offsetXName += 257.59973295;

        $elms++;
        if ($elms == 3) {
            $row++;
            $offsetX = 47.42263125;
            $offsetY += 371.0346669;

            $offsetXFor = 32.6267703;
            $offsetYFor += 371.0346669;

            $offsetXYear = 73.22054265;
            $offsetYYear += 371.41404795;

            $offsetXName = 73.22054265;
            $offsetYName += 371.793429;
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

Route::get('circle', function () {

    $image1 = Storage::disk('public')->path('pptx/BackgroundFirst.jpg');

    $manager = new ImageManager(array('driver' => 'imagick'));

    $image = $manager->make($image1);
    $image->encode('png');

    $width = $image->getWidth();
    $height = $image->getHeight();
    $mask = $manager->canvas($width, $height);

    $mask->circle($width, $width / 2, $height / 2, function ($draw) {
        $draw->background('#fff');
    });

    $image->mask($mask, false);

    return $image->response('png');
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
