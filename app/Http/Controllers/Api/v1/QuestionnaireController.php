<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\Questionnaire\Create;
use App\Utils\QuestionnaireUtils;
use Illuminate\Http\Request;

class QuestionnaireController extends QuestionnaireUtils
{
    public function create(Create $request)
    {
        $this->partnerAppearance();
        $this->personalQualitiesPartner();
        $this->partnerInformation();
    }
}
