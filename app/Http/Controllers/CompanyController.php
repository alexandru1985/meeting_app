<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\Company;
use Illuminate\Http\Response;
use App\Http\Requests\CompanyRequest;
use Illuminate\Http\Request;
use App\Services\CompanyService;

class CompanyController extends Controller
{
	public function __construct(
		private CompanyService $companyService
	) {
	}

	public function index(Request $request): JsonResponse
	{
		$perPage = 10;  
		$page = $request->get('page', 1);  
		$companies = $this->companyService
										->getAllCompanies($perPage, $page);

		return response()->json($companies, Response::HTTP_OK);
	}

	public function store(CompanyRequest $request): JsonResponse
	{
		$validated = $request->validated();
		$company = $this->companyService->createCompany($validated);

		return response()->json($company, Response::HTTP_CREATED);
	}

	public function show(Company $company): JsonResponse
	{
		$company = $this->companyService->getCompanyById($company->id);
		
		return response()->json($company, Response::HTTP_OK);
	}

	public function update(
		CompanyRequest $request, 
		Company $company
	): JsonResponse {
		$validated = $request->validated();
		$updatedCompany = $this->companyService
												->updateCompany($validated, $company);

		return response()->json($updatedCompany, Response::HTTP_OK);
	}

	public function destroy(Company $company): JsonResponse
	{
		$this->companyService->deleteCompany($company);

		return response()->json(null, Response::HTTP_NO_CONTENT);
	}
}
