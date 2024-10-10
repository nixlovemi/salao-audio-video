<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\ModelValidation;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class People extends Model
{
    use HasFactory;
    use \App\Traits\BaseModelTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    protected $attributes = [
        'active' => true,
    ];

    protected $appends = [
        'codedId',
    ];

    // relations
    // =========

    // class functions
    /**
     * https://laravel.com/docs/8.x/validation#available-validation-rules
     */
    public function validateModel(): ApiResponse
    {
        $validation = new ModelValidation($this->toArray());
        $validation->addIdField(self::class, 'Pessoa', 'id', 'ID');
        $validation->addField('name', ['required', 'string', 'min:2', 'max:40'], 'Nome');
        $validation->addField('active', ['required', 'filled', 'boolean'], 'Ativo');

        return $validation->validate();
    }
    // ===============

    // static functions
    public static function fSave(Request $request): ApiResponse
    {
        // get model for insert or update
        $codedId = $request->input('f-pid');
        if (!empty($codedId)) {
            $People = self::getModelByCodedId($codedId);
            if ($People === null) {
                return new ApiResponse(true, 'Pessoa não encontrada para edição');
            }
        } else {
            $People = new self();
        }
        $isEdit = $People->id > 0;

        // form + validation
        $form = [
            'name' => $request->input('f-name') ?: '',
            'active' => ($request->input('f-active') !== '') ? $request->input('f-active'): '',
        ];

        // fill model
        $People->fill($form);
        $validation = $People->validateModel();
        if ($validation->isError()) {
            return $validation;
        }

        // save model
        try {
            $People->save();
            $People->refresh();
        } catch (\Exception $e) {
            return new ApiResponse(true, 'Ocorreu um problema ao salvar a Pessoa, tente novamente.');
        }

        // all good, return success
        $msg = $isEdit ? 'Pessoa atualizada com sucesso!' : 'Pessoa cadastrada com sucesso!';
        return new ApiResponse(false, $msg, [
            'People' => $People
        ]);
    }
    // ================
}
