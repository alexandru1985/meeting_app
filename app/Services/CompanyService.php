<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CompanyService
{
  public function getAllCompanies(
		int $perPage, 
		int $page
	): LengthAwarePaginator {
    return Company::paginate($perPage, ['*'], 'page', $page);
  }

  public function createCompany(array $data): Company
  {
    return Company::create([
      'name' => $data['name'],
    ]);
  }

  public function getCompanyById(int $id): Company
  {
    return Company::findOrFail($id);
  }

  public function updateCompany(array $data, Company $company): Company
  {
    $company->update([
      'name' => $data['name'],
    ]);

    return $company;
  }

  public function deleteCompany(Company $company): bool
  {
    return $company->delete();
  }
}
