<?php

namespace App\Services;

use App\Models\Questionnaire;
use App\Models\QuestionnaireUploadPhoto;
use App\Utils\TranslateFields;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;

class PptxCreatorCopy
{
    use TranslateFields;

    protected string $backgroundFirst = '';
    protected string $backgroundWhite = '';
    protected string $fon = '';
    protected string $logo = '';


    protected PhpPresentation $objPHPPowerPoint;
    protected Slide $currentSlide;

    public function __construct()
    {
        $this->objPHPPowerPoint = new PhpPresentation();
        $oDocumentLayout = new DocumentLayout();
        $oDocumentLayout->setDocumentLayout(DocumentLayout::LAYOUT_A4, false);

        $this->objPHPPowerPoint = $this->objPHPPowerPoint->setLayout($oDocumentLayout);
        $this->currentSlide = $this->objPHPPowerPoint->getActiveSlide();
    }

    public function create(Questionnaire $questionnaire, int $questionnaireId)
    {
        $questionnaire = $questionnaire->my()->where('questionnaires.id', $questionnaireId)->first()?->toArray();

        if ($questionnaire == null)
            return false;

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
        $textRun->getFont()->setName('Arial')
            ->setSize(24)
            ->setColor(new Color('FF464C53'));


        $shape = $currentSlide->createRichTextShape()
            ->setHeight(74.73806685)
            ->setWidth(522.02832480002)
            ->setOffsetX(137.3359401)
            ->setOffsetY(265.94611605001);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $textRun = $shape->createTextRun('?????????????????? ?????? ?????????????? ????!');
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
            ->setSize(42)
            ->setColor(new Color('FFD2B690'));


        $shape = $currentSlide->createRichTextShape()
            ->setHeight(108.85039370208)
            ->setWidth(411.59055118598997)
            ->setOffsetX(191.62204724637)
            ->setOffsetY(571.4645669359201);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $textRun = $shape->createTextRun($questionnaire['name']);
        $textRun->getFont()->setName('Arial')
            ->setSize(26)
            ->setColor(new Color('FF464C53'));

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(38.929133858730005)
            ->setWidth(327.68503937396997)
            ->setOffsetX(233.19685039647)
            ->setOffsetY(640.25196851154);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $textRun = $shape->createTextRun($this->years($questionnaire['age']));
        $textRun->getFont()->setName('Arial')
            ->setSize(18)
            ->setColor(new Color('FF464C53'));

        $photoDB = QuestionnaireUploadPhoto::where('questionnaire_id', $questionnaire['questionnaire_id'])->first()?->toArray();

        if ($photoDB == null) {
            $photoPath = 'pptx/photoNull.png';
            $photo = Storage::disk('public')->path($photoPath);
        } else {
            $photo = str_replace('storage/app/public', '', $photoDB['path']);
        }

        $ims = new \App\Utils\Img();

        list($w, $s, $source_type) = getimagesize($photo);
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
            ->setSize(14)
            ->setBold(true)
            ->setColor(new Color('FF464C53'));

        $moving = [];

        if ($questionnaire['moving_country']) {
            $moving[] = '?? ???????????? ????????????';
        }

        if ($questionnaire['moving_city']) {
            $moving[] = '?? ???????????? ??????????';
        }

        $myInformation = [
            'country' => explode(', ', $questionnaire['city'])[0],
            'ethnicity' => $this->ethnicity($questionnaire['ethnicity']),
            'life_in' => explode(', ', $questionnaire['city'])[1],
            'birth_city' => $questionnaire['place_birth'],
            'moving' => empty($moving) ? '?????? ??????????' : implode(', ', $moving),
        ];

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(32.50393700826)
            ->setWidth(245.29133858558998)
            ->setOffsetX(476.59842520251)
            ->setOffsetY(829.98425197836);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $textRun = $shape->createTextRun($myInformation['country']);
        $textRun->getFont()->setName('Arial')
            ->setSize(14)
            ->setColor(new Color('FF464C53'));

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(32.50393700826)
            ->setWidth(245.29133858558998)
            ->setOffsetX(476.59842520251)
            ->setOffsetY(867.02362205754);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $textRun = $shape->createTextRun($myInformation['ethnicity']);
        $textRun->getFont()->setName('Arial')
            ->setSize(14)
            ->setColor(new Color('FF464C53'));
        $shape = $currentSlide->createRichTextShape()
            ->setHeight(32.50393700826)
            ->setWidth(245.29133858558998)
            ->setOffsetX(476.59842520251)
            ->setOffsetY(902.5511811130799);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $textRun = $shape->createTextRun($myInformation['life_in']);
        $textRun->getFont()->setName('Arial')
            ->setSize(14)
            ->setColor(new Color('FF464C53'));

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(32.50393700826)
            ->setWidth(245.29133858558998)
            ->setOffsetX(476.59842520251)
            ->setOffsetY(936.94488190089);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $textRun = $shape->createTextRun($myInformation['birth_city']);
        $textRun->getFont()->setName('Arial')
            ->setSize(14)
            ->setColor(new Color('FF464C53'));

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(32.50393700826)
            ->setWidth(245.29133858558998)
            ->setOffsetX(476.59842520251)
            ->setOffsetY(973.2283464682499);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $textRun = $shape->createTextRun($myInformation['moving']);
        $textRun->getFont()->setName('Arial')
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

        $photoDB = QuestionnaireUploadPhoto::where('questionnaire_id', $questionnaire['questionnaire_id'])->get()?->toArray();

        if ($photoDB !== null) {
            $photos = [];
            foreach ($photoDB as $item) {
                $photos[] = $item['path'];
            }
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
                imagepng(imagecreatefromstring(file_get_contents($item)), storage_path('app/public/pptx/temp1.png'));
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
                        ->setPath(square($photo))
                        ->setWidthAndHeight(318.61417323213, 289.51181102706)
                        ->setOffsetX(452.40944882427)
                        ->setOffsetY(399.49606299687);
                }

                if ($key == 3) {
                    $shape = $currentSlide->createDrawingShape();
                    $shape->setName('Photo_' . $key)
                        ->setPath(square($photo))
                        ->setWidthAndHeight(318.61417323213, 289.51181102706)
                        ->setOffsetX(78.99212598519)
                        ->setOffsetY(713.95275591399);
                }

                if ($key == 4) {
                    $shape = $currentSlide->createDrawingShape();
                    $shape->setName('Photo_' . $key)
                        ->setPath(square($photo))
                        ->setWidthAndHeight(318.61417323213, 289.51181102706)
                        ->setOffsetX(452.40944882427)
                        ->setOffsetY(713.95275591399);
                }
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
            ->setSize(14)
            ->setBold(true)
            ->setColor(new Color('FF464C53'));

        $zodiac = $this->zodiacSigns();
        /**
         * ????????????
         */
        $info = [
            'zodiac' => $zodiac[$questionnaire['zodiac_signs']],
            'height' => $questionnaire['height'],
            'weight' => $questionnaire['weight'],
            'body_type' => $this->bodyType($questionnaire['body_type']),
            'hair_color' => $this->hairColor($questionnaire['hair_color']),
            'eye_color' => $this->colorEye($questionnaire['eye_color']),
            'status' => $this->maritalStatus($questionnaire['marital_status'], $questionnaire['sex']),
            'children' => $questionnaire['children_desire'] ? '????????' : '??????',
            'want_children' => $this->childrenDesire($questionnaire['children_desire']),
            'smoking' => $this->smoking($questionnaire['smoking']),
            'alcohol' => $this->alcohol($questionnaire['alcohol']),
            'faith' => $this->religion($questionnaire['religion']),
            'langs' => $questionnaire['languages'],
            'education' => $questionnaire['education_name'],
            'work' => $questionnaire['work_name'],
            'salary' => $this->salary($questionnaire['salary']),
            'problem_healthy' => $questionnaire['health_problems'],
            'allergy' => $questionnaire['allergies']
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
            $textRun->getFont()->setName('Arial')
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
        $textRun->getFont()->setName('Arial')
            ->setBold(true)
            ->setSize(14)
            ->setColor(new Color('FF464C53'));

        $shape = $currentSlide->createDrawingShape();
        $shape->setName('ShapeCircle')
            ->setPath($shapeCircle)
            ->setWidthAndHeight(14.36220472458, 14.36220472458)
            ->setOffsetX(152.69291338764)
            ->setOffsetY(673.511811024);


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
        ];

        $info2Cords = [
            102.42519685161,
            166.299212598,
            263.05511811336,
            309.921259843,
            363.59055118542,
            404.78740157961,
            470.55118110794996,
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
            $textRun->getFont()->setName('Arial')
                ->setBold(true)
                ->setSize(13)
                ->setColor(new Color('FF464C53'));
            $i++;
        }

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(32.50393700826)
            ->setWidth(260.03149606608)
            ->setOffsetX(173.10236220678)
            ->setOffsetY(494.7401574803);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $textRun = $shape->createTextRun('???????????? ?????????????? ?????? ??????:');
        $textRun->getFont()->setName('Arial')
            ->setBold(true)
            ->setSize(13)
            ->setColor(new Color('FF464C53'));

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(32.50393700826)
            ->setWidth(260.03149606608)
            ->setOffsetX(173.10236220678)
            ->setOffsetY(643.2755905512);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $textRun = $shape->createTextRun('??????????:');
        $textRun->getFont()->setName('Arial')
            ->setBold(true)
            ->setSize(13)
            ->setColor(new Color('FF464C53'));

        $textRun = $shape->createTextRun('?????????????????? ??????????:');
        $textRun->getFont()->setName('Arial')
            ->setBold(true)
            ->setSize(13)
            ->setColor(new Color('FF464C53'));

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(32.50393700826)
            ->setWidth(507.5905511811)
            ->setOffsetX(173.1023622047)
            ->setOffsetY(822.0472440945);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        /**
         * ????????????
         */
        $info2 = [
            'like_pets' => $this->pets($questionnaire['pets']),
            'have_pets' => $questionnaire['have_pets'],
            'book_or_films' => $this->fm($questionnaire['films_or_books']),
            'relax' => $this->relax($questionnaire['relax']),
            'was_be_country' => $questionnaire['countries_was'],
            'dream_country' => $questionnaire['countries_dream']
        ];

        $info2Cords = [
            102.42519685161,
            166.299212598,
            263.05511811336,
            309.921259843,
            363.59055118542,
            404.78740157961,
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
            $textRun->getFont()->setName('Arial')
                ->setSize(13)
                ->setColor(new Color('FF464C53'));

            $i++;
        }
        $shape = $currentSlide->createRichTextShape()
            ->setHeight(32.50393700826)
            ->setWidth(260.03149606608)
            ->setOffsetX(420.66141732783)
            ->setOffsetY(529.8897637795);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $textRun = $shape->createTextRun($questionnaire['best_gift']);
        $textRun->getFont()->setName('Arial')
            ->setSize(13)
            ->setColor(new Color('FF464C53'));


        $shape = $currentSlide->createRichTextShape()
            ->setHeight(32.50393700826)
            ->setWidth(260.03149606608)
            ->setOffsetX(420.66141732783)
            ->setOffsetY(670.1102362205);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $textRun = $shape->createTextRun($questionnaire['hobbies']);
        $textRun->getFont()->setName('Arial')
            ->setSize(13)
            ->setColor(new Color('FF464C53'));

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(32.50393700826)
            ->setWidth(260.03149606608)
            ->setOffsetX(420.66141732783)
            ->setOffsetY(851.1496062992);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $textRun = $shape->createTextRun($questionnaire['kredo']);
        $textRun->getFont()->setName('Arial')
            ->setSize(13)
            ->setColor(new Color('FF464C53'));



        // New Page
//
//        $shape = $currentSlide->createRichTextShape()
//            ->setHeight(32.50393700826)
//            ->setWidth(501.16535433666)
//            ->setOffsetX(171.59055118314)
//            ->setOffsetY(664.440944882);
//        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
//
//        $textRun = $shape->createTextRun('???????????? ???? ??????????????');
//        $textRun->getFont()->setName('Arial')
//            ->setBold(true)
//            ->setSize(14)
//            ->setColor(new Color('FF464C53'));
//
//        $textRun = $shape->createTextRun('?????????? ?????????? ???????? ?????????????????????? ?? ???????????');
//        $textRun->getFont()->setName('Arial')
//            ->setBold(true)
//            ->setSize(13)
//            ->setColor(new Color('FF464C53'));
//
//        $shape = $currentSlide->createRichTextShape()
//            ->setHeight(32.50393700826)
//            ->setWidth(507.5905511811)
//            ->setOffsetX(173.1023622047)
//            ->setOffsetY(823.1811023622);
//        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
//
//        $textRun = $shape->createTextRun('?????? ???? ???????????????????? ?? ???????????????????????? ?????????????? ?? ???????????????? ?????????? ?????????????????????');
//        $textRun->getFont()->setName('Arial')
//            ->setBold(true)
//            ->setSize(13)
//            ->setColor(new Color('FF464C53'));
//
//        $shape = $currentSlide->createRichTextShape()
//            ->setHeight(32.50393700826)
//            ->setWidth(507.5905511811)
//            ->setOffsetX(173.1023622047)
//            ->setOffsetY(945.2598425197);
//        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
//
//        $textRun = $shape->createTextRun('?????? ???? ????????????????, ?????????? ?? ???????? ???????????????');
//        $textRun->getFont()->setName('Arial')
//            ->setBold(true)
//            ->setSize(13)
//            ->setColor(new Color('FF464C53'));
//
//        $shape = $currentSlide->createRichTextShape()
//            ->setHeight(32.50393700826)
//            ->setWidth(533.6692913386)
//            ->setOffsetX(173.1023622047)
//            ->setOffsetY(727.1811023622);
//        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
//
//        $textRun = $shape->createTextRun($questionnaire['features_repel']);
//        $textRun->getFont()->setName('Arial')
//            ->setSize(13)
//            ->setColor(new Color('FF464C53'));
//
//        $shape = $currentSlide->createRichTextShape()
//            ->setHeight(32.50393700826)
//            ->setWidth(533.6692913386)
//            ->setOffsetX(173.1023622047)
//            ->setOffsetY(874.9606299213);
//        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
//
//        $textRun = $shape->createTextRun($questionnaire['age_difference']);
//        $textRun->getFont()->setName('Arial')
//            ->setSize(13)
//            ->setColor(new Color('FF464C53'));
//
//        $shape = $currentSlide->createRichTextShape()
//            ->setHeight(32.50393700826)
//            ->setWidth(533.6692913386)
//            ->setOffsetX(173.1023622047)
//            ->setOffsetY(972.094488189);
//        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
//
//        $textRun = $shape->createTextRun($questionnaire['talents']);
//        $textRun->getFont()->setName('Arial')
//            ->setSize(13)
//            ->setColor(new Color('FF464C53'));



        $oWriterPPTX = IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
        Storage::disk('public')->createDir('/questionnaire/pptx/' . $questionnaire['id']);
        $path = Storage::disk('public')->path('/questionnaire/pptx/' . $questionnaire['id'] . '/presentation.pptx');
        $oWriterPPTX->save($path);
        return $path;
//        $connection = ssh2_connect('45.141.79.57', 22);
//        ssh2_auth_password($connection, env('SSH_U'), env('SSH_P'));
//
//        $stream = ssh2_exec($connection, 'unoconv -f pdf /var/www/html/storage/app/public/questionnaire/pptx/'.$questionnaire['id'].'/presentation.pptx');
//        sleep(2);
//        return str_replace('.pptx', '.pdf', $path);
    }
}
