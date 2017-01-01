<?php

namespace Thickas\CrudGenerator\Commands;

use Illuminate\Console\GeneratorCommand;

class CrudControllerCommand extends GeneratorCommand {

    /**
     * The name and signature of the console command.
     *
     * php artisan crud:controller Home\PostController --fields="title#string;age#number" --model-name=App\Models\Post
     * 
     * @var string
     */
    protected $signature = 'crud:controller
                            {name : The name of the controler.}                           
                            {--model-name= : The name of the Model.} 
                            {--fields= : Fields name for the form & migration.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new resource controller.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() {
        return __DIR__ . '/../stubs/controller.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace) {
        $directory = config('admin.directory');
        $namespace = ucfirst(basename($directory));
        return $rootNamespace . "\\$namespace\Controllers";
    }

    protected function buildClass($name) {
        $stub = $this->files->get($this->getStub());

        $modelName = $this->option('model-name');
        if ($modelName == '') {           
            $modelName = explode('\\', $this->argument('name'));
            $modelName = 'App\\' . str_replace('Controller', '', end($modelName));
            $modelName = ucwords($modelName);
        }
       
        $fields = explode(';', $this->option('fields'));

        $grid = '';
        $form = '';
        foreach ($fields as $field) {
            $fieldArray = explode('#', $field);
            if (count($fieldArray)<2)                
            {
                continue;
            }
            $grid .= $this->buildGrid($fieldArray);
            $form .= $this->buildForm($fieldArray);
        }


        $this->replaceNamespace($stub, $name)
                ->replaceModelName($stub, $modelName)
                ->replaceGrid($stub, $grid)
                ->replaceForm($stub, $form)
                ->replaceHeader($stub, $modelName)
        ;
        return $this->replaceClass($stub, $name);
    }

    protected function replaceModelName(&$stub, $modelName) {
        $stub = str_replace(
                'DummyModel', $modelName, $stub
        );

        return $this;
    }

    protected function replaceGrid(&$stub, $grid) {
        $stub = str_replace(
                '{{grid}}', $grid, $stub
        );
        
        return $this;
    }

    protected function replaceForm(&$stub, $form) {        
        $stub = str_replace(
                '{{form}}', $form, $stub
        );
        return $this;
    }
    
    protected function replaceHeader(&$stub, $modelName) {
        $modelName = explode('\\', $modelName);
        $modelName = end($modelName);
        $stub = str_replace(
                '{{header}}', $modelName, $stub
        );
        return $this;
    }

    protected function buildForm($fieldArray) {
        $fieldName = $fieldArray[0];
        $fieldType = $fieldArray[1];
        $option = '';
        switch ($fieldType) {
            case 'select':
                $option = "->options([])";
                break;

            case 'time':
                $option = "->format('HH:mm:ss')";
                break;

            case 'date':
                $option = "->format('YYYY-MM-DD')";
                break;

            case 'datetime':
                $option = "->format(''YYYY-MM-DD HH:mm:ss')";
                break;

            case 'text':
                $fieldType = 'textarea';
                break;

            case 'string':
                $fieldType = 'text';
                break;

            default:
        }
         $tabIndent = '    ';
        return "\$form->$fieldType('$fieldName','$fieldName')$option;\n$tabIndent$tabIndent$tabIndent";
    }
    protected function buildGrid($fieldArray) { 
         $tabIndent = '    ';
        return "\$grid->{$fieldArray[0]}('{$fieldArray[0]}');\n$tabIndent$tabIndent$tabIndent";
    }

}
