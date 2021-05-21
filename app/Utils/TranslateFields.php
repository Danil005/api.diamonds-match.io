<?php

namespace App\Utils;

use Illuminate\Support\Facades\Cache;

trait TranslateFields
{
    private function numWord($value, $words, $show = true): string
    {
        $num = $value % 100;
        if ($num > 19) {
            $num = $num % 10;
        }

        $out = ($show) ? $value . ' ' : '';
        switch ($num) {
            case 1:
                $out .= $words[0];
                break;
            case 2:
            case 3:
            case 4:
                $out .= $words[1];
                break;
            default:
                $out .= $words[2];
                break;
        }

        return $out;
    }

    private function fem(string $sex, string $value, array $other = []): string
    {
        if (empty($other)) {
            return $sex == 'female' ? $value . 'ая' : $value . 'ый';
        }

        return $sex == 'female' ? $value . $other[0] : $value . $other[1];
    }

    public function ethnicity(string $ethnicity): string
    {
        $lang = Cache::get('lang');

        $data = match ($lang) {
            "ru" => [
                'no_matter' => 'Не важно',
                'caucasoid' => 'Европеоид',
                'asian' => 'Азиат',
                'dark_skinned' => 'Темнокожей',
                'hispanic' => 'Латиноамериканец',
                'indian' => 'Индиец',
                'native_middle_east' => 'Выходец из стран Ближнего Востока',
                'mestizo' => 'Метис, родители принадлежат к разным расам',
                'native_american' => 'Представитель коренного населения Америки',
                'islands' => 'Представитель коренного населения островов | Тихого Океана / Австралии / Абориген',
                'other' => 'Другие'
            ],
            default => [
                'no_matter' => 'No Matter',
                'caucasoid' => 'Caucasoid',
                'asian' => 'Asian',
                'dark_skinned' => 'Dark Skinned',
                'hispanic' => 'Hispanic',
                'indian' => 'Indian',
                'native_middle_east' => 'Native to the Middle East',
                'mestizo' => 'Metis, parents are of different races',
                'native_american' => 'Native American Representative',
                'islands' => 'A representative of the indigenous population of the islands | Pacific / Australia / Aboriginal',
                'other' => 'Others'
            ],
        };

        return $data[$ethnicity];
    }

    public function bodyType(string $body): string
    {
        $lang = Cache::get('lang');

        $data = match ($lang) {
            "ru" => [
                'any' => 'Любой',
                'athletic' => 'Атлетический',
                'slim' => 'Стройный',
                'hourglass' => 'Песочные часы',
                'full' => 'Полный'
            ],
            default => [
                'any' => 'Any',
                'athletic' => 'Athletic',
                'slim' => 'Slim',
                'hourglass' => 'Hourglass',
                'full' => 'Full'
            ],
        };

        return $data[$body];
    }

    public function chestOrBooty(string $chestOrBooty): string
    {
        $lang = Cache::get('lang');

        $data = match ($lang) {
            "ru" => [
                'any' => 'Любая',
                'big' => 'Большая',
                'middle' => 'Средняя',
                'small' => 'Маленькая',
            ],
            default => [
                'any' => 'Any',
                'big' => 'Big',
                'middle' => 'Middle',
                'small' => 'Small',
            ],
        };

        return $data[$chestOrBooty];
    }

    public function hairColor(string $hairColor): string
    {
        $lang = Cache::get('lang');

        $data = match ($lang) {
            "ru" => [
                'any' => 'Любой',
                'brunette' => 'Брюнет',
                'blonde' => 'Блонд',
                'redhead' => 'Рыжий',
                'brown-haired' => 'Шатен',
            ],
            default => [
                'any' => 'Any',
                'brunette' => 'Brunette',
                'blonde' => 'Blonde',
                'redhead' => 'Redhead',
                'brown-haired' => 'Brown-Haired',
            ],
        };

        return $data[$hairColor];
    }

    public function hairLength(string $hairColor): string
    {
        $lang = Cache::get('lang');

        $data = match ($lang) {
            "ru" => [
                'any' => 'Любая',
                'long' => 'Длинные',
                'short' => 'Короткие',
            ],
            default => [
                'any' => 'Any',
                'long' => 'Long',
                'short' => 'Short',
            ],
        };

        return $data[$hairColor];
    }

    public function colorEye(string $colorEye): string
    {
        $lang = Cache::get('lang');

        $data = match ($lang) {
            "ru" => [
                'any' => 'Любые',
                'blue' => 'Голубые',
                'gray' => 'Серые',
                'green' => 'Зеленые',
                'brown' => 'Карие',
            ],
            default => [
                'any' => 'Any',
                'blue' => 'Blue',
                'gray' => 'Gray',
                'green' => 'Green',
                'brown' => 'Brown',
            ],
        };

        return $data[$colorEye];
    }




