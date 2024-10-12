<template>
  <Content>
    <template v-slot:content>
      <Toast />
      <div>
        <Breadcrumb :home="breadcrumbIcon" :model="breadcrumbItems" class="!bg-transparent"/>
      </div>
      <div class="bg-white rounded-md shadow-md border-l-8 border-[#022f45] w-full card p-4 mt-5">
         <div class="ret_ref_number mb-3">
          <div class="flex items-center space-x-2 mb-2">
            <label for="ref_number" class="font-semibold text-slate-500">Nomor Referensi</label>
            <i class="pi pi-question-circle bg-blue-300 text-white rounded-full cursor-pointer" v-tooltip.top="'Nomor referensi return'" style="font-size: 1.2rem"></i>
          </div>
          <div v-if="errors.return_reference_number">
            <p class="text-xs text-rose-500 mb-1">{{ errors.return_reference_number }}</p>
          </div>
          <InputGroup>
            <InputGroupAddon>RET-</InputGroupAddon>
            <InputText
              class="w-full"
              placeholder="(Opsional)"
              id="return_reference_number"
              v-model="return_reference_number"
              v-bind="returnReferenceNumberAttr"
            />
          </InputGroup>
        </div>
        <div class="ref_numbers">
          <label for="ref_numb" class="mb-2 block font-semibold text-slate-500">Nomor REF</label>
          <MultiSelect 
            class="w-full"
            placeholder="Silahkan pilih nomor REF dari pembelian."
            v-model="selectedRefNumber"
            :loading="selectLoading"
            :options="ref_numbers"
            optionLabel="reference_number"
            optionValue="reference_number"
            emptyMessage="Tidak ada data."
            display="chip"
            @click="loadRefNumbers"
            @change="setListProductWantToReturn"
          />
        </div>
        <div class="loading flex justify-center mt-5" v-show="loading">
          <ProgressSpinner style="width: 50px; height: 50px"/>
        </div>
        <div class="detail mb-4 mt-5"
          v-show="detail_products_returned.length > 0 && !loading"
          v-for="product in detail_products_returned"
          :key="product.id"
        >
          <Tag
            icon="pi pi-tag" 
            severity="contrast"
            :value="product.reference_number"
          />
          <div class="grid grid-cols-4 gap-3 mt-2">
            <div class="w-full shadow-md p-3 rounded" v-for="detail in product.ordered_product_details_for_return" :key="detail.name">
              <p class="font-semibold text-slate-500 text-center">{{ detail.product_code }} - {{ detail.name }}</p>
              <img :src="`/storage/product/${detail.img_product}`" loading="lazy" class="w-40 h-40 mx-auto my-3" alt="product image">
              <p class="font-semibold">Qty: <span class="font-normal">{{ detail.product_detail.qty }} items</span></p>
              <p class="font-semibold">Sub Total: <span class="font-normal">{{ detail.product_detail.sub_total }}</span></p>
              <p class="font-semibold">Batch Number: <span class="font-normal">{{ detail.product_detail.batch_number ?? '-' }}</span></p>
              <p class="font-semibold">Expired: <span class="font-normal">{{ detail.product_detail.expired_date_product }}</span></p>
            </div>
          </div>
        </div>
        <div class="detail" v-show="detail_products_returned.length > 0">
          <div class="flex justify-end mt-5">
            <div class="grid grid-cols-2 gap-5">
              <div class="flex flex-col items-start">
                <p class="font-semibold text-slate-600">Total Return</p>
                <p class="font-semibold text-slate-600">Dana Return</p>
              </div>
              <div class="flex flex-col items-start">
                <p>: {{ totalQtyItems }} Item</p>
                <p>: {{ formatCurrencyIDR(totalNominalReturned) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
       <div class="bg-white rounded-md shadow-md border-l-8 border-[#022f45] w-full card p-4 mt-3">
        <div class="grid grid-cols-4 gap-4">
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
              />
              <Button 
                icon="pi pi-plus-circle"
                size="medium"
                outlined 
                plain
                class="!text-blue-500"
              />
            </InputGroup>
          </div>
          <div>
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
          <div>
            <div>
             <label for="return_date" class="mb-2 block font-semibold text-slate-500">Tanggal Return</label>
              <div v-if="errors.return_date">
                <p class="text-xs text-rose-500 mb-1">{{ errors.return_date }}</p>
              </div>
              <DatePicker 
                class="w-full"
                placeholder="Tentukan tanggal return"
                showIcon 
                showButtonBar
                fluid
                dateFormat="dd/mm/yy"
                showTime 
                hourFormat="24"
                v-model="return_date"
                v-bind="returnDateAttr" 
              />
            </div>
          </div>
          <div>
            <div>
             <label for="return_status" class="mb-2 block font-semibold text-slate-500">Status Return</label>
              <div v-if="errors.return_status">
                <p class="text-xs text-rose-500 mb-1">{{ errors.return_status }}</p>
              </div>
              <Select 
                class="w-full"
                placeholder="Tentukan status return"
                size="small"
                v-model="return_status"
                v-bind="returnStatusAttr"
                :options="[{name: 'Pending'}, {name: 'Completed'}, {name: 'Rejected'}]"
                optionLabel="name"
                optionValue="name"
              />
            </div>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4 mt-4">
          <div>
            <label for="return_note" class="block mb-2 text-slate-500 font-semibold">
              Rincian Return
              <span class="text-rose-500 font-normal text-sm">(Opsional)</span>
            </label>
            <InputGroup>
              <InputGroupAddon>
                <i class="pi pi-clipboard"></i>
              </InputGroupAddon>
              <Textarea 
                id="return_note" 
                v-model="return_note"
                v-bind="returnNoteAttr"
                class="w-full !h-full" 
                autoResize
                rows="5" 
                cols="30"
                placeholder="Berikan rincian return (jika perlu)"
              />
            </InputGroup>
          </div>
          <div>
            <label for="additional_note" class="block mb-2 text-slate-500 font-semibold">
              Catatan Tambahan
              <span class="text-rose-500 font-normal text-sm">(Opsional)</span>
            </label>
            <InputGroup>
              <InputGroupAddon>
                <i class="pi pi-clipboard"></i>
              </InputGroupAddon>
              <Textarea 
                id="additional_note" 
                v-model="additional_note"
                v-bind="returnNoteAttr"
                class="w-full !h-full" 
                autoResize
                rows="5" 
                cols="30"
                placeholder="Berikan catatan tambahan (jika perlu)"
              />
            </InputGroup>
          </div>
        </div>
        <div class="flex justify-end">
          <Button 
            type="button"
            label="Simpan"
            class="!bg-blue-900 hover:!bg-blue-800 !text-white !border-none mt-5 disabled:!cursor-not-allowed"
            @click="submitReturnedProduct"
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
import MultiSelect from "primevue/multiselect";
import DatePicker from 'primevue/datepicker';
import InputText from "primevue/inputtext";
import InputNumber from "primevue/inputnumber";
import InputGroup from "primevue/inputgroup";
import InputGroupAddon from "primevue/inputgroupaddon";
import ProgressBar from 'primevue/progressbar';
import Textarea from "primevue/textarea";
import Tag from "primevue/tag";
import ProgressSpinner from 'primevue/progressspinner';
import { useToast } from "primevue/usetoast";
import { ref, onMounted } from 'vue';
import { useReturnPurchasedProductsStore } from "../../stores/return_purchased_products";
import { formatCurrencyIDR } from '../../helpers/formatCurrency';
import { useForm } from "vee-validate";
import * as yup from "yup";
import { useRouter } from "vue-router";

export default {
  components: {
    Content,
    Select,
    MultiSelect,
    DatePicker,
    InputText,
    InputNumber,
    InputGroup,
    InputGroupAddon,
    ProgressBar,
    Textarea,
    Tag,
    ProgressSpinner
  },
  setup() {
    const breadcrumbIcon = ref({
      icon: 'pi pi-chart-bar'
    })
    const breadcrumbItems = ref([
        { label: 'Dashboard' }, 
        { label: 'Data Return Pembelian' }, 
        { label: 'Tambah Data Return Pembelian' }, 
    ]);
    const router = useRouter()
    const toast = useToast()
    const returnPurchasedProducts = useReturnPurchasedProductsStore()
    const detail_products_returned = ref([])
    const apoteks = ref([])
    const suppliers = ref([])
    const ref_numbers = ref([])
    const loading = ref(false)
    const selectLoading = ref(false)
    const selectedSupplier = ref("")
    const qty = ref([])
    const batch_number = ref([])
    const expired_date_product = ref([])
    const totalQtyItems = ref(0)
    const totalNominalReturned = ref(0)
    const errorListProductNotSelected = ref(true)
    const filterType = ref('Nomor-REF')
    const selectedRefNumber = ref()

    onMounted(async () => {
      setNullStateValue()
      
      Promise.all([
        loadApoteks(),
        loadSuppliers()
      ])
    })

    const setListProductWantToReturn = async () => {
      loading.value = true
      if (selectedRefNumber.value.length > 0) {        
        await returnPurchasedProducts.getDetailProductWantToReturn(selectedRefNumber.value);
        detail_products_returned.value = returnPurchasedProducts.listDetailProductWantToReturn;
      } else {
        detail_products_returned.value = []
        returnPurchasedProducts.setNullDetailProductWantToReturn()        
      }

      let totalNominal = 0;
      let totalQty = 0;

      detail_products_returned.value.forEach(detail => {
        totalNominal += detail.grand_total;

        detail.ordered_product_details_for_return.forEach(product => {
          totalQty += product.product_detail.qty;
        })
      })

      totalNominalReturned.value = totalNominal;
      totalQtyItems.value = totalQty;
      loading.value = false
    }

    const loadApoteks = async () => {
      await returnPurchasedProducts.getListApoteks();
      apoteks.value = returnPurchasedProducts.listApoteks;
    }

    const loadSuppliers = async () => {
      await returnPurchasedProducts.getListSuppliers();
      suppliers.value = returnPurchasedProducts.listSuppliers;
    }

    const loadRefNumbers = async () => {
      selectLoading.value = true;
      const refNumbers = returnPurchasedProducts.refNumbers;      

      if (refNumbers.length === 0) {
        await returnPurchasedProducts.getListRefNumber();        
      }
      ref_numbers.value = returnPurchasedProducts.refNumbers;

      selectLoading.value = false;
    }

    const setNullStateValue = () => {
      returnPurchasedProducts.setNullRefNumbers()
      returnPurchasedProducts.setNullDetailProductWantToReturn()
    }

    const { handleSubmit, defineField, errors } = useForm({
      validationSchema: {
        return_reference_number: yup.string().nullable(),
        supplier_id: yup.string().required("Pemasok harus dipilih."),
        apotek_id: yup.string().required("Lokasi apotek harus dipilih."),
        return_date: yup.string().required("Tanggal return harus diisi."),
        return_status: yup.string().required("Status return harus dipilih."),
        return_note: yup.string().nullable(),
        additional_note: yup.string().nullable(),
      }
    })
    
    const [return_reference_number, returnReferenceNumberAttr] = defineField('return_reference_number');
    const [apotek_id, apotekIdAttr] = defineField('apotek_id');
    const [supplier_id, supplierIdAttr] = defineField('supplier_id');
    const [return_date, returnDateAttr] = defineField('return_date');
    const [return_status, returnStatusAttr] = defineField('return_status');
    const [return_note, returnNoteAttr] = defineField('return_note');
    const [additional_note, additionalNoteAttr] = defineField('additional_note');

    /**
     * Submits the returned product data after performing validations and processing.
     *
     * @function submitReturnedProduct
     * @param {Object} datas - The data object containing supplier_id, apotek_id, return_date, return_status, return_note, and additional_note.
     * @returns {Promise<void>}
     *
     * @description
     * This function handles the submission of returned products. It performs the following steps:
     * 1. Sets the loading state to true.
     * 2. Validates if at least one product is selected for return. If not, it shows an error toast and exits.
     * 3. Groups the product IDs from the returned products.
     * 4. Calculates the new stock quantities for each product.
     * 5. Constructs the data object for the return, including supplier information, product IDs, reference numbers, return date, status, total items, amount, and notes.
     * 6. Combines the return data with the new stock quantities.
     * 7. Sends the final data to the `AddReturnedProduct` API.
     * 8. Handles the response from the API:
     *    - If there is an error, it shows an error toast.
     *    - If successful, it resets the return purchased products list and navigates to the purchased product data page.
     *
     * @async
     */
    const submitReturnedProduct = handleSubmit(async (datas) => { 
      loading.value = true

      if (detail_products_returned.value.length === 0) {        
        errorListProductNotSelected.value = true
        toast.add({
          severity: 'error',
          summary: 'Error Submit Returned Product',
          detail: 'Wajib memilih minimal 1 produk.',
          life: 3000
        });
        loading.value = false
        return;
      }

      const returnedProducts = detail_products_returned.value
      
      const grouppedProductIds = returnedProducts.map(r => {
          let uniqueProductIds = new Map()
          r.ordered_product_details_for_return.forEach(d => {
            uniqueProductIds.set(d.product_id, d.product_id)
          })

          return Array.from(uniqueProductIds.keys());
        }).flat()
      
      const newStocks = returnedProducts.reduce((acc, product) => {
        product.ordered_product_details_for_return.forEach(detail => {
          const productId = detail.product_id;
          const qty = detail.product_detail.qty;

          if (acc[productId]) {
            acc[productId].stock += qty
          } else {
            acc[productId] = {
              stock: qty
            }
          }
        })
        
        return acc
      }, {})   

      const dataReturn = {
        return_reference_number: datas.return_reference_number ?? '',
        supplier_id: datas.supplier_id,
        apotek_id: datas.apotek_id,
        product_ids: [...new Set(grouppedProductIds)],
        ref_numbers: selectedRefNumber.value,
        return_date: datas.return_date,
        return_status: datas.return_status,
        total_returned_items: totalQtyItems.value,
        return_amount: totalNominalReturned.value,
        return_note: datas.return_note,
        additional_note: datas.additional_note,
      }   

      const finalData = {
        ...dataReturn,
        newStocks
      }
      
      await returnPurchasedProducts.AddReturnedProduct(finalData)

      if (returnPurchasedProducts.errorAddedData) {
        loading.value = false
        toast.add({
          severity: 'error',
          summary: 'Added Returned Product Failed',
          detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
          life: 3000
        });
      } else {
        loading.value = false
        returnPurchasedProducts.setNullListReturnPurchasedProducts()
        router.push({name: 'return-purchased-product.data-return-purchased'})
      }            
    })
    
    return {
      breadcrumbIcon,
      breadcrumbItems,
      detail_products_returned,
      suppliers,
      apoteks,
      loading,
      formatCurrencyIDR,
      selectedSupplier,
      errors,
      return_reference_number,
      returnReferenceNumberAttr,
      apotek_id,
      supplier_id,
      return_date,
      return_status,
      return_note,
      additional_note,
      apotekIdAttr,
      supplierIdAttr,
      returnDateAttr,
      returnStatusAttr,
      returnNoteAttr,
      additionalNoteAttr,
      qty,
      batch_number,
      expired_date_product,
      totalNominalReturned,
      totalQtyItems,
      submitReturnedProduct,
      errorListProductNotSelected,
      filterType,
      selectLoading,
      loadRefNumbers,
      selectedRefNumber,
      ref_numbers,
      setListProductWantToReturn
    }
  }
}
</script>

<style scoped>

</style>