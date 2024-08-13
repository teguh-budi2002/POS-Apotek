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
                v-model="order_date"
                v-bind="orderDateAttr" 
              />
            </div>
            <div class="mt-2">
              <div class="flex items-center space-x-2 mb-2">
                <label for="proof_of_payment" class="font-semibold text-slate-500">Bukti Pembayaran</label>
                <i class="pi pi-question-circle bg-blue-300 text-white rounded-full cursor-pointer" v-tooltip.top="'Unggah Bukti Pembayaran'" style="font-size: 1.2rem"></i>
              </div>
              <div v-if="errors.proof_of_payment">
                <p class="text-xs text-rose-500 mb-1">{{ errors.proof_of_payment }}</p>
              </div>
              <div class="flex justify-start">
                <FileUpload 
                  class="!bg-slate-900 hover:!bg-slate-700"
                  v-model="proof_of_payment" 
                  :maxFileSize="2048000" 
                  :multiple="false"
                  :fileLimit="1"
                  @select="handleSelectOnFileUpload" 
                />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-md shadow-md border-l-8 border-[#022f45] w-full card p-4 mt-5">
        <div class="search_product">
          <div class="flex justify-center items-center space-x-2">
            <Button
                    type="button"
                    icon="pi pi-filter-slash"
                    label="Clear"
                    outlined
                    @click="clearFilter()"
                />
            <div class="w-2/4 relative">
              <div class="bg-white z-50 absolute top-12 shadow-lg p-2 rounded-b-lg w-[94%] h-full min-h-40 overflow-y-auto custom-scrollbar" v-show="searchQuery">
                <div class="mt-10" v-if="loading">
                   <ProgressBar mode="indeterminate" style="height: 6px;" class="w-10/12 mx-auto"></ProgressBar>
                </div>
                <div v-else>
                  <div v-if="products.length > 0">
                    <div 
                      class="flex items-centers space-x-2 cursor-pointer hover:bg-slate-100 p-2 rounded font-semibold text-slate-500"
                      v-for="product in products"
                      :key="product.id"
                      @click="selectProduct(product)"
                    >
                      <p>{{ product.product_code }}</p>
                      <p>-</p>
                      <p>{{ product.name }}</p>
                    </div>
                  </div>
                  <div v-else class="mt-7">
                    <p class="text-center">Produk <b>{{ searchQuery }}</b> tidak ditemukan.</p>
                  </div>
                </div>
              </div>
              <InputGroup>
                <InputText 
                  class="w-full"
                  placeholder="Cari produk"
                  v-model="searchQuery"
                />
                <Button 
                  icon="pi pi-search"
                  class="!bg-slate-900 !text-white !border-none"
                />
              </InputGroup>
            </div>
          </div>
          <div v-if="errorListProductNotSelected" class="flex justify-center mt-3">
            <div class="p-1 px-2 bg-rose-400 w-fit rounded-md">
              <p class="text- text-center text-white uppercase font-semibold">Wajib Memilih Minimal 1 Produk</p>
            </div>
          </div>
        </div>
        <div class="selected_product mt-5">
          <div class="max-w-full max-h-56 overflow-x-auto custom-scrollbar mx-auto">
            <table class="w-full table-auto">
              <thead class="bg-slate-200">
                <tr>
                  <th class="p-2">
                    <i class="pi pi-trash text-rose-500"></i>
                  </th>
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
                  <td class="p-2">
                    <Button 
                      icon="pi pi-times"
                      class="!text-rose-500 hover:!bg-rose-100"
                      text
                      @click="removeSelectedProduct(product)"
                    />
                  </td>
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
                    />                 
                  </td>
                  <td class="p-2">
                    <InputNumber 
                      v-model="discount[product.id]"
                      @input="(value) => calculateEachProductData('discount', product.id, value)"   
                      class="w-full" 
                      :inputId="`disc_of${product.id}`" 
                      placeholder="(Opsional)"  
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
                      @input="(value) => handleExpiredDateProduct('expired_date_product', product.id, value)"
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
            <label for="shiping_cost" class="block mb-2 text-slate-500 font-semibold">
              Biaya Pengiriman
              <span class="text-rose-500 font-normal text-sm">(Opsional)</span>
            </label>
            <InputGroup>
              <InputGroupAddon>
                <i class="pi pi-truck"></i>
              </InputGroupAddon>
              <InputNumber 
                v-model="shiping_cost"
                v-bind="shipingCostAttr"
                @blur="calculateCostShippingIntoTotalPrice" 
                inputId="shiping_cost" 
                class="w-full"
                fluid
              />
            </InputGroup>
            <div class="flex justify-end items-end h-20">
              <div>
                <p class="font-semibold text-slate-600">Jumlah Total Pembelian</p>
              </div>
              <div>
                <p>: {{ formatCurrencyIDR(totalPriceItems) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-md shadow-md border-l-8 border-[#022f45] w-full card p-4 mt-5">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label for="payment_method" class="mb-2 block font-semibold text-slate-500">Metode Pembayaran</label>
            <div v-if="errors.payment_method">
                <p class="text-xs text-rose-500 mb-1">{{ errors.payment_method }}</p>
            </div>
            <Select 
              :options="paymentMethodOptions"
              optionLabel="name"
              optionValue="name"
              class="w-full"
              placeholder="Metode pembayaran saat pembelian barang"
              v-model="payment_method"
              v-bind="paymentMethodAttr"
              @change="handlePaymentMethodChange"
            />
            <div class="termin_payment mt-2" v-show="payment_method === 'Credit'">
              <div class="flex items-center space-x-2 mb-2">
                <label for="termin_payment" class="font-semibold text-slate-500">Termin Pembayaran</label>
                <i class="pi pi-question-circle bg-blue-300 text-white rounded-full cursor-pointer" v-tooltip.top="'Tentukan waktu kredit'" style="font-size: 1.2rem"></i>
              </div>
              <InputGroup>
                <InputNumber
                  v-model="termin_payment"
                  v-bind="terminPaymentAttr"
                  placeholder="Termin Pembayaran"
                  class="termin-input"
                />
                <Select 
                  :options="[
                    { name: 'Hari', value: 'Day' },
                    { name: 'Bulan', value: 'Month' }
                  ]"
                  v-model="format_termin_date"
                  v-bind="formatTerminDateAttr"
                  optionLabel="name"
                  optionValue="value"
                  placeholder="Pilih jenis waktu"
                />
              </InputGroup>
            </div>
            <div v-show="payment_method === 'Cash'">
              <div class="cash mt-2">
                <div class="flex items-center space-x-2 mb-2">
                  <label for="cash" class="font-semibold text-slate-500">Uang Tunai</label>
                  <i class="pi pi-question-circle bg-blue-300 text-white rounded-full cursor-pointer" v-tooltip.top="'Nominal uang tunai yang dibayar'" style="font-size: 1.2rem"></i>
                </div>
                <InputGroup>
                  <InputGroupAddon>
                    <i class="pi pi-money-bill"></i>
                  </InputGroupAddon>
                  <InputNumber
                    v-model="cash_paid"
                    v-bind="cashPaidAttr"
                    prefix="Rp "
                    placeholder="Nominal Uang Tunai Yang Dibayar"
                    class="termin-input"
                  />
                </InputGroup>
              </div>
              <div class="status_payment mt-2">
                <label for="status_payment" class="font-semibold text-slate-500 block mb-2">Status Pembayaran</label>
                <Select
                  v-model="status_payment"
                  v-bind="statusPaymentAttr"
                  :options="[{name: 'Sudah Dibayar', value: 'Paid'}, {name: 'Terlambat', value: 'Late'}]"
                  optionLabel="name"
                  optionValue="value"
                  placeholder="Pilih status pembayaran"
                  class="w-full"
                />
              </div>
              <div class="paid_date mt-2">
                <div>
                  <label for="paid_on" class="mb-2 block font-semibold text-slate-500">Dibayar Pada</label>
                  <DatePicker 
                    class="w-full"
                    placeholder="Tentukan tanggal pembelian"
                    showIcon 
                    showButtonBar
                    fluid
                    v-model="paid_on" 
                    v-bind="paidOnAttr"
                  />
                </div>
              </div>
            </div>
          </div>
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
        </div>
        <div class="flex justify-end">
          <Button 
            type="button"
            label="Simpan"
            class="!bg-blue-900 hover:!bg-blue-800 !text-white !border-none mt-5"
            @click="submitPurchasedProduct"
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
import { ref, onMounted, watch } from 'vue';
import { debounce } from '../../helpers/debounce';
import { usePurchasedProductStore } from "../../stores/purchased_product";
import { formatCurrencyIDR } from '../../helpers/formatCurrency';
import { useForm } from "vee-validate";
import * as yup from "yup";
import { useRouter } from "vue-router";

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
    const breadcrumbIcon = ref({
      icon: 'pi pi-chart-bar'
    })
    const breadcrumbItems = ref([
        { label: 'Dashboard' }, 
        { label: 'Data Pembelian' }, 
        { label: 'Tambah Data Pembelian' }, 
    ]);
    const paymentMethodOptions = ref([
      { name: "Bank Transfer" },
      { name: "Credit" },
      { name: "Cash" }
    ])
    const router = useRouter()
    const toast = useToast()
    const purchasedProductStore = usePurchasedProductStore()
    const products = ref([])
    const apoteks = ref([])
    const suppliers = ref([])
    const listProductSelected = ref([])
    const loading = ref(false)
    const searchQuery = ref(purchasedProductStore.filters.search || '')
    const selectedSupplier = ref("")
    const addressSupplier = ref("")
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
    const MAX_FILE_SIZE = 2048000;
    const initialPrice = ref([])
    const totalQtyItems = ref(0)
    const totalPriceItems = ref(0)
    const errorListProductNotSelected = ref(true)

    onMounted(async () => {
      searchQuery.value = purchasedProductStore.filters.search || ''

      if (searchQuery.value) {
        await loadProductsByNameAndProductCode()
      }

      listProductSelected.value = purchasedProductStore.listProductSelected
      if (listProductSelected.value.length > 0) {
        errorListProductNotSelected.value = false
      }
      
      Promise.all([
        loadApoteks(),
        loadSuppliers()
      ])

      initialValueEachProduct()
      countTotalItemAndPriceValues()
    })

    const loadProductsByNameAndProductCode = async () => {
      await purchasedProductStore.getListProductsBySpecificColumn();
      products.value = purchasedProductStore.listProducts;
      loading.value = false;
    };

    const loadApoteks = async () => {
      await purchasedProductStore.getListApoteks();
      apoteks.value = purchasedProductStore.listApoteks;
    }

    const loadSuppliers = async () => {
      await purchasedProductStore.getListSuppliers();
      suppliers.value = purchasedProductStore.listSuppliers;
    }

    const debouncedSearch = debounce((value) => {
      purchasedProductStore.setFilter('search', value)
      loadProductsByNameAndProductCode()
    }, 500)

    watch(searchQuery, (newQuery, oldQuery) => {
      if (newQuery !== oldQuery) {
          loading.value = true;
          debouncedSearch(newQuery)
        }
    })

    const clearFilter = () => {
        searchQuery.value = ''
        purchasedProductStore.filters.search = ''
    };

    const isValidExtension = (file) => {
      const validExtensions = ['application/msword', 'image/png', 'image/jpg'];
      return validExtensions.includes(file.type);
    }

    const { handleSubmit, defineField, errors } = useForm({
      validationSchema: {
        supplier_id: yup.string().required("Pemasok harus dipilih."),
        apotek_id: yup.string().required("Lokasi apotek harus dipilih."),
        reference_number: yup.string().nullable(),
        order_date: yup.date().required("Tanggal pembelian wajib ditentukan."),
        status_order: yup.string().required("Status order wajib dipilih."),
        paid_on: yup.date().nullable(),
        payment_method: yup.string().required("Metode pembayaran wajib dipilih."),
        termin_payment: yup.number().nullable(),
        format_terim_date: yup.string().nullable(),
        cash_paid: yup.number().nullable(),
        status_payment: yup.string().nullable(),
        shiping_cost: yup.number().nullable(),
        shipping_details: yup.string().nullable(),
        order_note: yup.string().nullable(),
        proof_of_payment: yup.mixed().required("Bukti pembayaran wajib diunggah.")
                              .test('max-file-size', 'Ukuran file terlalu besar.', (value) => value.size <= MAX_FILE_SIZE)
                              .test('file-type', 'Ekstensi file tidak valid.', (value) => isValidExtension(value)),
      },
      initialValues: {
        shiping_cost: 0
      }
    })

    const [payment_method, paymentMethodAttr] = defineField('payment_method');
    const [termin_payment, terminPaymentAttr] = defineField('termin_payment');
    const [format_termin_date, formatTerminDateAttr] = defineField('format_termin_date');
    const [cash_paid, cashPaidAttr] = defineField('cash_paid');
    const [status_payment, statusPaymentAttr] = defineField('status_payment')
    const [reference_number, referenceNumberAttr] = defineField('reference_number');
    const [order_date, orderDateAttr] = defineField('order_date');
    const [status_order, statusOrderAttr] = defineField('status_order');
    const [paid_on, paidOnAttr] = defineField('paid_on');
    const [apotek_id, apotekIdAttr] = defineField('apotek_id');
    const [supplier_id, supplierIdAttr] = defineField('supplier_id');
    const [shiping_cost, shipingCostAttr] = defineField('shiping_cost');
    const [shipping_details, shippingDetailsAttr] = defineField('shipping_details');
    const [order_note, orderNoteAttr] = defineField('order_note');
    const [proof_of_payment, proofOfPaymentAttr] = defineField('proof_of_payment');

    const handleDisplayAddressSupplier = (event) => {
      const { value:supplierId } = event

      suppliers.value.forEach(supplier => {
        if (supplier.id === supplierId) {
            addressSupplier.value = supplier.address
        }
      })      
    }

    const handleSelectOnFileUpload = (event) => {      
      if (event.files && event.files.length > 0) {
        if (event.files.length > 1) {
          return;
        }
          proof_of_payment.value = event.files[0];
      } else {
          proof_of_payment.value = null;
      }
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
        status_payment.value = null
        paid_on.value = null
      }
    }

    /**
     * Submits a purchased product to be added.
     * 
     * @param {Object} datas - The data for the purchased product.
     * @returns {Promise<void>} - A promise that resolves when the purchased product is added successfully.
     */
    const submitPurchasedProduct = handleSubmit(async (datas) => {      
      loading.value = true
      const productIds = purchasedProductStore.productIds

      if (listProductSelected.value.length === 0) {        
        errorListProductNotSelected.value = true
        toast.add({
          severity: 'error',
          summary: 'Error Submit Purchased Product',
          detail: 'Wajib memilih minimal 1 produk.',
          life: 3000
        });
        loading.value = false
        return;
      }

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
        paid_on: datas.paid_on,
        payment_method: datas.payment_method,
        termin_payment: datas.termin_payment,
        cash_paid: datas.cash_paid,
        status_payment: datas.status_payment,
        shiping_cost: datas.shiping_cost,
        shipping_details: datas.shipping_details,
        order_note: datas.order_note,
        proof_of_payment: datas.proof_of_payment,
        product_id: productIds,
        grand_total: totalPriceItems.value
      }

      const productData = dataOrder.product_id.reduce((acc, productId) => {
        acc[productId] = {
          qty: qty.value[productId] || 0,
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

      await purchasedProductStore.AddPurchasedProduct(finalDataOrder)

      if (purchasedProductStore.productDoesntHaveDefaultStockError) {
        loading.value = false
        toast.add({
          severity: 'error',
          summary: 'Error Submit Purchased Product',
          detail: purchasedProductStore.errorMessage,
          life: 6000
        });

        return;
      }

      if (purchasedProductStore.errorAddedData) {
        loading.value = false
        toast.add({
          severity: 'error',
          summary: 'Added Purchased Productt Failed',
          detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
          life: 3000
        });
      } else {
        loading.value = false
        router.push({name: 'purchased-product', params: { addedPurchasedProducts: true }})
      }            
    })

    /**
     * Selects a product and performs necessary actions.
     *
     * @param {Object} product - The selected product.
     */
    const selectProduct = (product) => {
      purchasedProductStore.setSelectedListProducts(product)
      purchasedProductStore.setProductIds(product.id)
      initialValueEachProduct()
      countTotalItemAndPriceValues()

      errorListProductNotSelected.value = false
      searchQuery.value = ''
      purchasedProductStore.filters.search = ''
    }

    /**
     * Removes a selected product from the purchased product list.
     * 
     * @param {Object} product - The product to be removed.
     */
    const removeSelectedProduct = (product) => {
      purchasedProductStore.removeSelectedListProducts(product)
      if (listProductSelected.value.length === 0) {        
        errorListProductNotSelected.value = true
        
      }  
      countTotalItemAndPriceValues()
    }

    /**
     * Initializes the initial values for each selected product.
     * If there are selected products, it sets the default values for each product.
     * @function initialValueEachProduct
     * @returns {void}
     */
    const initialValueEachProduct = () => {
    if (purchasedProductStore.listProductSelected.length > 0) {
          purchasedProductStore.listProductSelected.forEach(product => {
          const productId = product.id;

          qty.value[productId] = qty.value[productId] ?? 1;
          price_before_discount.value[productId] = price_before_discount.value[productId] ?? product.unit_price;
          tax.value[productId] = tax.value[productId] ?? 0;
          discount.value[productId] = discount.value[productId] ?? 0;
          price_after_discount.value[productId] = price_after_discount.value[productId] ?? product.unit_price;
          total_price.value[productId] = total_price.value[productId] ?? product.unit_price;
          profit_margin.value[productId] = profit_margin.value[productId] ?? product.profit_margin;
          selling_price.value[productId] = selling_price.value[productId] ?? product.unit_selling_price;
          batch_number.value[productId] = batch_number.value[productId] ?? 0;
          expired_date_product.value[productId] = expired_date_product.value[productId] ?? null;
          initialPrice.value[productId] = initialPrice.value[productId] ?? product.unit_price;
        });
      }
    };


    const countTotalItemAndPriceValues = () => {
      const countTotalQtyItems = totalQtyItems.value = listProductSelected.value.reduce((acc, curr) => {
        return acc + qty.value[curr.id]
      }, 0);

      const countTotalPriceItems = totalPriceItems.value = listProductSelected.value.reduce((acc, curr) => {
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
      totalPriceItems.value = totalPriceItems.value + shiping_cost.value;
    }

    let previousShippingCost = 0;
    const calculateCostShippingIntoTotalPrice = (event) => {      
      const newShippingCostValue = parseFloat(event.value.replace(/,/g, ''));

      if (!isNaN(newShippingCostValue)) { 
          totalPriceItems.value = totalPriceItems.value - previousShippingCost + newShippingCostValue;
          previousShippingCost = newShippingCostValue;
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
      searchQuery,
      clearFilter,
      selectProduct,
      removeSelectedProduct,
      listProductSelected,
      formatCurrencyIDR,
      selectedSupplier,
      handleDisplayAddressSupplier,
      handleSelectOnFileUpload,
      handlePaymentMethodChange,
      handleBatchNumberProduct,
      handleExpiredDateProduct,
      errors,
      addressSupplier,
      payment_method,
      termin_payment,
      format_termin_date,
      formatTerminDateAttr,
      cash_paid,
      cashPaidAttr,
      reference_number,
      order_date,
      apotek_id,
      supplier_id,
      proof_of_payment,
      proofOfPaymentAttr,
      paymentMethodAttr,
      terminPaymentAttr,
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
      shiping_cost,
      shipingCostAttr,
      shipping_details,
      shippingDetailsAttr,
      order_note,
      orderNoteAttr,
      paid_on,
      paidOnAttr,
      status_order,
      statusOrderAttr,
      status_payment,
      statusPaymentAttr,
      calculateEachProductData,
      calculateCostShippingIntoTotalPrice,
      totalPriceItems,
      totalQtyItems,
      submitPurchasedProduct,
      errorListProductNotSelected,
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