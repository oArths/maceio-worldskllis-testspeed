<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use LDAP\Result;

class GenereteModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generete:model {name} {table} {json}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $Modelname = $this->argument('name');
        $Tablename = $this->argument('table');
        $jsonName = $this->argument('json');

        $path = public_path("M¡dias\\$jsonName.json");
        $file = file_get_contents($path);
        $decode = json_decode($file, true);
        DB::table($Tablename)->insert($decode);

        Artisan::call("make:model {$Modelname}");
        Artisan::call("make:controller {$Modelname}Controller");

        $collums = Schema::getColumnListing($Tablename);
        $fillable = implode(',', $collums);

        $modelPath = app_path("Models\\{$Modelname}.php");
        $modelContent = file_get_contents($modelPath);
        $fillableProprity = "protected \$fillable = ['{$fillable}'];";
        $TableProprity = "protected \$table = '{$Tablename}';";
        $TimestampProprity = "public \$timestamps = false;";
        $modelContent = str_replace("use HasFactory;", "use HasFactory;
        {$fillableProprity} 
        {$TableProprity} 
        {$TimestampProprity}", $modelContent);

        file_put_contents($modelPath, $modelContent);
        $this->info("Model {$Modelname} criado com sucesso e atributos preenchidos.");
    }
}
