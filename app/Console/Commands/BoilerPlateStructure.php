<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BoilerPlateStructure extends Command
{
    protected $signature = 'make:structure {name}';
    protected $description = 'Create Interface, Repository, Service, and Request classes';

    public function handle()
    {
        $name = $this->argument('name');

        $this->createInterface($name);
        $this->createRepository($name);
        $this->createService($name);
        $this->createRequests($name);

        $this->info("Structure for $name created successfully.");
    }

    protected function createInterface($name)
    {
        $interfacePath = app_path("Interfaces/{$name}Interface.php");
        $interfaceTemplate = "<?php

namespace App\Interfaces;

interface {$name}Interface
{
    public function getAll{$name}s();
    public function get{$name}ById(\$id);
    public function create{$name}(array \$data);
    public function update{$name}(\$id, array \$data);
    public function delete{$name}(\$id);
}
";
        File::put($interfacePath, $interfaceTemplate);
        $this->info("Interface created at {$interfacePath}");
    }

    protected function createRepository($name)
    {
        $smallName = ucfirst($name);
        $repositoryPath = app_path("Repositories/{$name}Repository.php");
        $repositoryTemplate = "<?php

namespace App\Repositories;

use App\Interfaces\\{$name}Interface;
use Illuminate\Support\Facades\Log;
use App\Models\\{$name};

class {$name}Repository implements {$name}Interface
{
    public function getAll{$name}s()
    {
        return {$name}::all();
    }

    public function get{$name}ById(\$id)
    {
        return {$name}::findOrFail(\$id);
    }

    public function create{$name}(array \$data)
    {
    \DB::beginTransaction();
        try {
            {$smallName}Store = {$name}::create(\$data);
            \DB::commit();
            return response()->json(['success' => 'Successfully Created {$name}', '{$smallName}' => {$smallName}Store], 200);
        } catch (\Exception \$e) {
            \DB::rollBack();
            Log::error('Error Creating {$name}: ' . \$e->getMessage());
            return response()->json(['error' => \"Error Creating {$name}: \" . \$e->getMessage()], 500);
        }
    }

    public function update{$name}(\$id, array \$data)
    {

        \DB::beginTransaction();
        try {
            \${$smallName}Update = {$name}::findOrFail(\$id);
            \${$smallName}Update->update(\$data);
            \DB::commit();
            return response()->json([
                'success' => 'Successfully Updated {$name}',
                '{$smallName}' => \${$smallName}Update
            ], 200);
        } catch (\Exception \$e) {
            \DB::rollBack();
            Log::error('Error Updating {$name}: ' . \$e->getMessage());
            return response()->json([
                'error' => 'Error Updating {$name}: ' . \$e->getMessage()
            ], 500);
        }
    }

    public function delete{$name}(\$id)
    {
        \DB::beginTransaction();
        try {
            \${$smallName} = {$name}::findOrFail(\$id);
            \${$smallName}->delete();
            \DB::commit();
            return response()->json([
                'success' => 'Successfully Deleted {$name}'
            ], 200);
        } catch (\Exception \$e) {
            \DB::rollBack();
            Log::error('Error Deleting {$name}: ' . \$e->getMessage());
            return response()->json([
                'error' => 'Error Deleting {$name}: ' . \$e->getMessage()
            ], 500);
        }
    }
}
";
        File::put($repositoryPath, $repositoryTemplate);
        $this->info("Repository created at {$repositoryPath}");
    }

    protected function createService($name)
    {
        $smallName = ucfirst($name);
        $servicePath = app_path("Services/{$name}Service.php");
        $serviceTemplate = "<?php

namespace App\Services;

use App\Interfaces\\{$name}Interface;

class {$name}Service
{
    protected \${$smallName}Repository;

    public function __construct({$name}Interface \${$smallName}Repository)
    {
        \$this->{$smallName}Repository = \${$smallName}Repository;
    }

    public function getAll{$name}s()
    {
        return \$this->{$smallName}Repository->getAll{$name}s();
    }

    public function get{$name}ById(\$id)
    {
        return \$this->{$smallName}Repository->get{$name}ById(\$id);
    }

    public function create{$name}(array \$data)
    {
        return \$this->{$smallName}Repository->create{$name}(\$data);
    }

    public function update{$name}(\$id, array \$data)
    {
        return \$this->{$smallName}Repository->update{$name}(\$id, \$data);
    }

    public function delete{$name}(\$id)
    {
        return \$this->{$smallName}Repository->delete{$name}(\$id);
    }
}
";
        File::put($servicePath, $serviceTemplate);
        $this->info("Service created at {$servicePath}");
    }

    protected function createRequests($name)
    {
        $storeRequestPath = app_path("Http/Requests/{$name}StoreRequest.php");
        $storeRequestTemplate = "<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class {$name}StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            '' => 'required|string|max:255',
        ];
    }
}
";
        File::put($storeRequestPath, $storeRequestTemplate);
        $this->info("Store Request created at {$storeRequestPath}");

//         $updateRequestPath = app_path("Http/Requests/{$name}UpdateRequest.php");
//         $updateRequestTemplate = "<?php

// namespace App\Http\Requests;

// use Illuminate\Foundation\Http\FormRequest;

// class {$name}UpdateRequest extends FormRequest
// {
//     public function authorize()
//     {
//         return true;
//     }

//     public function rules()
//     {
//         return [
//             'name' => 'sometimes|required|string|max:255',
//         ];
//     }
// }
// ";
//         File::put($updateRequestPath, $updateRequestTemplate);
//         $this->info("Update Request created at {$updateRequestPath}");
    }
}
