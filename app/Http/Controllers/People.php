<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Constants;
use App\Models\People as mPeople;
use App\Helpers\ApiResponse;

class People extends Controller
{
    public function index()
    {
        return view('site-people', [
            'PAGE_TITLE' => 'Pessoas',
            'DASH_PAGE_TITLE' => 'Pessoas',
        ]);
    }

    public function view(string $codedId)
    {
        $People = $this->getPeopleOrRedirectIndex($codedId);
        $title = 'Visualizar Pessoa';

        return view('site-people-add', [
            'PAGE_TITLE' => $title,
            'DASH_PAGE_TITLE' => $title,
            'TYPE' => Constants::FORM_VIEW,
            'ACTION' => '',
            'PEOPLE' => $People,
        ]);
    }

    public function add()
    {
        $title = 'Adicionar Pessoa';
        return view('site-people-add', [
            'PAGE_TITLE' => $title,
            'DASH_PAGE_TITLE' => $title,
            'TYPE' => Constants::FORM_ADD,
            'ACTION' => route('people.doAdd'),
        ]);
    }

    public function doAdd(Request $request)
    {
        $response = mPeople::fSave($request);
        if ($response->isError()) {
            return redirect()->route('people.add')
                ->withInput()
                ->withErrors(['msg' => ApiResponse::getValidateMessage($response)]);
        }

        $People = $response->getValueFromResponse('People');
        return redirect()
            ->route('people.edit', [
                'codedId' => $People?->coded_id ?? ''
            ])
            ->withSuccess($response->getMessage());
    }

    public function edit(string $codedId)
    {
        $People = $this->getPeopleOrRedirectIndex($codedId);
        $title = 'Editar Pessoa';

        return view('site-people-add', [
            'PAGE_TITLE' => $title,
            'DASH_PAGE_TITLE' => $title,
            'TYPE' => Constants::FORM_EDIT,
            'ACTION' => route('people.doEdit'),
            'PEOPLE' => $People,
        ]);
    }

    private function getPeopleOrRedirectIndex(string $codedId): mPeople
    {
        /** @var ?mPeople $People */
        $People = mPeople::getModelByCodedId($codedId);
        if (!$People) {
            return $this->redirectWithError(
                'people.index',
                'Pessoa não encontrada',
            );
        }

        return $People;
    }

    public function doEdit(Request $request)
    {
        $response = mPeople::fSave($request);
        if ($response->isError()) {
            return redirect()->route('people.edit', ['codedId' => $request->input('f-pid')])
                ->withInput()
                ->withErrors(['msg' => ApiResponse::getValidateMessage($response)]);
        }

        $People = $response->getValueFromResponse('People');
        return redirect()
            ->route('people.edit', [
                'codedId' => $People?->coded_id ?? ''
            ])
            ->withSuccess($response->getMessage());
    }
}
