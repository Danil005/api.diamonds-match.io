<?php

use App\Events\NotifyPushed;
use App\Mail\CreateEmployee;
use App\Models\PayPal;
use App\Models\User;
use Carbon\Carbon;
use Dejurin\GoogleTranslateForFree;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\Media;
use PhpOffice\PhpPresentation\Slide\AbstractBackground;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Font;
use Stripe\Exception\InvalidRequestException;

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
//
//Route::get('/payment', function() {
//    $request = request()->all();
//
//    YooKassa::checkPayment($request['uniq_id'], function($response, $invoice) {
//
//    });
//});
//Route::any('/yookassa/callback', function() {
//    YooKassa::webhook()->read(request());
//});
//
Route::get('/createTestPayment', function() {
    YooKassa::webhook()->callback()->success(function($payment, $invoice) {
        Storage::disk('public')->put('payment/test.txt', 'test');
    });
    return redirect()->to(
        YooKassa::createPayment(1, 'RUB', 'Test Description')->response()->getConfirmation()->getConfirmationUrl()
    )->send();
});

Route::prefix('stripe')->group(function() {
    Route::get('success', function() {
        return redirect()->to('https://client.diamondsmatch.org/paySuccess')->send();
    });

    Route::get('cancel', function() {
        return redirect()->to('https://client.diamondsmatch.org/payFail');
    });

    Route::any('webhook', function() {
        $request = request();

        $stripe = new \Stripe\StripeClient('sk_test_51J6CsDHtIMZ16lIwJnxTGlZb6hRWIVK7WR9jt9kKdnlJ5DaVZdo3C5P9081CXtsEuUv0YF52c7quTNfDl3Yi03Kc00NqTf1MB9');

        $data = $request->all();

        if( !empty($data) ) {
            try {
                $id = $data['data']['object']['payment_intent'];
                \App\Models\StripePayment::where('payment_id', $id)->update([
                    'paid' => $data['data']['object']['payment_status'] == 'paid'
                ]);

                $res = $stripe->paymentIntents->confirm($id);
            } catch(InvalidRequestException $exception) {

            }
        }
        echo 'ok';
    });
});

Route::get('/paypal/order', function() {
    $clientId = env('PAYPAL_CLIENT_ID');
    $clientSecret = env('PAYPAL_SECRET');

    $environment = new SandboxEnvironment($clientId, $clientSecret);
    $client = new PayPalHttpClient($environment);

    $request = new OrdersCaptureRequest(request()->get('token'));
    $request->prefer('return=representation');
    try {
        // Call API with your client and get a response for your call
        $response = $client->execute($request);
        PayPal::where('order_id', request()->get('token'))->update([
            'status' => $response->result->status
        ]);
        return redirect()->to('https://clinet.diamondsmatch.org/paySuccess')->send();

    } catch(\PayPalHttp\HttpException $ex) {
        return redirect()->to('https://client.diamondsmatch.org/payFail')->send();
    }
});

Route::get('match3', function() {
    $questionnaire = new \App\Models\Questionnaire();
    (new \App\Services\MatchProcessorV3($questionnaire))->start($questionnaire);
});

Route::get('/online', function() {
    User::where('online', true)->update(['online' => false]);
});

Route::get('test1', function () {
    $information = '15.4.2000';
    $birthday = Carbon::createFromTimeString($information . ' 0:0');
    $now = Carbon::now();

    dd($birthday->diffInYears($now));
});

Route::get('uc', function() {
    $zodiac = [
        'aries' => '????????',
        'calf' => '??????????',
        'twins' => '????????????????',
        'cancer' => '??????',
        'lion' => '??????',
        'virgo' => '????????',
        'libra' => '????????',
        'scorpio' => '????????????????',
        'sagittarius' => '??????????????',
        'capricorn' => '??????????????',
        'aquarius' => '??????????????',
        'fish' => '????????'
    ];

    $search = '??';
    $find = collect($zodiac)->filter(function ($item) use ($search) {
        // replace stristr with your choice of matching function
        return false !== stristr($item, $search);
    });
    $find->toArray();
});

Route::get('fire', function () {
    event(new NotifyPushed('?????????????????? ?????????? ????????????', [
        'application_id' => 1,
    ]));
});

Route::get('pdf1', function() {
   return view('presa');
});