    public function zodiacSigns(): array
    {
        $lang = Cache::get('lang');

        if ($lang == 'ru') {
            return [
                'aries' => 'Овен',
                'calf' => 'Телец',
                'twins' => 'Близнецы',
                'cancer' => 'Рак',
                'lion' => 'Лев',
                'virgo' => 'Дева',
                'libra' => 'Весы',
                'scorpio' => 'Скорпион',
                'sagittarius' => 'Стрелец',
                'capricorn' => 'Козерог',
                'aquarius' => 'Водолей',
                'fish' => 'Рыба'
            ];
        }

        return [
            'aries' => 'Aries',
            'calf' => 'Calf',
            'twins' => 'Twins',
            'cancer' => 'Cancer',
            'lion' => 'Lion',
            'virgo' => 'Virgo',
            'libra' => 'Libra',
            'scorpio' => 'Scorpio',
            'sagittarius' => 'Sagittarius',
            'capricorn' => 'Capricorn',
            'aquarius' => 'Aquarius',
            'fish' => 'Fish'
        ];
    }

    public function years(array|string $years): string
    {
        $lang = Cache::get('lang');

        if (is_array($years)) {
            return $years[0] . ' — ' . $years[1] . ($lang == 'ru' ? ' лет' : ' years');
        } else {
            return $this->numWord($years, $lang == 'ru' ? ['год', 'года', 'лет'] : ['year', 'year', 'year']);
        }
    }

    public function personalQuality(string $quality, string $sex)
    {
        $lang = Cache::get('lang');

        if ($lang == 'ru') {
            $data = [
                'calm' => $this->fem($sex, 'Спокойн'),
                'energetic' => $this->fem($sex, 'Энергичн'),
                'live_in_moment' => $this->fem($sex, 'Живущ', ['ая', 'ий']) . ' в момент',
                'pragmatic' => $this->fem($sex, 'Прагматичн'),
                'ambitious' => $this->fem($sex, 'Амбициозн'),
                'modest' => $this->fem($sex, 'Скромн'),
                'self' => $this->fem($sex, 'Самостоятельн'),
                'need_support' => $this->fem($sex, 'Нуждающ', ['аяся', 'ийся']) . ' в помощи',
                'housewifely' => $this->fem($sex, 'Хозяйственн'),
                'indifferent_life' => $this->fem($sex, 'Равнодушн') . ' к жизни',
                'aristocratic' => $this->fem($sex, 'Аристократическ', ['ая', 'ий']),
                'simple' => $this->fem($sex, 'Прост', ['ая', 'ой']),
                'sport' => $this->fem($sex, 'Спротив'),
                'indifferent_sport' => $this->fem($sex, 'Безразличн') . ' к спорту',
                'lover_going_out' => $this->fem($sex, 'Любител', ['ьница', 'ь']) . ' гулять',
                'home' => $this->fem($sex, 'Домаш', ['яя', 'ий']),
                'adventuress' => 'Авантюрист',
                'rational' => $this->fem($sex, 'Рациональн'),
                'strong-willed' => $this->fem($sex, 'Волев', ['ая', 'ой']),
                'soft' => $this->fem($sex, 'Мягк', ['ая', 'ий']),
                'lark' => 'Жаворонок',
                'owl' => 'Сова',
                'humanitarian' => 'Гуманитарий',
                'mathematical' => 'Математик',
                'open' => $this->fem($sex, 'Открыт'),
                'cautious' => $this->fem($sex, 'Осторожн'),
                'extrovert' => 'Экстроверт',
                'introvert' => 'Интроверт',
                'infantile' => $this->fem($sex, 'Инфантильн'),
                'mature' => $this->fem($sex, 'Зрел'),
                'dependent' => $this->fem($sex, 'Зависящ'),
                'feminine' => $this->fem($sex, 'Женственн'),
                'courageous' => $this->fem($sex, 'Мужественн'),
                'confident' => $this->fem($sex, 'Уверенн') . ' в себе',
                'delicate' => $this->fem($sex, 'Нежн'),
                'live_here_now' => $this->fem($sex, 'Умеющ') . ' жить здесь и сейчас',
                'graceful' => $this->fem($sex, 'Грациозн'),
                'sociable' => $this->fem($sex, 'Общительн'),
                'smiling' => $this->fem($sex, 'Улыбчив'),
                'artistic' => $this->fem($sex, 'Артистичн'),
                'good' => $this->fem($sex, 'Добр'),
                'stylish' => $this->fem($sex, 'Стильн'),
                'economical' => $this->fem($sex, 'Экономн'),
                'business' => $this->fem($sex, 'Делов', ['ая', 'ой']),
                'sports' => $this->fem($sex, 'Спортивн'),
                'fearless' => $this->fem($sex, 'Бесстрашн'),
                'shy' => $this->fem($sex, 'Застенчив'),
                'playful' => $this->fem($sex, 'Игрив'),
            ];
        } else {
            $data = [
                'calm' => 'Calm',
                'energetic' => 'Energetic',
                'happy' => 'Happy',
                'modest' => 'Modest',
                'purposeful' => 'Purposeful',
                'weak-willed' => 'Weak-Willed',
                'self' => 'Self',
                'dependent' => 'Dependent',
                'feminine' => 'Feminine',
                'courageous' => 'Courageous',
                'confident' => 'Confident',
                'delicate' => 'Delicate',
                'live_here_now' => 'Live Here Now',
                'pragmatic' => 'Pragmatic',
                'graceful' => 'Graceful',
                'sociable' => 'Sociable',
                'smiling' => 'Smiling',
                'housewifely' => 'Housewifely',
                'ambitious' => 'Ambitious',
                'artistic' => 'Artistic',
                'good' => 'Good',
                'aristocratic' => 'Aristocratic',
                'stylish' => 'Stylish',
                'economical' => 'Economical',
                'business' => 'Business',
                'sports' => 'Sports',
                'fearless' => 'Fearless',
                'shy' => 'Shy',
                'playful' => 'Playful',
            ];
        }

        try {
            return $data[$quality];
        } catch (\Exception $exception) {
            return $quality;
        }
    }

