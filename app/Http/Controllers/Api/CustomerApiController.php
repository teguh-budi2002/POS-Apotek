<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerApiController extends Controller
{
    private $query;

    public function __construct() {
        $this->query = Customer::select("id", "name", "contact_phone", "gender", "address")->newQuery();
    }

    public function addCustomer(CustomerRequest $request) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            $createCustomer = Customer::create($validation);
            $addedCustomer = $this->query->findOrFail($createCustomer->id);
            DB::commit();
            
            return $this->responseJson($addedCustomer, 201, "Pelanggan Baru Berhasil Ditambah");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function getAllCustomer() {
        $customer = $this->query->get();
        if ($customer->isNotEmpty()) {
            return $this->responseJson($customer, 200, "Berhasil Mengambil Daftar Pelanggan");
        }
        return $this->responseJson(404, "Tidak Ada Daftar Pelanggan");
    }

     public function getPaginateCustomers(Request $request) {
        $perPage = $request->get('rows', 10);
        $page = $request->get('page', 1);

        if ($request->has('search')) {
            $this->query->where('name', 'like', '%' . $request->search . '%');
        }

        $customers = $this->query->paginate($perPage, ['*'], 'page', $page);

        if ($customers->isNotEmpty()) {
            return $this->responseJson([
                'customers' => $customers, 
                'total' => $customers->total()
            ], 200, "Berhasil Mengambil Daftar Paginasi Customer");
        }

        return $this->responseJson(404, "Tidak Ada Daftar Paginasi Customer");
    }

    public function getTrashedCustomers() {
        $trashedCustomer = $this->query->onlyTrashed()->get();

        if ($trashedCustomer->isNotEmpty()) {
            return $this->responseJson($trashedCustomer, 200, "Berhasil Mengambil Daftar Customer (Trashed)");
        }

        return $this->responseJson(404, "Tidak Ada Daftar Customer (Trashed)");
    }

    public function restoreTrashedCustomer($customerId) {
        $customer = Customer::onlyTrashed()->findOrFail($customerId);
        $customer->restore();

        $restoredCustomer = $this->query->findOrFail($customerId);

        return $this->responseJson([
            'restoredCustomer' => $restoredCustomer
        ], 200, "Berhasil Restore Data Customer");
    }

    public function editCustomer(CustomerRequest $request, $customerId) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            $doUpdate = Customer::whereId($customerId)->update($validation);
            DB::commit();

            $newUpdatedData = $this->query->findOrFail($customerId);

            return $this->responseJson([
                'newUpdatedCustomer' => $newUpdatedData
            ],200, "Data Pelanggan Berhasil Diedit");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function deleteCustomer($customerId) { 
        DB::beginTransaction();
        try {
            Customer::whereId($customerId)->delete();
            DB::commit();
            $trashedCustomer = $this->query->onlyTrashed()->findOrFail($customerId);

            return $this->responseJson([
                'trashedCustomer' => $trashedCustomer
            ], 200, "Customer Berhasil Dihapus");
        } catch (\Throwable $th) {
            return $this->responseJson(500, $th->getMessage());
        }
    }
}
