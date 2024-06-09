<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerApiController extends Controller
{
    public function addCustomer(CustomerRequest $request) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            Customer::create($validation);
            DB::commit();
            
            return $this->responseJson(201, "Pelanggan Baru Berhasil Ditambah");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function getAllCustomer() {
        $customer = Customer::get();
        if ($customer->isNotEmpty()) {
            return $this->responseJson($customer, 200, "Berhasil Mengambil Daftar Pelanggan");
        }
        return $this->responseJson(404, "Tidak Ada Daftar Pelanggan");
    }

    public function editCustomer(CustomerRequest $request, $customerId) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            $doUpdate = Customer::whereId($customerId)->update($validation);
            DB::commit();

            return $this->responseJson(200, "Data Pelanggan Berhasil Diedit");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function deleteCustomer($customerId) { 
        $doDelete = Customer::whereId($customerId)->delete();
        return $this->responseJson(200, "Pelanggan Berhasil Dihapus");
    }
}