    public function pets(string $quality, string $sex)
    {
        $lang = Cache::get('lang');

        if ($lang == 'ru') {
            $data = [
                'have_pets' => 'Есть животные',
                'no_pets' => 'Нет животных',
                'love_not_home' => 'Люблю, но нет дома',
                'not_love' => 'Не люблю',
                'neutral' => 'Нейтрально'
            ];
        } else {
            $data = [
                'have_pets' => 'Есть животные',
                'no_pets' => 'Нет животных',
                'love_not_home' => 'Люблю, но нет дома',
                'not_love' => 'Не люблю',
                'neutral' => 'Нейтрально'
            ];
        }

        try {
            return $data[$quality];
        } catch (\Exception $exception) {
            return $quality;
        }
    }

    public function fm(string $quality, string $sex)
    {
        $lang = Cache::get('lang');

        if ($lang == 'ru') {
            $data = [
                'film' => 'Фильмы',
                'book' => 'Книги',
                'film,book' => 'Книги и фильмы',
            ];
        } else {
            $data = [
                'film' => 'Фильмы',
                'book' => 'Книги',
                'film,book' => 'Книги и фильмы',
            ];
        }

        try {
            return $data[$quality];
        } catch (\Exception $exception) {
            return $quality;
        }
    }

    public function relax(string $quality, string $sex)
    {
        $lang = Cache::get('lang');

        if ($lang == 'ru') {
            $data = [
                'beach' => 'Пляж',
                'extreme' => 'Экстремальный',
                'calm' => 'Спокойный',
            ];
        } else {
            $data = [
                'beach' => 'Пляж',
                'extreme' => 'Экстремальный',
                'calm' => 'Спокойный',
            ];
        }

        try {
            return $data[$quality];
        } catch (\Exception $exception) {
            return $quality;
        }
    }

    public function smoking(string $quality, string $sex)
    {
        $lang = Cache::get('lang');

        if ($lang == 'ru') {
            $data = [
                'dont_smoking' => 'Не курю',
                'rarely' => 'Редко',
                'smoking' => 'Курю',
                'no_matter' => 'Не важно'
            ];
        } else {
            $data = [
                'dont_smoking' => 'Не курю',
                'rarely' => 'Редко',
                'smoking' => 'Курю',
                'no_matter' => 'Не важно'
            ];
        }

        try {
            return $data[$quality];
        } catch (\Exception $exception) {
            return $quality;
        }
    }

    public function alcohol(string $quality, string $sex)
    {
        $lang = Cache::get('lang');

        if ($lang == 'ru') {
            $data = [
                'no' => 'Не пью',
                'rarely' => 'Редко',
                'often' => 'Часто',
                'very_often' => 'Очень часто',
                'no_matter' => 'Не важно'
            ];
        } else {
            $data = [
                'no' => 'Не пью',
                'rarely' => 'Редко',
                'often' => 'Часто',
                'very_often' => 'Очень часто',
                'no_matter' => 'Не важно'
            ];
        }

        try {
            return $data[$quality];
        } catch (\Exception $exception) {
            return $quality;
        }
    }

