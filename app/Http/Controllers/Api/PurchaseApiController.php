<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseProductRequest;
use App\Models\Product;
use App\Models\PurchaseProduct;
use App\Models\StockProduct;
use App\StatusOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseApiController extends Controller
{
    public function purchasedProduct(PurchaseProductRequest $request) {
        DB::beginTransaction();
        try {
            $validation = $request->validated();

            $subTotal = array_sum($request->qty) * array_sum($request->unit_price);
            $grandTotal = 0;

            if ($request->hasAny(['discount', 'tax'])) {
                $grandTotal = $this->countGrandTotalValue($subTotal, $request->discount, $request->tax);
            }

            $createOrder = PurchaseProduct::create([
                'apotek_id' => $request->apotek_id,
                'supplier_id' => $request->supplier_id,
                'reference_number' => $request->reference_number,
                'sub_total' => $subTotal,
                'grand_total' => $grandTotal,
                'status_order' => StatusOrder::Pending,
                'payment_method' => $request->payment_method,
                'status_payment' => $request->status_payment,
                'order_date' => $request->order_date,
                'shipping_cost' => $request->shipping_cost,
                'shipping_details' => $request->shipping_details,
                'order_note' => $request->order_note,
                'action_by' => Auth::user()->name
            ]);

            $existingStockByOrderedProduct = StockProduct::whereIn('product_id', $request->product_id)
                                                          ->pluck('product_id')
                                                          ->toArray();
            $missingProduct = array_diff($request->product_id, $existingStockByOrderedProduct);

            // Check Product if doesn't have default stock
            if (!empty($missingProduct)) {
                $productMissingName = Product::whereIn('id', $missingProduct)->pluck('name')->toArray();
                $productMissingNameStr = implode(", ", $productMissingName);

                return $this->responseJson([
                        'missing_products' => $productMissingName,
                        'missing_product_id' => $missingProduct
                    ],
                    400, 
                    "Harap sesuaikan stock awal terlebih dahulu pada product: $productMissingNameStr, agar meminimalisir kesalahan dalam inventaris.");
            } else {
                $currentStock = StockProduct::select("id", "stock")->whereIn('product_id', $existingStockByOrderedProduct)->get();
                foreach ($currentStock as $key => $stock) {
                    $stock->stock += $request->qty[$key];
                    $stock->save();
                }
            }

            $combineProductReq = [];
            foreach ($request->product_id as $key => $productId) {
                $unitPrice = $request->unit_price[$key];
                $discountPercentage = isset($request->discount[$key]) ? $request->discount[$key] : 0;
    
                $priceAfterDiscount = $discountPercentage !== 0 ? $unitPrice - ($unitPrice * $discountPercentage / 100) : 0;

                $combineProductReq[$productId] = [
                    'qty' => $request->qty[$key],
                    'discount' => $discountPercentage,
                    'tax' => isset($request->tax[$key]) ? $request->tax[$key] : null,
                    'selling_price' => $unitPrice,
                    'price_after_discount' => $priceAfterDiscount,
                    'profit_margin' => $request->profit_margin[$key],
                    'expired_date_product' => $request->expired_date[$key]
                ];
            }
            // dd($combineProductReq);
            $createOrder->purchasedProducts()->attach($combineProductReq);

            DB::commit();
            return $this->responseJson(201, "Pembelian produk kepada supplier berhasil");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function getAllPurchasedProduct(){
        $purchasedProducts = PurchaseProduct::withWhereHas('purchasedProducts')->get();
        if ($purchasedProducts->isNotEmpty()) {
            return $this->responseJson($purchasedProducts, 200, "Berhasil mengambil daftar order produk");
        }

        return $this->responseJson(404, "Tidak Ada Daftar Order Produk");
    }

    protected function countGrandTotalValue($subTotal, $discount, $tax) {
        $countTax = 0;
        $countDisc = 0;

        if (!empty($discount) && !empty($tax)) {
            // Count Tax with Discount
            $countDisc = $subTotal * (array_sum($discount) / 100);
            $countTax = ($subTotal - $countDisc) * (array_sum($tax) / 100);
        } elseif (empty($discount) && !empty($tax)) {
            // Count Tax Without Discount
            $countTax = $subTotal * (array_sum($tax) / 100);
        } elseif (!empty($discount) && empty($tax)) {
            // Only Count DIscount
            $countDisc = $subTotal * (array_sum($discount) / 100);
        }

        return ($subTotal - $countDisc) + $countTax;
    }
}