Route::get('/getSlide/{slide}/{questionnaireId}', 'App\Http\Controllers\Api\v1\QuestionnaireController@getSlide');

Route::get('/generate', function () {
    $connection = ssh2_connect('45.141.79.57', 22);
    ssh2_auth_password($connection, env('SSH_U'), env('SSH_P'));

    for($i = 1; $i <= 5; $i++ ) {
        $stream = ssh2_exec($connection, 'wkhtmltoimage https://api.diamondsmatch.org/getSlide/'.$i.' /var/www/html/storage/app/public/pptx/generate/s'.$i.'.jpg');
        stream_set_blocking($stream, true);
        stream_get_contents($stream);
    }
    for($i = 1; $i <= 5; $i++ ) {
        $stream = ssh2_exec($connection, 'convert /var/www/html/storage/app/public/pptx/generate/s'.$i.'.jpg -crop 784x1119+0+0 /var/www/html/storage/app/public/pptx/generate/s'.$i.'.jpg');
        stream_set_blocking($stream, true);
        stream_get_contents($stream);
    }

    $slides = '/var/www/html/storage/app/public/pptx/generate/s1.jpg /var/www/html/storage/app/public/pptx/generate/s2.jpg'
             .' /var/www/html/storage/app/public/pptx/generate/s3.jpg'
             .' /var/www/html/storage/app/public/pptx/generate/s4.jpg /var/www/html/storage/app/public/pptx/generate/s5.jpg';

    $stream = ssh2_exec($connection, 'convert '.$slides.' /var/www/html/storage/app/public/pptx/generate/result.pdf');
    stream_set_blocking($stream, true);
    $stream_out = ssh2_fetch_stream( $stream, SSH2_STREAM_STDIO );
    echo '?????????????????? ??????????????????';
});

Route::get('convert', function() {

    $pdf = PDF::loadView('test');

    return $pdf->download('itsolutionstuff.pdf');
//    define('INVOICE_DIR', public_path('uploads/invoices'));
//
//    if (!is_dir(INVOICE_DIR)) {
//        mkdir(INVOICE_DIR, 0755, true);
//    }
//
//    $outputName = Str::random(10);
//    $pdfPath = INVOICE_DIR.'/'.$outputName.'.pdf';
//
//
//    File::put($pdfPath, $document);
//
//    $headers = [
//        'Content-Type' => 'application/pdf',
//        'Content-Disposition' =>  'attachment; filename="'.'filename.pdf'.'"',
//    ];
//
//    return response()->download($pdfPath, 'filename.pdf', $headers);
//    $dompdf = new DOMPDF();
//    $dompdf->load_html(view('presa')->render());
//    $dompdf->render();
//
//    $fileName = "invoice.pdf";
//    $dompdf->stream($fileName);//DOWNLOAD PDF
});

Route::get('/ssh', function() {
    $connection = ssh2_connect('45.141.79.57', 22);
    ssh2_auth_password($connection, env('SSH_U'), env('SSH_P'));

    $stream = ssh2_exec($connection, 'unoconv -f pdf /var/www/html/storage/app/public/questionnaire/pptx/1/presentation.pptx');
});

Route::get('match', function () {
    $match = new \App\Services\MatchProcessorV2();

    $match->start(new \App\Models\Questionnaire);
});

Route::get('/delete/{id}', function($id) {
    $q = \App\Models\Questionnaire::where('id', $id);

    \App\Models\QuestionnaireMyAppearance::where('id', $q->my_appearance_id)->delete();
    \App\Models\QuestionnaireMyInformation::where('id', $q->my_information_id)->delete();
    \App\Models\QuestionnaireMyPersonalQualities::where('id', $q->my_personal_qualities_id)->delete();
    \App\Models\QuestionnairePartnerInformation::where('id', $q->partner_information_id)->delete();
    \App\Models\QuestionnairePartnerAppearance::where('id', $q->partner_appearance_id)->delete();
    \App\Models\QuestionnairePersonalQualitiesPartner::where('id', $q->personal_qualities_partner_id)->delete();
    \App\Models\QuestionnaireAppointedDate::where('questionnaire_id', $q->id)->delete();
    \App\Models\QuestionnaireMatch::where('questionnaire_id', $q->id)->delete();
    \App\Models\QuestionnaireFiles::where('questionnaire_id', $q->id)->delete();
    \App\Models\QuestionnaireUploadPhoto::where('questionnaire_id', $q->id)->delete();

    echo '??????????????';
});

