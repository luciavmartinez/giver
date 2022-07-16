<?php

namespace App\Http\Controllers;

use App\Models\CsvImport;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class CsvImportController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('CsvEditor', [
            'sobrenomes' => CsvImport::emptyLastNamesResume()->get(),
            'generos' => CsvImport::emptyGendersResume()->get(),
            'emailsValidos' => CsvImport::getWithvalidEmails()->count(),
            'emailsInvalidos' => CsvImport::getWithInvalidEmails()->count()]
        );
    }

    /**
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        try{
            $this->validateFile(request()->file);

            $finalFile = $this->uploadFile();

            CsvImport::query()->truncate();

            $total = $this->importFileToDatabase($finalFile);

            return $this->response("Arquivo importado com sucesso ({$total} linhas)!", ["total" => $total]);

        }catch (Exception $e){

            return $this->response($e->getMessage());

        }
    }

    /**
     * @param $file
     * @return void
     */
    private function validateFile($file = null)
    {
        /* @var UploadedFile $file */
        if (empty($file)) {
            throw new \Exception("Nenhum arquivo enviado para processamento");
        }

        if ($file->getMimeType() != "text/csv") {
            throw new \Exception("Arquivo deve ser do tipo csv");
        }
    }

    /**
     * @param string $message
     * @param array $info
     * @return \Inertia\Response
     */
    private function response(string $message = "Erro inesperado", array $info = [])
    {
        return Inertia::render('CsvEditor', [
            "flash" => [
                "message" => $message,
                "info" => $info
            ]
        ]);
    }

    /**
     * @return string
     */
    private function uploadFile()
    {
        /* @var $file UploadedFile */
        $dir = Carbon::now()->format('Y') . DIRECTORY_SEPARATOR . Carbon::now()->format('m');

        $file = request('file');
        $path_parts = pathinfo(request('file')->getClientOriginalName());

        $uploadfile = $path_parts['filename'] . "-" . time() . "." . $path_parts['extension'];
        $path = $file->storeAs($dir, $uploadfile);

        return storage_path('app' . DIRECTORY_SEPARATOR . $path);
    }

    /**
     * @param $file
     * @return array
     */
    private function importFileToDatabase($file)
    {
        $index = 1;
        $totalInseridos = 0;
        $handle = fopen($file, 'r');

        while (($line = fgetcsv($handle)) !== FALSE) {

            if ($index == 1) {
                $header = $line;
            } else {
                try{
                    $data = array_combine($header, $line);
                    $data = $this->convertUtf8IfNecessary($data);
                    CsvImport::create($data);
                    $totalInseridos++;
                }catch (\Exception $e){}
            }

            $index++;
        }

        return $totalInseridos;
   }

    /**
     * @param $data
     * @return array|mixed
     */
   private function convertUtf8IfNecessary($data)
   {
       if (mb_check_encoding(implode("||", $data), "UTF-8") === false){
           $data = array_map("utf8_encode", $data);
       }

       return $data;
   }

}
