<?php

namespace Thickas\CrudGenerator\Commands;

use Illuminate\Console\GeneratorCommand;

class CrudMigrationCommand extends GeneratorCommand {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:migration
                            {name : The name of the migration.}
                            {--fields= : Fields name for the form & migration.}                           
                            {--pk=id : The name of the primary key.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Migration';

    /**
     *  Migration column types collection.
     *
     * @var array
     */
    protected $typeLookup = [
        'char' => 'char',
        'date' => 'date',
        'datetime' => 'dateTime',
        'time' => 'time',
        'timestamp' => 'timestamp',
        'text' => 'text',
        'mediumtext' => 'mediumText',
        'longtext' => 'longText',
        'json' => 'json',
        'jsonb' => 'jsonb',
        'binary' => 'binary',
        'number' => 'integer',
        'integer' => 'integer',
        'bigint' => 'bigInteger',
        'mediumint' => 'mediumInteger',
        'tinyint' => 'tinyInteger',
        'smallint' => 'smallInteger',
        'boolean' => 'boolean',
        'decimal' => 'decimal',
        'double' => 'double',
        'float' => 'float',
        'enum' => 'enum',
    ];
    protected $modifierLookup = [
        'comment',
        'default',
        'nullable',
        'unsigned',
    ];

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() {
        return __DIR__ . '/../stubs/migration.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     *
     * @return string
     */
    protected function getPath($name) {
        $name = str_replace($this->laravel->getNamespace(), '', $name);
        $datePrefix = date('Y_m_d_His');

        return database_path('/migrations/') . $datePrefix . '_create_' . str_plural(strtolower($name)) . '_table.php';
    }

    /**
     * Build the model class with the given name.
     *
     * @param  string  $name
     *
     * @return string
     */
    protected function buildClass($name) {
        $stub = $this->files->get($this->getStub());

        $tableName = str_plural(strtolower($this->argument('name')));
        $className = 'Create' . str_replace(' ', '', ucwords(str_replace('_', ' ', $tableName))) . 'Table';

        $fields = rtrim($this->option('fields'), ';');
        $fields = explode(';', $fields);

        $tabIndent = '    ';

        $schemaFields = '';
        foreach ($fields as $field) {
            $fieldArray = explode('#', $field);
            $fieldName = trim($fieldArray[0]);
            $fieldType = trim($fieldArray[1]);
            $fieldModifier = '';
            if (isset($fieldArray[2]) && in_array(trim($fieldArray[2]), $this->modifierLookup)) {
                if (isset($fieldArray[3]) && ($fieldArray[2] == 'comment' || $fieldArray[2] == 'default')) {
                    $fieldModifier = "->" . trim($fieldArray[2]) . "(" . trim($fieldArray[3]) . ")";
                } else {
                    $fieldModifier = "->" . trim($fieldArray[2]) . "()";
                }
            }

            if (isset($this->typeLookup[$fieldType])) {
                $fieldType = $this->typeLookup[$fieldType];
            } else {
                $fieldType = 'string';
            }
            $schemaFields .= "\$table->$fieldType('$fieldName')$fieldModifier;\n$tabIndent$tabIndent$tabIndent";
        }

        $primaryKey = $this->option('pk');
        $schemaUp = "Schema::create('$tableName', function(Blueprint \$table) {\n$tabIndent$tabIndent$tabIndent"
                . "\$table->increments('$primaryKey');\n$tabIndent$tabIndent$tabIndent"
                . "$schemaFields"
                . "\$table->timestamps();\n$tabIndent$tabIndent"
                . "});";

        $schemaDown = "Schema::drop('" . $tableName . "');";

        return $this->replaceSchemaUp($stub, $schemaUp)
                        ->replaceSchemaDown($stub, $schemaDown)
                        ->replaceClass($stub, $className);
    }

    /**
     * Replace the schema_up for the given stub.
     *
     * @param  string  $stub
     * @param  string  $schemaUp
     *
     * @return $this
     */
    protected function replaceSchemaUp(&$stub, $schemaUp) {
        $stub = str_replace(
                '{{schema_up}}', $schemaUp, $stub
        );

        return $this;
    }

    /**
     * Replace the schema_down for the given stub.
     *
     * @param  string  $stub
     * @param  string  $schemaDown
     *
     * @return $this
     */
    protected function replaceSchemaDown(&$stub, $schemaDown) {
        $stub = str_replace(
                '{{schema_down}}', $schemaDown, $stub
        );

        return $this;
    }

}