Route::get('/sendMail', function(\Illuminate\Http\Request $request) {
    $email = $request->get('email');
    $name = $request->get('name');
    $country = $request->get('country');
    $lang = $request->get('lang');
    $app_id = $request->get('app_id');

    Mail::to($email)->send(new \App\Mail\SendPrice(
        name: $name ?? '????????',
        lang: $lang,
        country: $country,
        app_id: $app_id
    ));
});

Route::get('pass', function () {
    echo Hash::make('12345');
});

Route::get('tp', function() {
   $questionnaire = new \App\Models\Questionnaire();
   $pptx = new \App\Services\PptxCreator();

   $pptx->create($questionnaire, 4);
});

Route::get('pptx', function () {
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

    $textRun = $shape->createTextRun('?????????????????????? ???????? ?????????????????????? ??????????????????');
    $textRun->getFont()->setName('Georgia Pro Cond')
        ->setSize(24)
        ->setColor(new Color('FF464C53'));


    $shape = $currentSlide->createRichTextShape()
        ->setHeight(74.73806685)
        ->setWidth(522.02832480002)
        ->setOffsetX(137.3359401)
        ->setOffsetY(265.94611605001);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $textRun = $shape->createTextRun('?????????????????? ?????? ?????????????? ????!');
    $textRun->getFont()->setName('Marianna')
        ->setSize(40)
        ->setColor(new Color('FFD2B690'));


    $background2 = Storage::disk('public')->path('pptx/Background2.jpg');
    $backgroundWhite2 = Storage::disk('public')->path('pptx/white.png');
    $shapeUp = Storage::disk('public')->path('pptx/shape_up.png');
    $shapeCircle = Storage::disk('public')->path('pptx/shape_circle.png');
//    $objPHPPowerPoint->createSlide();

//    $currentSlide = $objPHPPowerPoint->getSlide(1);
//    $shape = $currentSlide->createDrawingShape();
//    $shape->setName('Background')
//        ->setPath($background2)
//        ->setWidthAndHeight(796.661608, 1126.707)
//        ->setOffsetX(0)
//        ->setOffsetY(0);
//
//    $shape = $currentSlide->createDrawingShape();
//    $shape->setName('BackgroundWhite')
//        ->setPath($backgroundWhite2)
//        ->setWidthAndHeight(796.661608, 1126.707)
//        ->setOffsetX(0)
//        ->setOffsetY(0);
//
//    $shape = $currentSlide->createDrawingShape();
//    $shape->setName('ShapeUp')
//        ->setPath($shapeUp)
//        ->setWidthAndHeight(796.70020500003, 40.2143913)
//        ->setOffsetX(0)
//        ->setOffsetY(0);
//
//    $shape = $currentSlide->createDrawingShape();
//    $shape->setName('Logo')
//        ->setPath($logo)
//        ->setWidthAndHeight(163.13385150001, 76.25559105)
//        ->setOffsetX(316.78317675001)
//        ->setOffsetY(72.8411616);
//
//    $shape = $currentSlide->createDrawingShape();
//    $shape->setName('ShapeCircle')
//        ->setPath($shapeCircle)
//        ->setWidthAndHeight(17.4515283, 17.4515283)
//        ->setOffsetX(389.62433835001)
//        ->setOffsetY(162.37508940001);
//
//    $shape = $currentSlide->createRichTextShape()
//        ->setHeight(81.1875447)
//        ->setWidth(508.37060700002)
//        ->setOffsetX(144.164799)
//        ->setOffsetY(170.72147250001);
//    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
//
//    $textRun = $shape->createTextRun('?????????????????? ?????? ??????');
//    $textRun->getFont()->setName('Marianna')
//        ->setSize(40)
//        ->setColor(new Color('FFD2B690'));

//    // 250,77087405001 - ???????????????????? ?????????? ???? ????????????
//    $girls = [
//        [
//            'id' => 1,
//            'name' => '??????????????????',
//            'age' => '21 ??????',
//            'photo' => 'pptx/70f86bf6abaa314e43fb142f6c0b5957.jpg',
//        ],
//    ];
//
//    $forPhoto = Storage::disk('public')->path('pptx/forPhoto.png');
//
//
//
//    $offsetX = 47.42263125;
//    $offsetY = 289.08836010001;
//
//
//    $offsetXFor = 32.6267703;
//    $offsetYFor = 383.93362260001;
//
//    $offsetXName = 73.22054265;
//    $offsetYName = 481.81393350002;
//
//    $offsetXYear = 73.22054265;
//    $offsetYYear = 523.92523005002;
//
//    $row = 1;
//    $elms = 0;
//    foreach ($girls as $key => $item) {
//        $girlPhoto = Storage::disk('public')->path($item['photo']);
//
//        $ims = new \App\Utils\Img();
//
//        list($w, $s, $source_type) = getimagesize($girlPhoto);
//        $ims->create($w, $s, true);
//
//        $img2 = new \App\Utils\Img($girlPhoto);
//        $img2->circleCrop();
//        $ims->merge($img2, 0, 0);
//
//
//
//        $shape = $currentSlide->createDrawingShape();
//        $shape->setName('ForPhoto_' . $item['id'])
//            ->setPath($forPhoto)
//            ->setWidthAndHeight(224.21420055001, 211.69462590001)
//            ->setOffsetX($offsetXFor)
//            ->setOffsetY($offsetYFor);
//
//        $shape = $currentSlide->createDrawingShape();
//        $shape->setName('GirlPhoto_' . $item['id'])
//            ->setPath($ims->render())
//            ->setWidthAndHeight(189.69052500001, 194.62247865001)
//            ->setOffsetX($offsetX)
//            ->setOffsetY($offsetY);
//
//        $shape = $currentSlide->createRichTextShape()
//            ->setHeight(42.11129655)
//            ->setWidth(143.02665585)
//            ->setOffsetX($offsetXName)
//            ->setOffsetY($offsetYName);
//        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
//
//
//        $textRun = $shape->createTextRun($item['name']);
//        $textRun->getFont()->setName('Georgia Pro Cond')
//            ->setSize(20)
//            ->setColor(new Color('FF464C53'));
//
//        $shape = $currentSlide->createRichTextShape()
//            ->setHeight(29.21234085)
//            ->setWidth(143.02665585)
//            ->setOffsetX($offsetXYear)
//            ->setOffsetY($offsetYYear);
//        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
//
//
//        $textRun = $shape->createTextRun($item['age']);
//        $textRun->getFont()->setName('Yu Gothic UI Light')
//            ->setSize(12)
//            ->setColor(new Color('FF464C53'));
//
//        $offsetX += 257.59973295;
//        $offsetXFor += 257.979114;
//        $offsetXYear += 257.59973295;
//        $offsetXName += 257.59973295;
//
//        $elms++;
//        if ($elms == 3) {
//            $row++;
//            $offsetX = 47.42263125;
//            $offsetY += 371.0346669;
//
//            $offsetXFor = 32.6267703;
//            $offsetYFor += 371.0346669;
//
//            $offsetXYear = 73.22054265;
//            $offsetYYear += 371.41404795;
//
//            $offsetXName = 73.22054265;
//            $offsetYName += 371.793429;
//        }
//    }

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
        ->setOffsetY(1086.61417324);

    $shape = $currentSlide->createDrawingShape();
    $shape->setName('Logo')
        ->setPath($logo)
        ->setWidthAndHeight(163.13385150001, 76.25559105)
        ->setOffsetX(603.21259843)
        ->setOffsetY(40.06299213);

    $circleBig = Storage::disk('public')->path('pptx/CircleBig.png');

    $shape = $currentSlide->createDrawingShape();
    $shape->setName('CircleBig')
        ->setPath($circleBig)
        ->setWidthAndHeight(360.18897638000004, 360.18897638000004)
        ->setOffsetX(216.56692913643002)
        ->setOffsetY(80.12598425291999);

    $shape = $currentSlide->createDrawingShape();
    $shape->setName('ShapeCircle')
        ->setPath($shapeCircle)
        ->setWidthAndHeight(17.4515283, 17.4515283)
        ->setOffsetX(388.15748031957)
        ->setOffsetY(716.97637796127);

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(80.88188976)
        ->setWidth(162.89763779721)
        ->setOffsetX(324.66141733)
        ->setOffsetY(741.16535434);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $textRun = $shape->createTextRun('Hello!');
    $textRun->getFont()->setName('Marianna')
        ->setSize(44)
        ->setColor(new Color('FFD2B690'));


    $shape = $currentSlide->createRichTextShape()
        ->setHeight(108.85039370208)
        ->setWidth(411.59055118598997)
        ->setOffsetX(191.62204724637)
        ->setOffsetY(571.4645669359201);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $textRun = $shape->createTextRun('??????????????????');
    $textRun->getFont()->setName('Georgia Pro Cond')
        ->setSize(36)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(38.929133858730005)
        ->setWidth(327.68503937396997)
        ->setOffsetX(233.19685039647)
        ->setOffsetY(640.25196851154);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $textRun = $shape->createTextRun('29 ??????');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(18)
        ->setColor(new Color('FF464C53'));


    $photo = Storage::disk('public')->path('pptx/70f86bf6abaa314e43fb142f6c0b5957.jpg');

    $ims = new \App\Utils\Img();

    [$w, $s, $source_type] = getimagesize($photo);
    $ims->create($w, $s, true);

    $img2 = new \App\Utils\Img($photo);
    $img2->circleCrop();
    $ims->merge($img2, 0, 0);

    $shape = $currentSlide->createDrawingShape();
    $shape->setName('Photo')
        ->setPath($ims->render())
        ->setWidthAndHeight(360.18897638222995, 360.18897638222995)
        ->setOffsetX(216.56692913643002)
        ->setOffsetY(182.17322834862);

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(216.56692913643002)
        ->setOffsetY(829.98425197836);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('????????????:');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(216.56692913643002)
        ->setOffsetY(867.02362205754);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('??????????????????:');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(216.56692913643002)
        ->setOffsetY(902.5511811130799);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('?????????? ????????????????????:');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(216.56692913643002)
        ->setOffsetY(936.94488190089);

    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('?????????? ????????????????:');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(216.56692913643002)
        ->setOffsetY(973.2283464682499);

    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('???????????????????????????? ???? ????????????:');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    $myInformation = [
        'country' => '????????????',
        'ethnicity' => '??????????????????',
        'life_in' => '????????????',
        'birth_city' => '??????????????',
        'moving' => '?? ???????????? ????????????',
    ];

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(245.29133858558998)
        ->setOffsetX(476.59842520251)
        ->setOffsetY(829.98425197836);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun($myInformation['country']);
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(245.29133858558998)
        ->setOffsetX(476.59842520251)
        ->setOffsetY(867.02362205754);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun($myInformation['ethnicity']);
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setColor(new Color('FF464C53'));
    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(245.29133858558998)
        ->setOffsetX(476.59842520251)
        ->setOffsetY(902.5511811130799);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun($myInformation['life_in']);
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(245.29133858558998)
        ->setOffsetX(476.59842520251)
        ->setOffsetY(936.94488190089);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun($myInformation['birth_city']);
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(245.29133858558998)
        ->setOffsetX(476.59842520251)
        ->setOffsetY(973.2283464682499);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun($myInformation['moving']);
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setColor(new Color('FF464C53'));


    $objPHPPowerPoint->createSlide();
    $currentSlide = $objPHPPowerPoint->getSlide(2);


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
        ->setOffsetY(1086.61417324);


    $photos = [
        'pptx/70f86bf6abaa314e43fb142f6c0b5957.jpg',
        'pptx/70f86bf6abaa314e43fb142f6c0b5957.jpg',
        'pptx/70f86bf6abaa314e43fb142f6c0b5957.jpg',
        'pptx/70f86bf6abaa314e43fb142f6c0b5957.jpg',
        'pptx/70f86bf6abaa314e43fb142f6c0b5957.jpg',
    ];
    function square($path)
    {
        $im = imagecreatefrompng($path);
        $size = min(imagesx($im), imagesy($im));
        $im2 = imagecrop($im, ['x' => 0, 'y' => 0, 'width' => $size, 'height' => $size]);
        if ($im2 !== FALSE) {
            imagepng($im2, storage_path('app/public/pptx/temp3.png'));
            sleep(3);
            return \Storage::disk('public')->path('pptx/temp3.png');
        }
    }

    function rectangle($path)
    {
        $im = imagecreatefrompng($path);
        $im2 = imagecrop($im, ['x' => 0, 'y' => 0, 'width' => 663.30708662205, 'height' => 319.37007874395]);
        if ($im2 !== FALSE) {
            imagepng($im2, storage_path('app/public/pptx/temp2.png'));
            sleep(2);
            return \Storage::disk('public')->path('pptx/temp2.png');
        }
    }

    foreach ($photos as $key => $item) {
        $photo = Storage::disk('public')->path($item);
        imagepng(imagecreatefromstring(file_get_contents($photo)), storage_path('app/public/pptx/temp1.png'));
        sleep(3);
        $photo = Storage::disk('public')->path('pptx/temp1.png');


        if ($key == 0) {
            $shape = $currentSlide->createDrawingShape();
            $shape->setName('Photo_' . $key)
                ->setPath(rectangle($photo))
                ->setWidthAndHeight(663.30708662205, 353.38582677585)
                ->setOffsetX(78.99212598519)
                ->setOffsetY(57.44881889832);
        }
        if ($key == 1) {
            $shape = $currentSlide->createDrawingShape();

            $shape->setName('Photo_' . $key)
                ->setPath(square($photo))
                ->setWidthAndHeight(318.61417323213, 289.51181102706)
                ->setOffsetX(78.99212598519)
                ->setOffsetY(399.49606299687);
        }

        if ($key == 2) {
            $shape = $currentSlide->createDrawingShape();
            $shape->setName('Photo_' . $key)
                ->setPath($photo)
                ->setWidthAndHeight(318.61417323213, 289.51181102706)
                ->setOffsetX(452.40944882427)
                ->setOffsetY(399.49606299687);
        }

        if ($key == 3) {
            $shape = $currentSlide->createDrawingShape();
            $shape->setName('Photo_' . $key)
                ->setPath($photo)
                ->setWidthAndHeight(318.61417323213, 289.51181102706)
                ->setOffsetX(78.99212598519)
                ->setOffsetY(713.95275591399);
        }

        if ($key == 4) {
            $shape = $currentSlide->createDrawingShape();
            $shape->setName('Photo_' . $key)
                ->setPath($photo)
                ->setWidthAndHeight(318.61417323213, 289.51181102706)
                ->setOffsetX(452.40944882427)
                ->setOffsetY(713.95275591399);
        }
    }

    /**
     * ===========================================
     */
    $objPHPPowerPoint->createSlide();
    $currentSlide = $objPHPPowerPoint->getSlide(3);


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
        ->setOffsetY(1086.61417324);

    $shape = $currentSlide->createDrawingShape();
    $shape->setName('ShapeCircle')
        ->setPath($shapeCircle)
        ->setWidthAndHeight(14.36220472458, 14.36220472458)
        ->setOffsetX(152.69291338764)
        ->setOffsetY(78.61417322928);

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(501.16535433666)
        ->setOffsetX(171.59055118314)
        ->setOffsetY(69.54330708744);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('?????????? ????????????');
    $textRun->getFont()->setName('Yu Gothic')
        ->setBold(true)
        ->setSize(14)
        ->setColor(new Color('FF464C53'));
    /**
     *
     */

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(173.10236220678)
        ->setOffsetY(102.42519685161);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('???????? ??????????????: ');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(173.10236220678)
        ->setOffsetY(135.30708661577998);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('????????: ');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(173.10236220678)
        ->setOffsetY(165.54330708858);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('??????: ');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(173.10236220678)
        ->setOffsetY(198.04724409684002);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('????????????????????????: ');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(173.10236220678)
        ->setOffsetY(227.90551181373002);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('???????? ??????????:');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(173.10236220678)
        ->setOffsetY(260.40944882199);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('???????? ????????:');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(173.10236220678)
        ->setOffsetY(363.21259842950997);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('????????????:');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(173.10236220678)
        ->setOffsetY(394.58267717004);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('????????:');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));


    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(173.10236220678)
        ->setOffsetY(425.57480315465995);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('???????????? ???? ??????????:');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(173.10236220678)
        ->setOffsetY(490.96062992709);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('??????????????:');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(173.10236220678)
        ->setOffsetY(521.57480314961);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('????????????????:');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(173.10236220678)
        ->setOffsetY(552.5669291404199);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('??????????????????????????????:');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(173.10236220678)
        ->setOffsetY(583.93700788095);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('???????????????? ??????????:');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    /**
     *
     */

    $shape = $currentSlide->createDrawingShape();
    $shape->setName('ShapeCircle')
        ->setPath($shapeCircle)
        ->setWidthAndHeight(14.36220472458, 14.36220472458)
        ->setOffsetX(160.62992126175)
        ->setOffsetY(666.7086614252399);

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(501.16535433666)
        ->setOffsetX(173.10236220678)
        ->setOffsetY(659.90551181886);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('?????????????????????? ?? ????????????????');
    $textRun->getFont()->setName('Yu Gothic')
        ->setBold(true)
        ->setSize(14)
        ->setColor(new Color('FF464C53'));


    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(173.10236220678)
        ->setOffsetY(721.13385827628);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('??????????????????????:');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(173.10236220678)
        ->setOffsetY(782.7401574896099);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('????????????:');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(173.10236220678)
        ->setOffsetY(842.4566929233899);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('????????????????:');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(173.10236220678)
        ->setOffsetY(878.36220473484);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('???????????????? ???? ??????????????????:');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(260.03149606608)
        ->setOffsetX(173.10236220678)
        ->setOffsetY(916.5354330817499);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('????????????????:');
    $textRun->getFont()->setName('Yu Gothic UI Light')
        ->setSize(14)
        ->setBold(true)
        ->setColor(new Color('FF464C53'));

    /**
     * ????????????
     */
    $info = [
        'zodiac' => '????????',
        'height' => 1.78,
        'weight' => 55,
        'body_type' => '????????????????????????',
        'hair_color' => '??????????????',
        'eye_color' => '??????????????',
        'status' => '????????',
        'children' => '??????',
        'want_children' => '????',
        'smoking' => '????????',
        'alcohol' => '???? ??????',
        'faith' => '????????????????????????',
        'langs' => '??????????????, ????????????????????',
        'education' => '??????????-????',
        'work' => '??????????-????',
        'salary' => '???? 1000$ ???? 5000$',
        'problem_healthy' => '??????',
        'allergy' => '????????????'
    ];

    $offsetUp = 31.3700787405;
    $offsetStart = 101.66929133978999;
    $i = 1;
    foreach ($info as $item) {
        $shape = $currentSlide->createRichTextShape()
            ->setHeight(32.50393700826)
            ->setWidth(260.03149606608)
            ->setOffsetX(420.66141732783)
            ->setOffsetY($offsetStart);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $textRun = $shape->createTextRun($item);
        $textRun->getFont()->setName('Yu Gothic UI Light')
            ->setSize(14)
            ->setColor(new Color('FF464C53'));

        if ($i == 6) {
            $offsetStart = 361.70078740587;
            $i++;
            continue;
        }
        if ($i == 9) {
            $offsetStart = 490.20472441526994;
            $i++;
            continue;
        }

        if ($i == 13) {
            $offsetStart = 721.88976378;
            $i++;
            continue;
        }

        if ($i == 14) {
            $offsetStart = 782.74015748;
            $i++;
            continue;
        }
        if ($i == 15) {
            $offsetStart = 843.212598425;
            $i++;
            continue;
        }
        if ($i == 16) {
            $offsetStart = 879.874015748;
            $i++;
            continue;
        }
        if ($i == 17) {
            $offsetStart = 916.535433071;
            $i++;
            continue;
        }
        $offsetStart += $offsetUp;
        $i++;
    }

    /**
     * ===========================================
     */
    $objPHPPowerPoint->createSlide();
    $currentSlide = $objPHPPowerPoint->getSlide(4);


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
        ->setOffsetY(1086.61417324);

    $shape = $currentSlide->createDrawingShape();
    $shape->setName('ShapeCircle')
        ->setPath($shapeCircle)
        ->setWidthAndHeight(14.36220472458, 14.36220472458)
        ->setOffsetX(152.69291338764)
        ->setOffsetY(78.61417322928);

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(501.16535433666)
        ->setOffsetX(171.59055118314)
        ->setOffsetY(69.54330708744);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('????????????????');
    $textRun->getFont()->setName('Yu Gothic')
        ->setBold(true)
        ->setSize(14)
        ->setColor(new Color('FF464C53'));

    $shape = $currentSlide->createDrawingShape();
    $shape->setName('ShapeCircle')
        ->setPath($shapeCircle)
        ->setWidthAndHeight(14.36220472458, 14.36220472458)
        ->setOffsetX(152.69291338764)
        ->setOffsetY(673.511811024);

    $shape = $currentSlide->createRichTextShape()
        ->setHeight(32.50393700826)
        ->setWidth(501.16535433666)
        ->setOffsetX(171.59055118314)
        ->setOffsetY(664.440944882);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $textRun = $shape->createTextRun('???????????? ???? ??????????????');
    $textRun->getFont()->setName('Yu Gothic')
        ->setBold(true)
        ->setSize(14)
        ->setColor(new Color('FF464C53'));

    /**
     * ????????
     */

    $info2 = [
        'like_pets' => '???????????? ???? ???? ???????????????? ????????????????:',
        'have_pets' => '???????? ???? ???????????????? ???????????????? ?? ??????????:',
        'book_or_films' => '?????????? ?????? ????????????:',
        'relax' => '??????????:',
        'was_be_country' => '????????????, ?? ?????????????? ????????:',
        'dream_country' => '????????????, ?? ?????????????? ?????????????? ????????????????:',
        'best_gift' => '???????????? ?????????????? ?????? ??????:',
        'hobbies' => '??????????:',
        'life_kredo' => '?????????????????? ??????????:',
        'replace_for_human' => '?????????? ?????????? ???????? ?????????????????????? ?? ???????????',
        'different_age' => '?????? ???? ???????????????????? ?? ???????????????????????? ?????????????? ?? ???????????????? ?????????? ?????????????????????',
        'talents' => '?????? ???? ????????????????, ?????????? ?? ???????? ???????????????',
    ];

    $info2Cords = [
        102.42519685161,
        166.299212598,
        263.05511811336,
        309.921259843,
        363.59055118542,
        404.78740157961,
        470.55118110794996,
        518.92913386443,
        583.93700788095,
        721.13385827628,
        782.7401574896099,
        898.01574804216
    ];

    $i = 0;
    foreach ($info2 as $item) {
        $shape = $currentSlide->createRichTextShape()
            ->setHeight(32.50393700826)
            ->setWidth(260.03149606608)
            ->setOffsetX(173.10236220678)
            ->setOffsetY($info2Cords[$i]);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $textRun = $shape->createTextRun($item);
        $textRun->getFont()->setName('Yu Gothic UI Light')
            ->setBold(true)
            ->setSize(14)
            ->setColor(new Color('FF464C53'));

        $i++;
    }

    /**
     * ????????????
     */
    $info2 = [
        'like_pets' => '????',
        'have_pets' => '????????????',
        'book_or_films' => '?????????? ?? ????????????',
        'relax' => '??????????????????????????',
        'was_be_country' => '????????????',
        'dream_country' => '??????????, ????????????',
        'best_gift' => '????????????????',
        'hobbies' => '????????????????????????????????',
        'life_kredo' => '??????????',
        'replace_for_human' => '??????????????',
        'different_age' => '?????????? ??????????',
        'talents' => '???????????? ????????',
    ];

    $info2Cords = [
        102.42519685161,
        166.299212598,
        263.05511811336,
        309.921259843,
        363.59055118542,
        404.78740157961,
        470.55118110794996,
        518.92913386443,
        583.93700788095,
        721.13385827628,
        782.7401574896099,
        898.01574804216
    ];

    $i = 0;
    foreach ($info2 as $item) {
        $shape = $currentSlide->createRichTextShape()
            ->setHeight(32.50393700826)
            ->setWidth(260.03149606608)
            ->setOffsetX(420.66141732783)
            ->setOffsetY($info2Cords[$i]);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $textRun = $shape->createTextRun($item);
        $textRun->getFont()->setName('Yu Gothic UI Light')
            ->setSize(14)
            ->setColor(new Color('FF464C53'));

        $i++;
    }

    echo '?????????? ????????????';

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

Route::get('conv', function () {
    ?>

    <input type="text"> oninput: <span id="result"></span>
    <script>
        var input = document.body.children[0];

        input.oninput = function () {
            document.getElementById('result').innerHTML = (parseFloat(input.value.replace(',', '.').replace(/[^-0-9]/gim, '')) * 37.795275591) / 100;
        };
    </script>
    <?php
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
