<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormuleTarifRequest;
use App\Http\Requests\UpdateFormuleTarifRequest;
use App\Http\Resources\FormuleTarifResource;
use App\Services\FormuleTarifService;

class FormuleTarifController extends Controller
{
    public function __construct(private FormuleTarifService $formuleTarifService) {}

    public function index()
    {
        return FormuleTarifResource::collection($this->formuleTarifService->getAll());
    }

    public function store(StoreFormuleTarifRequest $request)
    {
        $formule = $this->formuleTarifService->create($request->validated());
        return new FormuleTarifResource($formule);
    }

    public function show(int $id)
    {
        return new FormuleTarifResource($this->formuleTarifService->find($id));
    }

    public function update(UpdateFormuleTarifRequest $request, int $id)
    {
        $formule = $this->formuleTarifService->update($id, $request->validated());
        return new FormuleTarifResource($formule);
    }

    public function destroy(int $id)
    {
        $this->formuleTarifService->delete($id);
        return response()->json(['message' => 'Formule tarifaire supprimée avec succès.']);
    }
}
