<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Applications
 *
 * @property int $id
 * @property string $client_name
 * @property string $service_type
 * @property string|null $responsibility
 * @property int $status
 * @property int|null $questionnaire_id
 * @property string|null $link
 * @property bool|null $link_active
 * @property string|null $email
 * @property string|null $phone
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $from
 * @method static \Illuminate\Database\Eloquent\Builder|Applications newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Applications newQuery()
 * @method static \Illuminate\Database\Query\Builder|Applications onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Applications query()
 * @method static \Illuminate\Database\Eloquent\Builder|Applications whereClientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Applications whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Applications whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Applications whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Applications whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Applications whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Applications whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Applications whereLinkActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Applications wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Applications whereQuestionnaireId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Applications whereResponsibility($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Applications whereServiceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Applications whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Applications whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Applications withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Applications withoutTrashed()
 */
	class Applications extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Cities
 *
 * @property int $city_id
 * @property int $country_id
 * @property bool $important
 * @property int|null $region_id
 * @property string|null $title_ru
 * @property string|null $area_ru
 * @property string|null $region_ru
 * @property string|null $title_ua
 * @property string|null $area_ua
 * @property string|null $region_ua
 * @property string|null $title_be
 * @property string|null $area_be
 * @property string|null $region_be
 * @property string|null $title_en
 * @property string|null $area_en
 * @property string|null $region_en
 * @property string|null $title_es
 * @property string|null $area_es
 * @property string|null $region_es
 * @property string|null $title_pt
 * @property string|null $area_pt
 * @property string|null $region_pt
 * @property string|null $title_de
 * @property string|null $area_de
 * @property string|null $region_de
 * @property string|null $title_fr
 * @property string|null $area_fr
 * @property string|null $region_fr
 * @property string|null $title_it
 * @property string|null $area_it
 * @property string|null $region_it
 * @property string|null $title_pl
 * @property string|null $area_pl
 * @property string|null $region_pl
 * @property string|null $title_ja
 * @property string|null $area_ja
 * @property string|null $region_ja
 * @property string|null $title_lt
 * @property string|null $area_lt
 * @property string|null $region_lt
 * @property string|null $title_lv
 * @property string|null $area_lv
 * @property string|null $region_lv
 * @property string|null $title_cz
 * @property string|null $area_cz
 * @property string|null $region_cz
 * @method static \Illuminate\Database\Eloquent\Builder|Cities newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cities newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cities query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereAreaBe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereAreaCz($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereAreaDe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereAreaEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereAreaEs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereAreaFr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereAreaIt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereAreaJa($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereAreaLt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereAreaLv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereAreaPl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereAreaPt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereAreaRu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereAreaUa($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereImportant($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereRegionBe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereRegionCz($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereRegionDe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereRegionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereRegionEs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereRegionFr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereRegionIt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereRegionJa($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereRegionLt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereRegionLv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereRegionPl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereRegionPt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereRegionRu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereRegionUa($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereTitleBe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereTitleCz($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereTitleDe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereTitleEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereTitleEs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereTitleFr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereTitleIt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereTitleJa($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereTitleLt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereTitleLv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereTitlePl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereTitlePt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereTitleRu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cities whereTitleUa($value)
 */
	class Cities extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Countries
 *
 * @property int $country_id
 * @property string|null $title_ru
 * @property string|null $title_ua
 * @property string|null $title_be
 * @property string|null $title_en
 * @property string|null $title_es
 * @property string|null $title_pt
 * @property string|null $title_de
 * @property string|null $title_fr
 * @property string|null $title_it
 * @property string|null $title_pl
 * @property string|null $title_ja
 * @property string|null $title_lt
 * @property string|null $title_lv
 * @property string|null $title_cz
 * @method static \Illuminate\Database\Eloquent\Builder|Countries newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Countries newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Countries query()
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereTitleBe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereTitleCz($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereTitleDe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereTitleEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereTitleEs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereTitleFr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereTitleIt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereTitleJa($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereTitleLt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereTitleLv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereTitlePl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereTitlePt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereTitleRu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereTitleUa($value)
 */
	class Countries extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Interpretation
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Interpretation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Interpretation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Interpretation query()
 */
	class Interpretation extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Langs
 *
 * @property int $id
 * @property string $code
 * @property string $nameRU
 * @property string $nameEN
 * @property string $nativeName
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Langs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Langs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Langs query()
 * @method static \Illuminate\Database\Eloquent\Builder|Langs whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Langs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Langs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Langs whereNameEN($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Langs whereNameRU($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Langs whereNativeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Langs whereUpdatedAt($value)
 */
	class Langs extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Questionnaire
 *
 * @property int $id
 * @property string|null $sign
 * @property int|null $partner_appearance_id
 * @property int|null $personal_qualities_partner_id
 * @property int|null $partner_information_id
 * @property int|null $test_id
 * @property int|null $my_appearance_id
 * @property int|null $my_personal_qualities_id
 * @property int|null $my_information_id
 * @property int|null $manager_id
 * @property string $status_pay
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire newQuery()
 * @method static \Illuminate\Database\Query\Builder|Questionnaire onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire query()
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire whereMyAppearanceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire whereMyInformationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire whereMyPersonalQualitiesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire wherePartnerAppearanceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire wherePartnerInformationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire wherePersonalQualitiesPartnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire whereSign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire whereStatusPay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire whereTestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Questionnaire whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Questionnaire withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Questionnaire withoutTrashed()
 */
	class Questionnaire extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QuestionnaireAppointedDate
 *
 * @property int $id
 * @property int $questionnaire_id
 * @property int $with_questionnaire_id
 * @property string $date
 * @property string $time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireAppointedDate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireAppointedDate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireAppointedDate query()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireAppointedDate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireAppointedDate whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireAppointedDate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireAppointedDate whereQuestionnaireId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireAppointedDate whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireAppointedDate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireAppointedDate whereWithQuestionnaireId($value)
 */
	class QuestionnaireAppointedDate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QuestionnaireFiles
 *
 * @property int $id
 * @property int $questionnaire_id
 * @property string $type
 * @property string $key
 * @property string $path
 * @property string $name
 * @property string $size
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireFiles newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireFiles newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireFiles query()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireFiles whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireFiles whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireFiles whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireFiles whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireFiles wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireFiles whereQuestionnaireId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireFiles whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireFiles whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireFiles whereUpdatedAt($value)
 */
	class QuestionnaireFiles extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QuestionnaireMyAppearance
 *
 * @property int $id
 * @property string $sex
 * @property string $ethnicity
 * @property string $body_type
 * @property string|null $chest
 * @property string|null $booty
 * @property string $hair_color
 * @property string|null $hair_length
 * @property string|null $eye_color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyAppearance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyAppearance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyAppearance query()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyAppearance whereBodyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyAppearance whereBooty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyAppearance whereChest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyAppearance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyAppearance whereEthnicity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyAppearance whereEyeColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyAppearance whereHairColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyAppearance whereHairLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyAppearance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyAppearance whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyAppearance whereUpdatedAt($value)
 */
	class QuestionnaireMyAppearance extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QuestionnaireMyInformation
 *
 * @property int $id
 * @property string $age
 * @property string $place_birth
 * @property string $city
 * @property string $zodiac_signs
 * @property string $height
 * @property string $weight
 * @property string $marital_status
 * @property string $languages
 * @property bool $moving_country
 * @property bool $moving_city
 * @property bool $children
 * @property string|null $children_count
 * @property string $children_desire
 * @property string $smoking
 * @property string $alcohol
 * @property string $religion
 * @property string $sport
 * @property string $education
 * @property string $work
 * @property string $salary
 * @property string $health_problems
 * @property string $allergies
 * @property string $pets
 * @property string $have_pets
 * @property string $films_or_books
 * @property string $relax
 * @property string $countries_was
 * @property string $countries_dream
 * @property string $best_gift
 * @property string $hobbies
 * @property string $kredo
 * @property string $features_repel
 * @property string $age_difference
 * @property string $films
 * @property string $songs
 * @property string $ideal_weekend
 * @property string $sleep
 * @property string $doing_10
 * @property string $signature_dish
 * @property string $clubs
 * @property string $best_gift_received
 * @property string $talents
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $name
 * @property string|null $birthday
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation query()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereAgeDifference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereAlcohol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereAllergies($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereBestGift($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereBestGiftReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereChildren($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereChildrenCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereChildrenDesire($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereClubs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereCountriesDream($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereCountriesWas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereDoing10($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereEducation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereFeaturesRepel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereFilms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereFilmsOrBooks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereHavePets($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereHealthProblems($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereHobbies($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereIdealWeekend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereKredo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereLanguages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereMaritalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereMovingCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereMovingCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation wherePets($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation wherePlaceBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereRelax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereReligion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereSignatureDish($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereSleep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereSmoking($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereSongs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereSport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereTalents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereWork($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyInformation whereZodiacSigns($value)
 */
	class QuestionnaireMyInformation extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QuestionnaireMyPersonalQualities
 *
 * @property int $id
 * @property bool $calm
 * @property bool $energetic
 * @property bool $happy
 * @property bool $modest
 * @property bool $purposeful
 * @property bool $weak-willed
 * @property bool $self
 * @property bool $dependent
 * @property bool $feminine
 * @property bool $courageous
 * @property bool $confident
 * @property bool $delicate
 * @property bool $live_here_now
 * @property bool $pragmatic
 * @property bool $graceful
 * @property bool $sociable
 * @property bool $smiling
 * @property bool $housewifely
 * @property bool $ambitious
 * @property bool $artistic
 * @property bool $good
 * @property bool $aristocratic
 * @property bool $stylish
 * @property bool $economical
 * @property bool $business
 * @property bool $sports
 * @property bool $fearless
 * @property bool $shy
 * @property bool $playful
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities query()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereAmbitious($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereAristocratic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereArtistic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereBusiness($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereCalm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereConfident($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereCourageous($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereDelicate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereDependent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereEconomical($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereEnergetic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereFearless($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereFeminine($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereGood($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereGraceful($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereHappy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereHousewifely($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereLiveHereNow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereModest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities wherePlayful($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities wherePragmatic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities wherePurposeful($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereSelf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereShy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereSmiling($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereSociable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereSports($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereStylish($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireMyPersonalQualities whereWeakWilled($value)
 */
	class QuestionnaireMyPersonalQualities extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QuestionnairePartnerAppearance
 *
 * @property int $id
 * @property string $sex
 * @property string $ethnicity
 * @property string $body_type
 * @property string|null $chest
 * @property string|null $booty
 * @property string $hair_color
 * @property string|null $hair_length
 * @property string|null $eye_color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerAppearance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerAppearance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerAppearance query()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerAppearance whereBodyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerAppearance whereBooty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerAppearance whereChest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerAppearance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerAppearance whereEthnicity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerAppearance whereEyeColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerAppearance whereHairColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerAppearance whereHairLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerAppearance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerAppearance whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerAppearance whereUpdatedAt($value)
 */
	class QuestionnairePartnerAppearance extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QuestionnairePartnerInformation
 *
 * @property int $id
 * @property string $age
 * @property string $place_birth
 * @property string $city
 * @property string $zodiac_signs
 * @property string $height
 * @property string $weight
 * @property string $marital_status
 * @property string $languages
 * @property bool $moving_country
 * @property bool $moving_city
 * @property bool $children
 * @property string|null $children_count
 * @property string $children_desire
 * @property string $smoking
 * @property string $alcohol
 * @property string $religion
 * @property string $sport
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation query()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation whereAlcohol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation whereChildren($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation whereChildrenCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation whereChildrenDesire($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation whereLanguages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation whereMaritalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation whereMovingCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation whereMovingCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation wherePlaceBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation whereReligion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation whereSmoking($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation whereSport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation whereWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePartnerInformation whereZodiacSigns($value)
 */
	class QuestionnairePartnerInformation extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QuestionnairePersonalQualitiesPartner
 *
 * @property int $id
 * @property bool $calm
 * @property bool $energetic
 * @property bool $happy
 * @property bool $modest
 * @property bool $purposeful
 * @property bool $weak-willed
 * @property bool $self
 * @property bool $dependent
 * @property bool $feminine
 * @property bool $courageous
 * @property bool $confident
 * @property bool $delicate
 * @property bool $live_here_now
 * @property bool $pragmatic
 * @property bool $graceful
 * @property bool $sociable
 * @property bool $smiling
 * @property bool $housewifely
 * @property bool $ambitious
 * @property bool $artistic
 * @property bool $good
 * @property bool $aristocratic
 * @property bool $stylish
 * @property bool $economical
 * @property bool $business
 * @property bool $sports
 * @property bool $fearless
 * @property bool $shy
 * @property bool $playful
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner query()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereAmbitious($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereAristocratic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereArtistic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereBusiness($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereCalm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereConfident($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereCourageous($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereDelicate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereDependent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereEconomical($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereEnergetic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereFearless($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereFeminine($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereGood($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereGraceful($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereHappy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereHousewifely($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereLiveHereNow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereModest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner wherePlayful($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner wherePragmatic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner wherePurposeful($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereSelf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereShy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereSmiling($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereSociable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereSports($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereStylish($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnairePersonalQualitiesPartner whereWeakWilled($value)
 */
	class QuestionnairePersonalQualitiesPartner extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QuestionnaireTest
 *
 * @property int $id
 * @property int $lies
 * @property int $intervention
 * @property int $value
 * @property int $life
 * @property int $motive_marriage
 * @property int $family_atmosphere
 * @property int $position_sex
 * @property int $books
 * @property int $friends
 * @property int $leisure
 * @property int $discussion_feelings
 * @property int $work_relationship
 * @property int $family_decisions
 * @property int $consent
 * @property int $interests_partner
 * @property int $first_place_relationship
 * @property int $position_society
 * @property int $conflicts
 * @property int $cleanliness
 * @property int $clear_plan
 * @property int $conflict_behavior
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest query()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereBooks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereCleanliness($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereClearPlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereConflictBehavior($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereConflicts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereConsent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereDiscussionFeelings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereFamilyAtmosphere($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereFamilyDecisions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereFirstPlaceRelationship($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereFriends($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereInterestsPartner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereIntervention($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereLeisure($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereLies($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereLife($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereMotiveMarriage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest wherePositionSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest wherePositionSociety($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireTest whereWorkRelationship($value)
 */
	class QuestionnaireTest extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QuestionnaireUploadPhoto
 *
 * @property int $id
 * @property int $questionnaire_id
 * @property string $path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireUploadPhoto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireUploadPhoto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireUploadPhoto query()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireUploadPhoto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireUploadPhoto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireUploadPhoto wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireUploadPhoto whereQuestionnaireId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionnaireUploadPhoto whereUpdatedAt($value)
 */
	class QuestionnaireUploadPhoto extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\QuestionsTest
 *
 * @property int $id
 * @property string $field
 * @property string $question_ru
 * @property string $question_en
 * @property string $answers_ru
 * @property string $answers_en
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsTest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsTest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsTest query()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsTest whereAnswersEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsTest whereAnswersRu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsTest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsTest whereField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsTest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsTest whereQuestionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsTest whereQuestionRu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionsTest whereUpdatedAt($value)
 */
	class QuestionsTest extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SignQuestionnaire
 *
 * @property int $id
 * @property int $questionnaire_id
 * @property int $application_id
 * @property string $sign
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SignQuestionnaire newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SignQuestionnaire newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SignQuestionnaire query()
 * @method static \Illuminate\Database\Eloquent\Builder|SignQuestionnaire whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SignQuestionnaire whereApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SignQuestionnaire whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SignQuestionnaire whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SignQuestionnaire whereQuestionnaireId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SignQuestionnaire whereSign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SignQuestionnaire whereUpdatedAt($value)
 */
	class SignQuestionnaire extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $avatar
 * @property string $phone
 * @property int $role
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Query\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

