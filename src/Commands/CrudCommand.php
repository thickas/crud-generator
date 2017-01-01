<?php

namespace Thickas\CrudGenerator\Commands;

use File;
use Illuminate\Console\Command;

class CrudCommand extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:generate
                            {name : The name of the Crud.}
                            {--fields= : Fields name for the form & migration.}                          
                            {--controller-namespace= : Namespace of the controller.}
                            {--model-namespace= : Namespace of the model inside "app" dir.}
                            {--pk=id : The name of the primary key.}                                                        
                            {--relationships= : The relationships for the model.}
                            {--route=yes : Include Crud route to routes.php? yes|no.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Crud including controller, model & migrations.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {        
        $name = $this->argument('name');
        $modelName = ucfirst(str_singular($name));
        $migrationName = str_plural(snake_case($name));
        $tableName = strtolower($migrationName);

        $controllerNamespace = ($this->option('controller-namespace')) ? $this->option('controller-namespace') . '\\' : '';
        $controllerNamespace = ucfirst($controllerNamespace);

        $modelNamespace = ($this->option('model-namespace')) ? trim($this->option('model-namespace')) . '\\' : '';
        $modelNamespace = ucfirst($modelNamespace);

        $fields = rtrim($this->option('fields'), ';');
        $primaryKey = $this->option('pk');
        $relationships = $this->option('relationships');

        $this->call('crud:migration', [
            'name' => $migrationName,
            '--fields' => $fields,
            '--pk' => $primaryKey
        ]);

        $this->call('crud:model', [
            'name' => $modelNamespace . $modelName,
            '--fields' => $fields,
            '--table' => $tableName,
            '--pk' => $primaryKey,
            '--relationships' => $relationships
        ]);


        $this->call('crud:controller', [
            'name' => $controllerNamespace . $modelName . 'Controller',
            '--model-name' => 'App\\' . $modelNamespace . $modelName,
            '--fields' => $fields
        ]);

        // For optimizing the class loader
        $this->callSilent('optimize');

        // Updating the Http/routes.php file
        $routeFile = config('admin.directory') . '/routes.php';
        if (file_exists($routeFile) && (strtolower($this->option('route')) === 'yes')) {
            $routeName = ($this->option('controller-namespace')) ? strtolower($this->option('controller-namespace')) . '/' : '';
            $routeName .= strtolower(str_plural($name));
            $routeName = str_replace('\\', '/', $routeName);

            $route = "\$router->resource('$routeName',$controllerNamespace{$name}Controller::class);";
            File::put($routeFile, str_replace("});", "$route\r\n});", File::get($routeFile)));
            $this->info('Crud/Resource route added to ' . $routeFile);
        }
    }

}
