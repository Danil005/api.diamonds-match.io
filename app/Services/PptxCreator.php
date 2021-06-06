<?php

namespace App\Services;

use App\Models\Questionnaire;
use App\Utils\TranslateFields;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PptxCreator
{
    use TranslateFields;

    public function create(Request $request)
    {
        $id = $request->questionnaire_id;
        Storage::makeDirectory('public/pptx/generate/'.$id);
        sleep(2);

        $connection = ssh2_connect('45.141.79.57', 22);
        ssh2_auth_password($connection, env('SSH_U'), env('SSH_P'));


        for ($i = 1; $i <= 5; $i++) {
            $stream = ssh2_exec($connection, 'wkhtmltoimage https://api.diamondsmatch.org/getSlide/' . $i . '/' . $id . ' /var/www/html/storage/app/public/pptx/generate/'.$id.'/s' . $i . '.jpg');
            stream_set_blocking($stream, true);
            stream_get_contents($stream);
        }
        for ($i = 1; $i <= 5; $i++) {
            $stream = ssh2_exec(
                $connection,
                'convert /var/www/html/storage/app/public/pptx/generate/'.$id.'/s' . $i . '.jpg -crop 784x1119+0+0 /var/www/html/storage/app/public/pptx/generate/'.$id.'/s' . $i . '.jpg'
            );
            stream_set_blocking($stream, true);
            stream_get_contents($stream);
        }

        $slides = '';
        for($i = 1; $i <= 5; $i++) $slides .= '/var/www/html/storage/app/public/pptx/generate/'.$id.'/s'.$i . ' ';
        $slides = trim($slides);

        $stream = ssh2_exec($connection, 'convert ' . $slides . ' /var/www/html/storage/app/public/pptx/generate/'.$id.'/presentation.pdf');
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);

        $path = Storage::disk('public')->path('/pptx/generate/'.$id.'/presentation.pdf');
    }

    public function getSlide($slide, $questionnaireId)
    {
        dd($slide, $questionnaireId);
        $questionnaire = new Questionnaire();
        $questionnaire = $questionnaire->my()->where('questionnaires.id', $questionnaireId)->first()?->toArray();

        if ($questionnaire == null)
            return false;
        return view('pdf.slide' . $slide, ['q' => $questionnaire]);
    }
}
