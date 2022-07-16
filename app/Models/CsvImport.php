<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CsvImport extends Model
{
    use HasFactory;

    protected $table = "csv_import";

    protected $guarded = [];

    public static function emptyLastNamesResume()
    {
        return CsvImport::selectRaw("count(*) as total, if(trim(last_name) <> '', 'Preenchidos', 'Vazios') as finalName")->groupBy("finalName");
    }

    public static function emptyGendersResume()
    {
        return CsvImport::selectRaw("count(*) as total, if(trim(gender) <> '', 'Preenchidos', 'Vazios') as finalName")->groupBy("finalName");
    }

    public static function getWithvalidEmails()
    {
        return CsvImport::whereRaw('`email` REGEXP "' . "^[a-zA-Z0-9][a-zA-Z0-9.!#$%&'*+-/=?^_`{|}~]*?[a-zA-Z0-9._-]?@[a-zA-Z0-9][a-zA-Z0-9._-]*?[a-zA-Z0-9]?\\.[a-zA-Z]{2,63}$" . '"');
    }

    public static function getWithInvalidEmails()
    {
        return CsvImport::whereRaw('`email` NOT REGEXP "' . "^[a-zA-Z0-9][a-zA-Z0-9.!#$%&'*+-/=?^_`{|}~]*?[a-zA-Z0-9._-]?@[a-zA-Z0-9][a-zA-Z0-9._-]*?[a-zA-Z0-9]?\\.[a-zA-Z]{2,63}$" . '"');
    }

}
