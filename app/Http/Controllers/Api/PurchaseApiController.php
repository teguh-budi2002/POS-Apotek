<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseProductRequest;
use App\Models\OrderedPurchaseProduct;
use App\Models\Payment;
use App\Models\Product;
use App\Models\PurchaseProduct;
use App\Models\StockProduct;
use App\PaymentType;
use App\StatusOrder;
use App\StatusPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseApiController extends Controller
{
    private $getRelation;

    public function __construct() {
        $this->getRelation = [
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
            'payment' => function($query) {
                $query->select(
                    'payments.id', 
                    'payments.purchase_product_id', 
                    'payments.cash_paid', 
                    'payments.payment_method', 
                    'payments.status_payment', 
                    'payments.paid_on', 
                    'payments.payment_notes',
                    'is_the_nominal_cost_more_or_less',
                    'nominal_cost_more_or_less',
                    'proof_of_payment'
                );
            },
            'apotek' => function($query) {
                $query->select('apoteks.id', 'apoteks.name_of_apotek', 'apoteks.address', 'apoteks.contact_phone');
            },
            'supplier' => function($query) {
                $query->select('suppliers.id', 'suppliers.supplier_name', 'suppliers.address', 'suppliers.contact_phone', 'suppliers.email');
            }
        ];
    }
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
            $terminDate = self::formattedTerminDate($request->termin_payment, $request->format_termin_date, $request->order_date);
            $paymentPath = "/public/purchase-product/proof-of-payment";
            $invoicePath = "/public/purchase-product/invoice";
            $proofOfPayment = $request->file('proof_of_payment') ? $this->storeImage($request->file('proof_of_payment'), $paymentPath) : null;
            $purchaseInvoice = $request->file('purchase_invoice') ? $this->storeImage($request->file('purchase_invoice'), $invoicePath) : null;
            $paymentDetails = self::calculatePaymentStatus($request->cash_paid, $request->grand_total);
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
                'order_date' => $request->order_date,
                'shipping_cost' => $request->shipping_cost,
                'shipping_details' => $request->shipping_details,
                'order_note' => $request->order_note,
                'action_by' => Auth::user()->name,
                'termin_payment' => $request->termin_payment,
                'format_termin_date' => $request->format_termin_date,
                'termin_date' => $terminDate,
                'purchase_invoice' => $purchaseInvoice
            ]);

            $createOrder->payment()->create([
                'cash_paid' => $request->cash_paid,
                'payment_method' => $request->payment_method,
                'status_payment' => $paymentDetails['statusPayment'],
                'paid_on' => $request->paid_on,
                'is_the_nominal_cost_more_or_less' => $paymentDetails['isCostMoreOrLess'],
                'nominal_cost_more_or_less' => $paymentDetails['nominalCostMoreOrLess'],
                'payment_notes' => $request->payment_notes,
                'payment_type' => PaymentType::ORDER_PRODUCT,
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
                    'batch_number' => $request->productData[$productId]['batch_number'],
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

    public function editPurchasedProduct(PurchaseProductRequest $request, $purchaseProductId) {
         DB::beginTransaction();
        try {
            $validation = $request->validated();
            $purchasedProducts = PurchaseProduct::findOrFail($purchaseProductId);
            $newGrandTotal = $request->grand_total;
            $existingStockByOrderedProduct = StockProduct::whereIn('product_id', $request->product_id)
                                                          ->pluck('product_id')
                                                          ->toArray();
                                                          
            if ($purchasedProducts->grand_total !== $newGrandTotal) {
                $payment = Payment::select("id", "nominal_cost_more_or_less")->findOrFail($purchaseProductId);
                $payment->nominal_cost_more_or_less = $newGrandTotal;
                $payment->save();
                
                // Count stock if [$oldQty !== $newQty]
                $currentStock = StockProduct::select("id", "stock", "product_id")->whereIn('product_id', $existingStockByOrderedProduct)->get();
                foreach ($currentStock as $stock) {
                    $oldQty = $request->productData[$stock->product_id]['oldQty'];
                    $newQty = $request->productData[$stock->product_id]['newQty'];
                    if ($oldQty !== $newQty) {
                        $stock->stock = $stock->stock - $oldQty + $newQty;
                        $stock->save();
                    } else {
                        break;
                    }
                }
            }
           
            $purchasedProducts->update([
                'apotek_id' => $request->apotek_id,
                'supplier_id' => $request->supplier_id,
                'reference_number' => $request->reference_number,
                'grand_total' => $newGrandTotal,
                'status_order' => $request->status_order,
                'order_date' => $request->order_date,
                'shipping_cost' => $request->shipping_cost,
                'shipping_details' => $request->shipping_details,
                'order_note' => $request->order_note,
                'updated_by' => Auth::user()->name,
            ]);

            $combineRequestProductPurchased = [];
            foreach ($request->product_id as $productId) {
                $combineRequestProductPurchased[$productId] = [
                    'qty' => $request->productData[$productId]['newQty'],
                    'discount' => $request->productData[$productId]['discount'],
                    'tax' => $request->productData[$productId]['tax'],
                    'selling_price' => $request->productData[$productId]['selling_price'],
                    'sub_total' => $request->productData[$productId]['total_price'],
                    'price_after_discount' => $request->productData[$productId]['price_after_discount'],
                    'profit_margin' => $request->productData[$productId]['profit_margin'],
                    'batch_number' => $request->productData[$productId]['batch_number'],
                    'expired_date_product' => Carbon::parse($request->productData[$productId]['expired_date_product'])->format("Y-m-d")
                ];
            }

            $purchasedProducts->purchasedProducts()->sync($combineRequestProductPurchased);

            DB::commit();
            return $this->responseJson(201, "Order produk berhasil diubah");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function getRefNumbers() {
        $referenceNumbers = PurchaseProduct::select('reference_number')->get();
        return $referenceNumbers->isNotEmpty()
            ? $this->responseJson($referenceNumbers, 200, "Berhasil mengambil daftar nomor referensi")
            : $this->responseJson(404, "Tidak Ada Daftar Nomor Referensi");
    }

    public function getListOrderedProductForReturn(Request $request){
        $reference_number = $request->reference_number;

        if (!empty($reference_number)) {
            $detailProductWantToReturn = PurchaseProduct::with(['orderedProductDetailsForReturn' => function($query) {
                $query->select('products.id AS product_id', 'product_code', 'name', 'img_product'); 
            }])->select('id', 'grand_total', 'reference_number')->whereIn('reference_number', $reference_number)->get();

            return $detailProductWantToReturn->isNotEmpty()
                ? $this->responseJson($detailProductWantToReturn, 200, "Berhasil mengambil daftar order produk by (Filter Reference Number)")
                : $this->responseJson(404, "Tidak Ada Daftar Order Produk by (Filter Reference Number)");
        } else {
            return $this->responseJson(400, "Error, Cant Found The Detail");
        }
    }

    /**
     * Retrieves a paginated list of purchased products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaginatePurchasedProduct(Request $request){
        $perPage = 10;
        $purchasedProducts = PurchaseProduct::with($this->getRelation);
        
        if ($request->has('filters')) {
            $purchasedProducts->filterOrderedProducts($request->filters);
        }
       
        $purchasedProducts = $purchasedProducts->cursorPaginate($perPage);

        return $purchasedProducts->isNotEmpty()
            ? $this->responseJson(['purchasedProducts' => $purchasedProducts], 200, "Berhasil mengambil daftar order produk")
            : $this->responseJson(404, "Tidak Ada Daftar Order Produk");
    }

    /**
     * Update the payment status of a purchase order and store the proof of payment.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object.
     * @param int $purchaseProductOrderId The ID of the purchase product order.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the updated data order.
     *
     * @throws \Throwable If an error occurs during the update process.
     */
    public function paidOrder(Request $request, $purchaseProductOrderId){
        DB::beginTransaction();
        try {
            $file  = $request->file('proof_of_payment');
            $path = "/public/purchase-product/proof-of-payment";
            $proofOfPayment = $this->storeImage($file, $path);
            $currentPayment = Payment::where('purchase_product_id', $purchaseProductOrderId)->latest()->firstOrFail();
            
            if (!empty($currentPayment)) {
                $costComparisonType = $currentPayment->is_the_nominal_cost_more_or_less;
                $previousCostDifference = $currentPayment->nominal_cost_more_or_less;
                $currentCashPaid = $currentPayment->cash_paid;
                $cashPaidRequest = (int) $request->cash_paid;

                $adjustedCashPaid = $cashPaidRequest + $currentCashPaid;
                $newCostDifference = abs($cashPaidRequest - $previousCostDifference);

                // Do update when "LUNAS" (cash_paid === nominal_cost_more_or_less)
                if ($costComparisonType == 'less' && $cashPaidRequest === $previousCostDifference) {
                    $currentPayment->cash_paid = $adjustedCashPaid;
                    $currentPayment->nominal_cost_more_or_less = $newCostDifference;
                    $currentPayment->status_payment = StatusPayment::Paid;
                    $currentPayment->paid_on = $request->paid_date;
                    $currentPayment->proof_of_payment = $proofOfPayment;
                    $currentPayment->payment_notes = $request->paid_notes;
                    $currentPayment->save();
                    DB::commit();
                }               
            }

            $updatedDataOrder = PurchaseProduct::with($this->getRelation)
                                                ->whereId($purchaseProductOrderId)
                                                ->first();
            
            return $this->responseJson(['updatedDataOrder' => $updatedDataOrder], 200, "Pembayaran order produk berhasil");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    public function changeStatusOrder(Request $request, $purchaseProductOrderId) {
        DB::beginTransaction();
        try {        
            $purchaseProductOrder = PurchaseProduct::findOrFail($purchaseProductOrderId);
            $purchaseProductOrder->status_order = $request->status_order;
            $purchaseProductOrder->save();
            DB::commit();

            $updatedDataOrder = PurchaseProduct::with($this->getRelation)
                                                ->whereId($purchaseProductOrderId)
                                                ->first();
    
            return $this->responseJson(['updatedDataOrder' => $updatedDataOrder], 200, "Status order produk berhasil diubah");
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseJson(500, $th->getMessage());
        }
    }

    /**
     * Store the proof of payment image.
     *
     * @param  \Illuminate\Http\UploadedFile|null  $file
     * @param  string  $path
     * @return string
     */
    private static function storeImage($file, $path){
        if ($file) {
            $fileName = $file->getClientOriginalName();
            Storage::putFileAs($path, $file, $fileName);
            
            return $fileName;
        } else {
            return null;
        }
    }

    private static function calculatePaymentStatus($cashPaid, $grandTotal) {
        $statusPayment = '';
        $isCostMoreOrLess = '';
        $nominalCostMoreOrLess = 0;

        if ($cashPaid < $grandTotal) {
            $statusPayment = StatusPayment::Due;
            $isCostMoreOrLess = 'less';
            $nominalCostMoreOrLess = $grandTotal - $cashPaid;
        } else if ($cashPaid == $grandTotal || $cashPaid > $grandTotal) {
            $statusPayment = StatusPayment::Paid;
            $isCostMoreOrLess = 'more';
            $nominalCostMoreOrLess = $cashPaid - $grandTotal;
        }

        return [
            'statusPayment' => $statusPayment,
            'isCostMoreOrLess' => $isCostMoreOrLess,
            'nominalCostMoreOrLess' => $nominalCostMoreOrLess,
        ];
    }

    private static function formattedTerminDate($terminPayment, $formatDate, $orderDate) {
        $orderDate = Carbon::parse($orderDate);
        $termin = (int) $terminPayment;

        if ($formatDate == 'Day') {
            $orderDate->addDays($termin);
        } elseif ($formatDate == 'Month') {
            $orderDate->addMonths($termin);
        }

        $terminDate = $orderDate->format('Y-m-d');

        return $terminDate;
    }
}