    public function religion(string $quality, string $sex)
    {
        $lang = Cache::get('lang');

        if ($lang == 'ru') {
            $data = [
                'christianity' => 'Христианство',
                'judaism' => 'Иудаизм',
                'catholicism' => 'Католицизм',
                'islam' => 'Ислам',
                'buddhism' => 'Буддизм',
                'atheism' => 'Атеизм',
                'hinduism' => 'Индуизм',
                'no_matter' => 'Не важно'
            ];
        } else {
            $data = [
                'christianity' => 'Христианство',
                'judaism' => 'Иудаизм',
                'catholicism' => 'Католицизм',
                'islam' => 'Ислам',
                'buddhism' => 'Буддизм',
                'atheism' => 'Атеизм',
                'hinduism' => 'Индуизм',
                'no_matter' => 'Не важно'
            ];
        }

        try {
            return $data[$quality];
        } catch (\Exception $exception) {
            return $quality;
        }
    }

    public function sport(string $quality, string $sex)
    {
        $lang = Cache::get('lang');

        if ($lang == 'ru') {
            $data = [
                'playing_sports' => 'Активно занимаюсь',
                'sometimes' => 'Иногда',
                'not_engaged' => 'Не занимаюсь',
                'no_matter' => 'Не важно'
            ];
        } else {
            $data = [
                'playing_sports' => 'Активно занимаюсь',
                'sometimes' => 'Иногда',
                'not_engaged' => 'Не занимаюсь',
                'no_matter' => 'Не важно'
            ];
        }

        try {
            return $data[$quality];
        } catch (\Exception $exception) {
            return $quality;
        }
    }

    public function education(string $quality, string $sex)
    {
        $lang = Cache::get('lang');

        if ($lang == 'ru') {
            $data = [
                'middle' => 'Среднее',
                'college' => 'Колледж',
                'unfinished_higher' => 'Не законченное высшее',
                'specialist' => 'Специалист',
                'bachelor' => 'Бакалавриат',
                'master' => 'Магистр',
                'phd' => 'Доктор наук',
                'no_matter' => 'Не важно'
            ];
        } else {
            $data = [
                'middle' => 'Среднее',
                'college' => 'Колледж',
                'unfinished_higher' => 'Не законченное высшее',
                'specialist' => 'Специалист',
                'bachelor' => 'Бакалавриат',
                'master' => 'Магистр',
                'phd' => 'Доктор наук',
                'no_matter' => 'Не важно'
            ];
        }

        try {
            return $data[$quality];
        } catch (\Exception $exception) {
            return $quality;
        }
    }

    public function work(string $quality, string $sex)
    {
        $lang = Cache::get('lang');

        if ($lang == 'ru') {
            $data = [
                'work' => 'Работаю',
                'temp_no_work' => 'Временно без работы',
                'no_work' => 'Нет работы',
                'study' => 'Учусь',
                'pensioner' => 'Пенсионер',
            ];
        } else {
            $data = [
                'work' => 'Работаю',
                'temp_no_work' => 'Временно без работы',
                'no_work' => 'Нет работы',
                'study' => 'Учусь',
                'pensioner' => 'Пенсионер',
            ];
        }

        try {
            return $data[$quality];
        } catch (\Exception $exception) {
            return $quality;
        }
    }

    public function sleep(string $quality, string $sex)
    {
        $lang = Cache::get('lang');

        if ($lang == 'ru') {
            $data = [
                'owl' => 'Сова',
                'lark' => 'Жаворонок',
                'differently' => 'По-разному',
            ];
        } else {
            $data = [
                'owl' => 'Сова',
                'lark' => 'Жаворонок',
                'differently' => 'По-разному',
            ];
        }

        try {
            return $data[$quality];
        } catch (\Exception $exception) {
            return $quality;
        }
    }

    public function clubs(string $quality, string $sex)
    {
        $lang = Cache::get('lang');

        if ($lang == 'ru') {
            $data = [
                'yes' => 'Да',
                'no' => 'Нет',
                'sometimes' => 'Иногда',
            ];
        } else {
            $data = [
                'yes' => 'Да',
                'no' => 'Нет',
                'sometimes' => 'Иногда',
            ];
        }

        try {
            return $data[$quality];
        } catch (\Exception $exception) {
            return $quality;
        }
    }
}
