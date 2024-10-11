<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Constants;
use App\Models\Attendance as mAttendance;
use App\Helpers\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

class Attendance extends Controller
{
    public function index()
    {
        return view('site-attendance', [
            'PAGE_TITLE' => 'Presença',
            'DASH_PAGE_TITLE' => 'Presença',
        ]);
    }

    public function view(string $timestamp)
    {
        $title = 'Visualizar Presença';
        return view('site-attendance-add', [
            'PAGE_TITLE' => $title,
            'DASH_PAGE_TITLE' => $title,
            'TYPE' => Constants::FORM_VIEW,
            'ACTION' => '',
            'MEETING_DATE' => date('Y-m-d', $timestamp),
        ]);
    }

    public function add()
    {
        $title = 'Adicionar Presença';
        return view('site-attendance-add', [
            'PAGE_TITLE' => $title,
            'DASH_PAGE_TITLE' => $title,
            'TYPE' => Constants::FORM_ADD,
            'ACTION' => route('attendance.doAdd'),
        ]);
    }

    public function doAdd(Request $request)
    {
        return $this->saveRequest($request, 'attendance.add');
    }

    public function edit(string $timestamp)
    {
        $title = 'Editar Presença';
        return view('site-attendance-add', [
            'PAGE_TITLE' => $title,
            'DASH_PAGE_TITLE' => $title,
            'TYPE' => Constants::FORM_EDIT,
            'ACTION' => route('attendance.doEdit'),
            'MEETING_DATE' => date('Y-m-d', $timestamp),
        ]);
    }

    public function doEdit(Request $request)
    {
        return $this->saveRequest($request, 'attendance.edit');
    }

    public function ajaxFilterTable(Request $request)
    {
        // vars
        $date1 = $request->input('f-date1');
        $date2 = $request->input('f-date2');

        // convert both from d/m/Y to timestamps
        $dt1 = DateTime::createFromFormat('d/m/Y', trim($date1));
        $dt2 = DateTime::createFromFormat('d/m/Y', trim($date2));

        // get view
        $view = view('site-attendance-table', [
            'DATE_TIME1' => $dt1->getTimestamp(),
            'DATE_TIME2' => $dt2->getTimestamp(),
        ]);

        return $this->returnResponse(
            false,
            'HTML retornado com sucesso!',
            [
                'html' => $view->render()
            ],
            Response::HTTP_OK
        );
    }

    private function saveRequest(Request $request, string $errorRouteName): \Illuminate\Http\RedirectResponse
    {
        $response = mAttendance::fSave($request);
        if ($response->isError()) {
            return redirect()->route($errorRouteName)
                ->withInput()
                ->withErrors(['msg' => ApiResponse::getValidateMessage($response)]);
        }

        $meetingDate = $response->getValueFromResponse('meetingDate');
        return redirect()
            ->route('attendance.edit', [
                'timestamp' => strtotime($meetingDate),
            ])
            ->withSuccess($response->getMessage());
    }
}
