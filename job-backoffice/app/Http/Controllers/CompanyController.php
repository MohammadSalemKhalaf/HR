<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\JobApplication;
use App\Http\Requests\CompanyCreateRequest;
use App\Http\Requests\CompanyUpdateRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class CompanyController extends Controller
{
public function index(Request $request)
{
    $query = Company::query();

    if ($request->archived == 'true') {
        $query = $query->onlyTrashed();
    } else {
        $query = $query->latest();
    }

    $companies = $query->paginate(10)->withQueryString();

    return view('company.index', compact('companies'));
}

 public function create()
{
    $users = User::select('id', 'name')->get();

    $industries = ['Technology', 'Finance', 'Healthcare', 'Education', 'Manufacturing', 'Retail', 'Other'];
     return view(view: 'company.create', data: compact('industries', 'users'));

}


public function show(?string $id = null)
{
    if ($id) {
        $company = Company::findOrFail($id);
    } else {
        $company = Company::where('ownerId', Auth::id())->firstOrFail();
    }

    return view('company.show', compact('company'));
}

public function store(CompanyCreateRequest $request)
{

        // create or reuse user by provided owner email/password
        $ownerEmail = $request->input('email');
        $ownerPassword = $request->input('password');
        $ownerName = $request->input('owner_name', $ownerEmail);

        $user = User::firstOrCreate(
            ['email' => $ownerEmail],
            [
                'name' => $ownerName,
                'password' => Hash::make($ownerPassword),
                'role' => 'company',
                'email_verified_at' => now(),
            ]
        );

        Company::create([
            'name' => $request->name,
            'industry' => $request->industry,
            'address' => $request->address,
            'website' => $request->website,
            'ownerId' => $user->id,
        ]);

    return redirect()
        ->route('companies.index')
        ->with('success', 'Company created successfully!');
}

   public function edit(?string $id = null)
{
    if ($id) {
        // admin
        $company = Company::findOrFail($id);
    } else {
        // company owner
        $company = Company::where('ownerId', Auth::user()->id)->firstOrFail();
    }

    $owners = User::all();

    return view('company.edit', compact('company', 'owners'));
}

public function update(CompanyUpdateRequest $request, ?string $id = null)
{
    $company = $id
        ? Company::findOrFail($id) // admin
        : Auth::user()->company; // owner

    $company->update($request->validated());

    if (Auth::user()->role === 'admin') {
        return redirect()
            ->route('companies.show', $company->id)
            ->with('success', 'Company updated successfully');
    }

    return redirect()
        ->route('my-company.show')
        ->with('success', 'Company updated successfully');
}

    public function destroy(string $id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        return redirect()
            ->route('companies.index')
            ->with('success', 'Company archived successfully');
    }

    public function restore(string $id)
    {
        $company = Company::withTrashed()->findOrFail($id);
        $company->restore();

        return redirect()
            ->route('companies.index')
            ->with('success', 'Company restored successfully');
    }
}