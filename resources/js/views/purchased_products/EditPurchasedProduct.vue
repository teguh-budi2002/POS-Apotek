<template>
  <Content>
    <template v-slot:content>
      <Toast />
      <div>
        <Breadcrumb :home="breadcrumbIcon" :model="breadcrumbItems" class="!bg-transparent"/>
      </div>
      <div class="bg-white rounded-md shadow-md border-l-8 border-[#022f45] w-full card p-4 mt-3">
        <div class="grid grid-cols-3 gap-8">
          <div>
            <label for="supplier" class="mb-2 block font-semibold text-slate-500">Pemasok / Supplier</label>
            <div v-if="errors.supplier_id">
              <p class="text-xs text-rose-500 mb-1">{{ errors.supplier_id }}</p>
            </div>
            <InputGroup>
              <Select 
                class="w-full"
                placeholder="Pilih pemasok / supplier"
                v-model="supplier_id"
                v-bind="supplierIdAttr"
                :options="suppliers"
                optionLabel="supplier_name"
                optionValue="id"
                id="supplier_id"
                @change="handleDisplayAddressSupplier"
              />
              <Button 
                icon="pi pi-plus-circle"
                size="medium"
                outlined 
                plain
                class="!text-blue-500"
              />
            </InputGroup>
            <div class="mt-2">
              <p class="font-semibold text-slate-500">Alamat Lengkap:</p>
              <p class="text-sm text-slate-400" v-if="addressSupplier !== null">{{ addressSupplier }}</p>
            </div>
          </div>
          <div>
            <div>
              <div class="flex items-center space-x-2 mb-2">
                <label for="ref_number" class="font-semibold text-slate-500">Nomor Referensi</label>
                <i class="pi pi-question-circle bg-blue-300 text-white rounded-full cursor-pointer" v-tooltip.top="'Nomor invoice pembelian dari Supplier'" style="font-size: 1.2rem"></i>
              </div>
              <div v-if="errors.reference_number">
                <p class="text-xs text-rose-500 mb-1">{{ errors.reference_number }}</p>
              </div>
              <InputGroup>
                <InputGroupAddon>REF-</InputGroupAddon>
                <InputText
                  class="w-full"
                  placeholder="(Opsional)"
                  id="reference_number"
                  v-model="reference_number"
                  v-bind="referenceNumberAttr"
                />
              </InputGroup>
            </div>
            <div class="mt-2">
              <label for="apotek" class="mb-2 block font-semibold text-slate-500">Lokasi Apotek</label>
              <div v-if="errors.apotek_id">
                <p class="text-xs text-rose-500 mb-1">{{ errors.apotek_id }}</p>
              </div>
              <Select 
                class="w-full"
                placeholder="Pilih apotek"
                size="small"
                v-model="apotek_id"
                v-bind="apotekIdAttr"
                :options="apoteks"
                optionLabel="name_of_apotek"
                optionValue="id"
              />
            </div>
          </div>
          <div>
            <div>
              <label for="status_order" class="mb-2 block font-semibold text-slate-500">Status Order</label>
              <div v-if="errors.status_order">
                <p class="text-xs text-rose-500 mb-1">{{ errors.status_order }}</p>
              </div>
              <Select 
                class="w-full"
                placeholder="Pilih status pembelian"
                :options="[{name: 'Pending'}, {name: 'Shipping'}, {name: 'Delivered'}, {name: 'Cancelled'}]"
                optionLabel="name"
                optionValue="name"
                v-model="status_order"
                v-bind="statusOrderAttr" 
              />
            </div>
            <div class="mt-2">
              <label for="order_date" class="mb-2 block font-semibold text-slate-500">Tanggal Pembelian</label>
              <div v-if="errors.order_date">
                <p class="text-xs text-rose-500 mb-1">{{ errors.order_date }}</p>
              </div>
              <DatePicker 
                class="w-full"
                placeholder="Tentukan tanggal pembelian"
                showIcon 
                showButtonBar
                fluid
                dateFormat="dd/mm/yy"
                showTime 
                hourFormat="24"
                v-model="order_date"
                v-bind="orderDateAttr" 
              />
            </div>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-md shadow-md border-l-8 border-[#022f45] w-full card p-4 mt-5">
        <div class="alert_payment_is_paid flex justify-center" v-if="isOrderPaid()">
          <div class="w-2/4 bg-green-500 rounded-md p-2 shadow-md">
            <p class="text-white uppercase text-center font-semibold">Pembayaran Lunas</p>
          </div>
        </div>
        <div class="selected_product mt-5">
          <div class="max-w-full max-h-56 overflow-x-auto custom-scrollbar mx-auto">
            <table class="w-full table-auto">
              <thead class="bg-slate-200">
                <tr>
                  <th class="p-2">
                    <p class="text-slate-700 font-semibold text-sm whitespace-nowrap">Nama Produk</p>
                  </th>
                  <th class="p-2">
                    <p class="text-slate-700 font-semibold text-sm whitespace-nowrap">Qty Pembelian</p>
                  </th>
                  <th class="p-2">
                    <p class="text-slate-700 font-semibold text-sm whitespace-nowrap">Harga Modal Satuan (Sebelum Diskon)</p>
                  </th>
                  <th class="p-2">
                    <p class="text-slate-700 font-semibold text-sm whitespace-nowrap">Pajak</p>
                  </th>
                  <th class="p-2">
                    <p class="text-slate-700 font-semibold text-sm whitespace-nowrap">Diskon (%)</p>
                  </th>
                  <th class="p-2">
                    <p class="text-slate-700 font-semibold text-sm text-center whitespace-nowrap">Harga Modal Satuan (Setelah Diskon)</p>
                  </th>
                  <th class="p-2">
                    <p class="text-slate-700 font-semibold text-sm whitespace-nowrap">Total Harga</p>
                  </th>
                  <th class="p-2">
                    <p class="text-slate-700 font-semibold text-sm whitespace-nowrap">Profit Margin</p>
                  </th>
                  <th class="p-2">
                    <p class="text-slate-700 font-semibold text-sm whitespace-nowrap">Harga Jual Satuan</p>
                  </th>
                  <th class="p-2">
                    <p class="text-slate-700 font-semibold text-sm whitespace-nowrap">Nomor Batch</p>
                  </th>
                  <th class="p-2">
                    <p class="text-slate-700 font-semibold text-sm whitespace-nowrap">Expired Date</p>
                  </th>
                </tr>
              </thead>
              <tbody v-if="listProductSelected.length > 0" class="bg-gray-50">
                <tr v-for="product in listProductSelected" :key="product.id">
                  <td class="p-2 space-y-3 text-center">
                    <p class="text-slate-900 font-semibold text-center text-sm whitespace-nowrap">{{ product.name }}</p>
                    <Button 
                      class="!text-slate-100 !bg-slate-800 hover:!bg-slate-700 !border-none"
                      :label="product.unit.name"
                      size="small"
                    />
                  </td>
                  <td class="p-2">
                    <InputNumber 
                      v-model="qty[product.id]" 
                      @input="(value) => calculateEachProductData('qty', product.id, value)" 
                      :inputId="`qty_of_${product.id}`" 
                      class="qty-input"
                      :disabled="isOrderPaid()" 
                    />
                  </td>
                  <td class="p-2">
                    <InputNumber 
                      v-model="price_before_discount[product.id]"
                      prefix="Rp. "
                      disabled 
                      :inputId="`price_before_disc_of_${product.id}`" 
                      class="w-full"
                    />
                  </td>
                  <td class="p-2">
                    <InputNumber 
                      v-model="tax[product.id]"
                      @input="(value) => calculateEachProductData('tax', product.id, value)"  
                      class="w-full" 
                      :inputId="`tax_of_${product.id}`" 
                      placeholder="(Opsional)"
                      :disabled="isOrderPaid()" 
                    />                 
                  </td>
                  <td class="p-2">
                    <InputNumber 
                      v-model="discount[product.id]"
                      @input="(value) => calculateEachProductData('discount', product.id, value)"   
                      class="w-full" 
                      :inputId="`disc_of${product.id}`" 
                      placeholder="(Opsional)"
                      :disabled="isOrderPaid()"  
                    />
                  </td>
                  <td class="p-2">
                    <InputNumber 
                      v-model="price_after_discount[product.id]"
                      prefix="Rp. "
                      disabled  
                      :inputId="`price_after_disc_of_${product.id}`" 
                      class="w-full"
                    />
                  </td>
                  <td class="p-2">
                    <InputNumber 
                      v-model="total_price[product.id]"
                      prefix="Rp. " 
                      :inputId="`total_price_of_${product.id}`" 
                      disabled 
                      class="w-full"  
                    />
                  </td>
                  <td class="p-2">
                    <InputNumber 
                      v-model="profit_margin[product.id]"
                      disabled
                      :inputId="`profit_margin_of${product.id}`" 
                      suffix="%"
                      class="w-full"  
                    />
                  </td>
                  <td class="p-2">
                    <InputNumber 
                      v-model="selling_price[product.id]"
                      disabled
                      prefix="Rp. "
                      :inputId="`selling_price_of${product.id}`" 
                      class="w-full"
                    />
                  </td>
                  <td class="p-2">
                    <InputNumber
                     v-model="batch_number[product.id]"
                     placeholder="(Opsional)"
                     :useGrouping="false" 
                     :inputId="`batch_number_of${product.id}`"
                     class="batch-number-input"
                     @input="(value) => handleBatchNumberProduct('batch_number', product.id, value)" 
                    />
                  </td>
                  <td class="p-2">
                    <DatePicker 
                      v-model="expired_date_product[product.id]"
                      class="expired-date-input"
                      placeholder="Tanggal kadaluarsa"
                      dateFormat="dd/mm/yy"
                      showIcon
                      fluid
                      :inputId="`expired_date_of${product.id}`"
                      @change="(value) => handleExpiredDateProduct('expired_date_product', product.id, value)"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
            <div v-if="listProductSelected.length === 0" class="p-4">
              <p class="text-center text-slate-600">Tidak ada produk yang dipilih.</p>
            </div>
          </div>
        </div>
        <div class="detail">
          <div class="flex justify-end mt-5">
            <div class="grid grid-cols-2 gap-5">
              <div class="flex flex-col items-start">
                <p class="font-semibold text-slate-600">Total Item</p>
                <p class="font-semibold text-slate-600">Jumlah Total</p>
              </div>
              <div class="flex flex-col items-start">
                <p>: {{ totalQtyItems }}</p>
                <p>: {{ formatCurrencyIDR(totalPriceItems) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-md shadow-md border-l-8 border-[#022f45] w-full card p-4 mt-5">
        <div class="grid grid-cols-2 gap-3">
          <div class="shipping_details">
            <label for="shipping_details" class="block mb-2 text-slate-500 font-semibold">
              Rincian Pengiriman
              <span class="text-rose-500 font-normal text-sm">(Opsional)</span>
            </label>
            <InputGroup>
              <InputGroupAddon>
                <i class="pi pi-clipboard"></i>
              </InputGroupAddon>
              <Textarea 
                id="shipping_details" 
                v-model="shipping_details"
                v-bind="shippingDetailsAttr"
                class="w-full" 
                autoResize
                rows="5" 
                cols="30"
                placeholder="Catatan pengiriman barang"
              />
            </InputGroup>
          </div>
          <div>
            <label for="shipping_cost" class="block mb-2 text-slate-500 font-semibold">
              Biaya Pengiriman
              <span class="text-rose-500 font-normal text-sm">(Opsional)</span>
            </label>
            <InputGroup>
              <InputGroupAddon>
                <i class="pi pi-truck"></i>
              </InputGroupAddon>
              <InputNumber 
                v-model="shipping_cost"
                v-bind="shipingCostAttr"
                @blur="calculateCostShippingIntoTotalPrice" 
                inputId="shipping_cost"
                mode="currency" 
                currency="IDR"
                locale="id-ID"
                :minFractionDigits="0"
                class="w-full"
                fluid
                :disabled="isOrderPaid()"
              />
            </InputGroup>
            <div class="flex justify-end items-end space-y-2 h-20">
              <div>
                <p class="font-semibold text-slate-600">Jumlah Total Pembelian</p>
                <p class="font-semibold text-slate-600">Status Pembayaran</p>
              </div>
              <div>
                <p>: {{ formatCurrencyIDR(totalPriceItems) }}</p>
                <p :class="[isOrderPaid() ? 'text-green-500' : 'text-rose-500']">: {{ isOrderPaid() ? 'LUNAS' : 'BELUM LUNAS' }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-md shadow-md border-l-8 border-[#022f45] w-full card p-4 mt-5">
        <div>
          <div class="order_note">
            <label for="order_note" class="block mb-2 text-slate-500 font-semibold">
              Catatan Pembelian
              <span class="text-rose-500 font-normal text-sm">(Opsional)</span>
            </label>
            <Textarea 
              rows="8"
              placeholder="Berikan rincian catatan pembelian"
              v-model="order_note"
              v-bind="orderNoteAttr"
              class="w-full"
            />
          </div>
        </div>
        <div class="flex justify-end">
          <Button 
            type="button"
            label="Simpan"
            class="!bg-blue-900 hover:!bg-blue-800 !text-white !border-none mt-5"
            @click="submitEditPurchasedProduct"
            :loading="loading"
            />
          </div>
      </div>
    </template>
  </Content>
</template>

<script>
import Content from "../../components/Layout/Content.vue";
import Select from "primevue/select";
import DatePicker from 'primevue/datepicker';
import InputText from "primevue/inputtext";
import InputNumber from "primevue/inputnumber";
import FileUpload from "primevue/fileupload";
import InputGroup from "primevue/inputgroup";
import InputGroupAddon from "primevue/inputgroupaddon";
import ProgressBar from 'primevue/progressbar';
import Textarea from "primevue/textarea";
import { useToast } from "primevue/usetoast";
import { ref, onMounted, watch, computed } from 'vue';
import { debounce } from '../../helpers/debounce';
import { usePurchasedProductStore } from "../../stores/purchased_product";
import { useApotekStore } from "../../stores/apotek";
import { useSupplierStore } from "../../stores/supplier";
import { formatCurrencyIDR } from '../../helpers/formatCurrency';
import { useForm } from "vee-validate";
import * as yup from "yup";
import { useRoute, useRouter } from "vue-router";

export default {
  components: {
    Content,
    Select,
    DatePicker,
    InputText,
    InputNumber,
    FileUpload,
    InputGroup,
    InputGroupAddon,
    ProgressBar,
    Textarea
  },
  setup() {
    const route = useRoute()
    const breadcrumbIcon = ref({
      icon: 'pi pi-chart-bar'
    })
    const breadcrumbItems = ref([
        { label: 'Dashboard' }, 
        { label: 'Data Pembelian' }, 
        { label: 'Edit Data Pembelian' }, 
        { label: route.params.reference_number }, 
    ]);
    const paymentMethodOptions = ref([
      { name: "Bank Transfer" },
      { name: "Credit" },
      { name: "Cash" }
    ])
    const router = useRouter()
    const toast = useToast()
    const purchasedProductStore = usePurchasedProductStore()
    const detailPurchasedProduct = computed(() => purchasedProductStore.detailPurchasedProduct)
    const apotekStore = useApotekStore()
    const supplierStore = useSupplierStore()
    const products = ref([])
    const apoteks = ref([])
    const suppliers = ref([])
    const listProductSelected = ref([])
    const loading = ref(false)
    const selectedSupplier = ref("")
    const addressSupplier = ref(detailPurchasedProduct.value.supplier.address || '')
    const qty = ref([])
    const price_before_discount = ref([])
    const tax = ref([])
    const discount = ref([])
    const price_after_discount = ref([])
    const total_price = ref([])
    const profit_margin = ref([])
    const selling_price = ref([])
    const batch_number = ref([])
    const expired_date_product = ref([])
    const initialPrice = ref([])
    const totalQtyItems = ref(0)
    const totalPriceItems = ref(0)
    const errorListProductNotSelected = ref(true)

    const isOrderPaid = () => {
      return detailPurchasedProduct.value.payment.status_payment === 'Paid'
    }

    onMounted(async () => {
      if (listProductSelected.value.length > 0) {
        errorListProductNotSelected.value = false
      }
      
      Promise.all([
        loadApoteks(),
        loadSuppliers()
      ])

      loadProductSelected()
      initialValueEachProduct()
      countTotalItemAndPriceValues()
      calculateCostShippingIntoTotalPrice()
    })

    const loadProductSelected = () => {
      purchasedProductStore.setInitialListProductSelected(detailPurchasedProduct.value.purchased_products)
      purchasedProductStore.setProductIds(...detailPurchasedProduct.value.purchased_products.map(product => product.id))

      listProductSelected.value = purchasedProductStore.listProductSelected
    }

    const loadApoteks = async () => {
      await apotekStore.getListApotekBySpecificColumn();
      apoteks.value = apotekStore.listApoteks;
    }

    const loadSuppliers = async () => {
      await supplierStore.getListSupplierBySpecificColumn();
      suppliers.value = supplierStore.listSuppliers;
    }

    const { handleSubmit, defineField, errors } = useForm({
      validationSchema: {
        supplier_id: yup.string().required("Pemasok harus dipilih."),
        apotek_id: yup.string().required("Lokasi apotek harus dipilih."),
        reference_number: yup.string().nullable(),
        order_date: yup.date().required("Tanggal pembelian wajib ditentukan."),
        status_order: yup.string().required("Status order wajib dipilih."),
        shipping_cost: yup.number().nullable(),
        shipping_details: yup.string().nullable(),
        order_note: yup.string().nullable(),
      },
      initialValues: {
        supplier_id: detailPurchasedProduct.value.supplier_id,
        reference_number: detailPurchasedProduct.value.reference_number,
        apotek_id: detailPurchasedProduct.value.apotek_id,
        status_order: detailPurchasedProduct.value.status_order,
        order_date: detailPurchasedProduct.value.order_date,
        shipping_details: detailPurchasedProduct.value.shipping_details,
        shipping_cost: detailPurchasedProduct.value.shipping_cost,
        order_note: detailPurchasedProduct.value.order_note
      }
    })

    const [reference_number, referenceNumberAttr] = defineField('reference_number');
    const [order_date, orderDateAttr] = defineField('order_date');
    const [status_order, statusOrderAttr] = defineField('status_order');
    const [apotek_id, apotekIdAttr] = defineField('apotek_id');
    const [supplier_id, supplierIdAttr] = defineField('supplier_id');
    const [shipping_cost, shipingCostAttr] = defineField('shipping_cost');
    const [shipping_details, shippingDetailsAttr] = defineField('shipping_details');
    const [order_note, orderNoteAttr] = defineField('order_note');

    const handleDisplayAddressSupplier = (event) => {
      const { value:supplierId } = event

      suppliers.value.forEach(supplier => {
        if (supplier.id === supplierId) {
            addressSupplier.value = supplier.address
        }
      })      
    }

    const handleBatchNumberProduct = (field, productId, event) => {      
      const { value: batchNumber } = event
      if (field === 'batch_number') {
        batch_number.value[productId] = batchNumber
      }
    }

    const handleExpiredDateProduct = (field, productId, event) => {
      const { value: expiredProduct } = event
      if (field === 'expired_date_product') {
        expired_date_product.value[productId] = expiredProduct
      }
    }

    const handlePaymentMethodChange = (event) => {      
      const { value: paymentMethod } = event

      if (paymentMethod !== 'Credit') {
        termin_payment.value = null;
        format_termin_date.value = null
      } else {
        cash_paid.value = null
        paid_on.value = null
        paid_notes.value = null
      }
    }

    /**
     * Submits a purchased product to be added.
     * 
     * @param {Object} datas - The data for the purchased product.
     * @returns {Promise<void>} - A promise that resolves when the purchased product is added successfully.
     */
    const submitEditPurchasedProduct = handleSubmit(async (datas) => { 
      loading.value = true
      const productIds = detailPurchasedProduct.value.purchased_products.map(product => product.id);
      const purchaseProductId = detailPurchasedProduct.value.id;

      const expiredDateIsNull = listProductSelected.value.some(product => {
        if (expired_date_product.value[product.id] === null) {
          return true;
        }
        return false;
      });

      if (expiredDateIsNull) {
        toast.add({
          severity: 'error',
          summary: 'Error Submit Purchased Product',
          detail: `Cantumkan tanggal kadaluarsa pada setiap produk.`,
          life: 3000
        });
        loading.value = false
        return; 
      }
      
      const dataOrder = {
        supplier_id: datas.supplier_id,
        apotek_id: datas.apotek_id,
        reference_number: datas.reference_number,
        order_date: datas.order_date,
        status_order: datas.status_order,
        shipping_cost: datas.shipping_cost,
        shipping_details: datas.shipping_details,
        order_note: datas.order_note,
        product_id: productIds,
        grand_total: totalPriceItems.value,
      }
      
      const productData = dataOrder.product_id.reduce((acc, productId) => {
        acc[productId] = {
          oldQty: detailPurchasedProduct.value.purchased_products.find(product => product.id === productId).product_detail.qty,
          newQty: qty.value[productId] || 0,
          price_before_discount: price_before_discount.value[productId] || 0,
          tax: tax.value[productId] || 0,
          discount: discount.value[productId] || 0,
          price_after_discount: price_after_discount.value[productId] || 0,
          profit_margin: profit_margin.value[productId] || 0,
          selling_price: selling_price.value[productId] || 0,
          batch_number: batch_number.value[productId] || '',
          expired_date_product: expired_date_product.value[productId] || '',
          total_price: total_price.value[productId] || 0,
        };
        return acc;
      }, {});

      const finalDataOrder = {
        ...dataOrder,
        productData,
      }
      
      await purchasedProductStore.editPurchasedProduct(finalDataOrder, purchaseProductId)

      if (purchasedProductStore.errorEditData) {
        loading.value = false
        toast.add({
          severity: 'error',
          summary: 'Added Purchased Productt Failed',
          detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
          life: 3000
        });
      } else {
        loading.value = false
        purchasedProductStore.setNullListProductSelected()
        router.push({name: 'purchased-product.data-purchased'})
      }            
    })

    /**
     * Initializes the initial values for each product in the purchased product list.
     * If there are selected products, it sets the initial values based on the product properties.
     * If there are no selected products, it does nothing.
     */
    const initialValueEachProduct = () => {      
      if (purchasedProductStore.listProductSelected.length > 0) {
          purchasedProductStore.listProductSelected.forEach(product => {
          const productId = product.id;

          qty.value[productId] = qty.value[productId] ?? product.qty;
          price_before_discount.value[productId] = price_before_discount.value[productId] ?? product.unit_price;
          tax.value[productId] = tax.value[productId] ?? product.tax;
          discount.value[productId] = discount.value[productId] ?? product.discount;
          price_after_discount.value[productId] = price_after_discount.value[productId] ?? product.price_after_discount;
          total_price.value[productId] = total_price.value[productId] ?? product.total_price;
          profit_margin.value[productId] = profit_margin.value[productId] ?? product.profit_margin;
          selling_price.value[productId] = selling_price.value[productId] ?? product.unit_selling_price;
          batch_number.value[productId] = batch_number.value[productId] ?? product.batch_number;
          expired_date_product.value[productId] = expired_date_product.value[productId] ?? product.expired_date_product;
          initialPrice.value[productId] = initialPrice.value[productId] ?? product.unit_price;
        });
      }
    };


    const countTotalItemAndPriceValues = () => {
      totalQtyItems.value = listProductSelected.value.reduce((acc, curr) => {
        return acc + qty.value[curr.id]
      }, 0);
      
      totalPriceItems.value = listProductSelected.value.reduce((acc, curr) => {        
        return acc + total_price.value[curr.id]
      }, 0);
    }

    /**
     * Calculates the data for each product based on the given field, product ID, and event.
     * 
     * @param {string} field - The field to calculate the data for (e.g., 'qty', 'discount', 'tax').
     * @param {number} productId - The ID of the product.
     * @param {Event} event - The event object containing the value.
     */
    const calculateEachProductData = (field, productId, event) => {
      const { value: eventValue } = event;

      switch (field) {
        case 'qty':
            qty.value[productId] = eventValue;
            total_price.value[productId] = eventValue * initialPrice.value[productId];
          break;

        case 'discount':
            const newPriceAfterDiscount = initialPrice.value[productId] - (initialPrice.value[productId] * (eventValue / 100));
            const newTaxAmount = newPriceAfterDiscount * (tax.value[productId] / 100);
            
            total_price.value[productId] = qty.value[productId] * (newPriceAfterDiscount + newTaxAmount);
            price_after_discount.value[productId] = newPriceAfterDiscount;
            profit_margin.value[productId] = ((selling_price.value[productId] - newPriceAfterDiscount) / newPriceAfterDiscount) * 100;
          break;

        case 'tax':         
            const currentPriceAfterDiscount = initialPrice.value[productId] - (initialPrice.value[productId] * (discount.value[productId] / 100));
            const updatedTaxAmount = currentPriceAfterDiscount * (eventValue / 100);

            total_price.value[productId] = qty.value[productId] * (currentPriceAfterDiscount + updatedTaxAmount);
          break;

        default:
          break;
      }

      countTotalItemAndPriceValues()
      totalPriceItems.value = totalPriceItems.value + shipping_cost.value;
    }

    /**
     * Calculates the total price of purchased products by taking into account the shipping cost.
     * 
     * @param {Object} event - The event object containing the shipping cost value.
     */
    let previousShippingCost = 0;
    const calculateCostShippingIntoTotalPrice = (event) => {
      if (event?.value) {
        const newShippingCostValue = parseFloat(event.value.replace(/Rp\s*/g, '').replace(/\./g, '').replace(/,/g, '.'));
        
        if (!isNaN(newShippingCostValue)) { 
            totalPriceItems.value = totalPriceItems.value - previousShippingCost + newShippingCostValue;
            previousShippingCost = newShippingCostValue;
        }   
      } else {
        const initialShippingCost = shipping_cost.value;
        totalPriceItems.value = totalPriceItems.value + initialShippingCost;
        previousShippingCost = initialShippingCost;
      }      
    }
    
    return {
      breadcrumbIcon,
      breadcrumbItems,
      paymentMethodOptions,
      products,
      suppliers,
      apoteks,
      loading,
      listProductSelected,
      formatCurrencyIDR,
      selectedSupplier,
      handleDisplayAddressSupplier,
      handlePaymentMethodChange,
      handleBatchNumberProduct,
      handleExpiredDateProduct,
      errors,
      addressSupplier,
      reference_number,
      order_date,
      apotek_id,
      supplier_id,
      referenceNumberAttr,
      orderDateAttr,
      apotekIdAttr,
      supplierIdAttr,
      qty,
      price_before_discount,
      tax,
      discount,
      price_after_discount,
      total_price,
      profit_margin,
      selling_price,
      batch_number,
      expired_date_product,
      shipping_cost,
      shipingCostAttr,
      shipping_details,
      shippingDetailsAttr,
      order_note,
      orderNoteAttr,
      status_order,
      statusOrderAttr,
      calculateEachProductData,
      calculateCostShippingIntoTotalPrice,
      totalPriceItems,
      totalQtyItems,
      submitEditPurchasedProduct,
      errorListProductNotSelected,
      detailPurchasedProduct,
      isOrderPaid
    }
  }
}
</script>

<style scoped>
:deep(.qty-input > .p-inputtext.p-inputnumber-input)  {
  width: 5rem !important;
}

:deep(.batch-number-input > .p-inputtext.p-inputnumber-input)  {
  width: 10rem !important;
}

:deep(.expired-date-input > .p-inputtext.p-datepicker-input)  {
  width: 10rem !important;
}

:deep(.p-fileupload) {
  width: 100% !important;
}
</style>