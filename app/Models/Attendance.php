<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\ModelValidation;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\People;
use Illuminate\Support\Facades\DB;
use DateTime;

class Attendance extends Model
{
    use HasFactory;
    use \App\Traits\BaseModelTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    protected $attributes = [];

    protected $appends = [
        'codedId',
    ];

    public const RESPONSABILITY_AV = 'AV';
    public const RESPONSABILITY_AV_AUX = 'AV_AUX';
    public const RESPONSABILITY_ZOOM = 'ZOOM';
    public const RESPONSABILITY_ZOOM_AUX = 'ZOOM_AUX';
    public const RESPONSABILITY_MICRO1 = 'MICRO1';
    public const RESPONSABILITY_MICRO2 = 'MICRO2';
    public const RESPONSABILITIES = [
        self::RESPONSABILITY_AV => 'AV',
        self::RESPONSABILITY_AV_AUX => 'AV Aux',
        self::RESPONSABILITY_ZOOM => 'Zoom',
        self::RESPONSABILITY_ZOOM_AUX => 'Zoom Aux',
        self::RESPONSABILITY_MICRO1 => 'Micro 1',
        self::RESPONSABILITY_MICRO2 => 'Micro 2',
    ];

    public const STATUS_PRESENT = 'PRESENT';
    public const STATUS_ABSENT = 'ABSENT';
    public const STATUS_LATE = 'LATE';
    public const STATUSES = [
        self::STATUS_PRESENT => 'Presente',
        self::STATUS_ABSENT => 'Ausente',
        self::STATUS_LATE => 'Atrasado',
    ];

    // relations
    public function people()
    {
        return $this->hasOne(
            People::class, 'id',
            'people_id'
        );
    }
    // =========

    // class functions
    /**
     * https://laravel.com/docs/8.x/validation#available-validation-rules
     */
    public function validateModel(): ApiResponse
    {
        $validation = new ModelValidation($this->toArray());
        $validation->addIdField(self::class, 'Presença', 'id', 'ID');
        $validation->addField('meeting_date', ['required', 'date', 'date_format:Y-m-d'], 'Data Reunião');
        $validation->addField('responsability', ['required', function ($attribute, $value, $fail) {
            if (!in_array($value, array_keys(self::RESPONSABILITIES))) {
                $fail('O tipo da função não é válido');
            }
        }], 'Função');
        $validation->addIdField(People::class, 'Pessoa', 'people_id', 'People', ['nullable']);
        $validation->addField('status', ['nullable', function ($attribute, $value, $fail) {
            if (null !== $value && !in_array($value, array_keys(self::STATUSES))) {
                $fail('O status não é válido');
            }
        }], 'Status');

        return $validation->validate();
    }
    // ===============

    // static functions
    public static function fSave(Request $request): ApiResponse
    {
        DB::beginTransaction();

        // get meeting date for insert or update
        $hdnMeetingDate = $request->input('f-mt');
        $meetingDate = $request->input("f-meeting-date");
        $isEdit = !empty($hdnMeetingDate);
        if (!$hdnMeetingDate && !$meetingDate) {
            return new ApiResponse(true, 'Data não encontrada para continuar a ação!');
        }

        foreach (array_keys(self::RESPONSABILITIES) as $responsability) {
            $peopleId = $request->input("f-people-$responsability");
            $status = $request->input("f-status-$responsability");

            // get attendance
            $Attendance = self::where('meeting_date', $hdnMeetingDate)
                ->where('responsability', $responsability)
                ->first();

            // create or update
            if ($Attendance) {
                $Attendance->people_id = $peopleId;
                $Attendance->status = $status;
                $Attendance->save();
            } else {
                $date = DateTime::createFromFormat('d/m/Y', trim($meetingDate));

                $Attendance = new self();
                $Attendance->meeting_date = $date->format('Y-m-d');
                $Attendance->responsability = $responsability;
                $Attendance->people_id = $peopleId;
                $Attendance->status = $status;
                $validation = $Attendance->validateModel();
                if ($validation->isError()) {
                    DB::rollBack();
                    return $validation;
                }

                // save model
                try {
                    $Attendance->save();
                } catch (\Exception $e) {
                    return new ApiResponse(true, 'Ocorreu um problema ao salvar as Presenças, tente novamente.');
                }
            }
        }

        // all good, return success
        DB::commit();
        $msg = $isEdit ? 'Presença atualizada com sucesso!' : 'Presença cadastrada com sucesso!';
        return new ApiResponse(false, $msg, [
            'meetingDate' => $Attendance->meeting_date
        ]);
    }
    // ================
}
