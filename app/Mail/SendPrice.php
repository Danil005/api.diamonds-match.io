<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPrice extends Mailable
{
    use Queueable, SerializesModels;

    private string $name = '';
    private string $lang = '';

    private array $prices = [
        'rub' => [
            'v1' => '30 000 ₽',
            'v2' => '160 000 ₽',
            'v3' => '350 000 ₽',
            'p1' => '100 000 ₽',
            'p2' => '300 000 ₽',
            'p3' => '500 000 ₽',
        ],
        'eur' => [
            'v1' => '700 €',
            'v2' => '1 800 €',
            'v3' => '5 000 €',
            'p1' => '2 000 €',
            'p2' => '5 000 €',
            'p3' => '8 000 €',
        ],
        'usd' => [
            'v1' => '1 000 $',
            'v2' => '1 800 $',
            'v3' => '8 000 $',
            'p1' => '2 000 $',
            'p2' => '5 000 $',
            'p3' => '8 000 €',
        ]
    ];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $name, string $lang = 'ru')
    {
        $this->name = $name;
        $this->lang = $lang;
    }

    private function pricing(string $country)
    {
        $countriesEuropeRu = collect([
            'Австрия', 'Бельгия', 'Великобритания', 'Германия', 'Ирландия', 'Лихтенштейн', 'Люксембург', 'Монако',
            'Нидерланды', 'Франция', 'Швейцария', 'Белоруссия', 'Болгария', 'Венгрия', 'Молдавия', 'Польша',
            'Румыния', 'Словакия', 'Чехия', 'Украина', 'Дания', 'Исландия', 'Литва', 'Норвегия', 'Финляндия', 'Эстония',
            'Швеция', 'Албания', 'Андорра', 'Босния и Герцеговина', 'Ватикан', 'Греция', 'Испания', 'Италия',
            'Северная Македония', 'Мальта', 'Португалия', 'Сан-Марино', 'Сербия', 'Словения', 'Хорватия', 'Черногория'
        ]);

        $countriesEuropeEn = collect([
            'Austria', 'Belgium', 'Great Britain', 'Germany', 'Ireland', 'Liechtenstein', 'Luxembourg', 'Monaco',
            'Netherlands', 'France', 'Switzerland', 'Belarus', 'Bulgaria', 'Hungary', 'Moldova', 'Poland',
            'Romania', 'Slovakia', 'Czech Republic', 'Ukraine', 'Denmark', 'Iceland', 'Lithuania', 'Norway', 'Finland', 'Estonia',
            'Sweden', 'Albania', 'Andorra', 'Bosnia and Herzegovina', 'Vatican', 'Greece', 'Spain', 'Italy',
            'North Macedonia', 'Malta', 'Portugal', 'San Marino', 'Serbia', 'Slovenia', 'Croatia', 'Montenegro'
        ]);

        $ru = collect($countriesEuropeRu)->filter(function ($item) use ($country) {
            return false !== stristr($item->name, $country);
        })?->first();

        $en = collect($countriesEuropeEn)->filter(function ($item) use ($country) {
            return false !== stristr($item->name, $country);
        })?->first();

        if( $ru || $en ) {
            $pricing = 'eur';
        } else if($country == 'Россия' || $country == 'россия' || $country == 'Russia') {
            $pricing = 'rub';
        } else {
            $pricing = 'usd';
        }

        return $pricing;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_USERNAME'))->view('mails.sendPrice', [
            'name' => $this->name,
            'lang' => $this->lang,
            'pricing' => $this->prices[$this->pricing('США')]
        ])->subject('Наши тарифы');
    }
}
