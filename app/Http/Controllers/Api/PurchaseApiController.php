<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseProductRequest;
use App\Models\Product;
use App\Models\PurchaseProduct;
use App\Models\StockProduct;
use App\StatusOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseApiController extends Controller
{
    /**
     * Purchase a product.
     *
     * @param PurchaseProductRequest $request The request object.
     * @return JsonResponse The JSON response.
     */
    public function purchasedProduct(PurchaseProductRequest $request) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();
            $proofOfPayment = "";

            if ($request->file('proof_of_payment')) {
                $file  = $request->file('proof_of_payment');
                $proofOfPayment = $file->getClientOriginalName();
                $storeImage = Storage::putFileAs('/public/purchase-product/proof_of_payment', $file, $proofOfPayment);
            }

            $existingStockByOrderedProduct = StockProduct::whereIn('product_id', $request->product_id)
                                                          ->pluck('product_id')
                                                          ->toArray();
            $missingProduct = array_diff($request->product_id, $existingStockByOrderedProduct);

            // Check Product if doesn't have default stock
            if (!empty($missingProduct)) {
                $productMissingName = Product::select("id", "name")->whereIn('id', $missingProduct)->pluck('name')->toArray();
                $productMissingNameStr = implode(", ", $productMissingName);

                return $this->responseJson([
                    'isProductDoesntHaveDefaultStock' => true,
                    'message' => "Harap sesuaikan stock awal pada {$productMissingNameStr}. untuk menghindari kesalahan dalam inventaris."
                ], 400, "Produk Tidak Memiliki Stock Awal (Default Stock)");
            }

            $createOrder = PurchaseProduct::create([
                'apotek_id' => $request->apotek_id,
                'supplier_id' => $request->supplier_id,
                'reference_number' => $request->reference_number,
                'grand_total' => $request->grand_total,
                'status_order' => $request->status_order,
                'payment_method' => $request->payment_method,
                'status_payment' => $request->status_payment,
                'cash_paid' => $request->cash_paid,
                'paid_on' => $request->paid_on,
                'order_date' => $request->order_date,
                'shipping_cost' => $request->shipping_cost,
                'shipping_details' => $request->shipping_details,
                'order_note' => $request->order_note,
                'action_by' => Auth::user()->name,
                'termin_payment' => $request->termin_payment,
                'format_termin_date' => $request->format_termin_date,
                'proof_of_payment' => $proofOfPayment
            ]);

            $currentStock = StockProduct::select("id", "stock", "product_id")->whereIn('product_id', $existingStockByOrderedProduct)->get();
            foreach ($currentStock as $stock) {
                $stock->stock += $request->productData[$stock->product_id]['qty'];
                $stock->save();
            }

            $combineRequestProductPurchased = [];
            foreach ($request->product_id as $productId) {
                $combineRequestProductPurchased[$productId] = [
                    'qty' => $request->productData[$productId]['qty'],
                    'discount' => $request->productData[$productId]['discount'],
                    'tax' => $request->productData[$productId]['tax'],
                    'selling_price' => $request->productData[$productId]['selling_price'],
                    'sub_total' => $request->productData[$productId]['total_price'],
                    'price_after_discount' => $request->productData[$productId]['price_after_discount'],
                    'profit_margin' => $request->productData[$productId]['profit_margin'],
                    'expired_date_product' => Carbon::parse($request->productData[$productId]['expired_date_product'])->format("Y-m-d")
                ];
            }
            
            $createOrder->purchasedProducts()->attach($combineRequestProductPurchased);

            DB::commit();
            return $this->responseJson(201, "Pembelian produk kepada supplier berhasil");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    /**
     * Retrieve all purchased products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllPurchasedProduct(){
        $purchasedProducts = PurchaseProduct::withWhereHas('purchasedProducts')->get();

        $purchasedProducts->isNotEmpty()
            ? $this->responseJson($purchasedProducts, 200, "Berhasil mengambil daftar order produk")
            : $this->responseJson(404, "Tidak Ada Daftar Order Produk");
    }

    public function getPaginatePurchasedProduct(Request $request){
        $perPage = $request->get('rows', 10);
        $purchasedProducts = PurchaseProduct::with([
            'purchasedProducts' => function($query) {
                $query->join('unit_products', 'products.unit_product_id', 'unit_products.id')
                ->select(
                    'products.id',
                    'products.name',
                    'products.product_code',
                    'products.img_product',
                    'products.unit_price',
                    'unit_products.id as unit_product_id',
                    'unit_products.name AS unit_product_name'
                );
            },
            'apotek' => function($query) {
                $query->select('apoteks.id', 'apoteks.name_of_apotek', 'apoteks.address', 'apoteks.contact_phone');
            },
            'supplier' => function($query) {
                $query->select('suppliers.id', 'suppliers.supplier_name', 'suppliers.address', 'suppliers.contact_phone');
            }
        ])->cursorPaginate($perPage);

        return $purchasedProducts->isNotEmpty()
            ? $this->responseJson(['purchasedProducts' => $purchasedProducts], 200, "Berhasil mengambil daftar order produk")
            : $this->responseJson(404, "Tidak Ada Daftar Order Produk");
    }
}
