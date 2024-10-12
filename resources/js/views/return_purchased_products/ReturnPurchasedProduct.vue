<template>
    <Content>
        <template v-slot:content>
            <Toast />
						<div>
                <Breadcrumb :home="breadcrumbIcon" :model="breadcrumbItems" class="!bg-transparent" />
            </div>
            <div class="filter-content bg-white rounded-md shadow-lg w-full card p-4 mb-4">
              <div class="header-filter-content">
                <div class="flex items-center space-x-1 !text-slate-600">
                  <i class="pi pi-filter" style="font-size: 1.2rem;"></i>
                  <p class="text-lg font-semibold">Filter</p>
                </div>
              </div>
              <hr class="mt-2 mb-2">
              <div class="grid grid-cols-4 gap-3 p-6">
                <div>
                  <label for="apotek" class="mb-2 block font-semibold text-slate-500">Lokasi Apotek</label>
                  <Select 
                    v-model="selectedFilterApotek"
                    :options="apoteks"
                    optionLabel="name_of_apotek"
                    optionValue="id"
                    placeholder="Filter Lokasi Apotek"
                    class="w-full"
                    @change="setFilterOption('apotek', selectedFilterApotek)"
                  />
                </div>
                <div>
                  <label for="supplier" class="mb-2 block font-semibold text-slate-500">Supplier</label>
                  <Select 
                    v-model="selectedFilterSupplier"
                    :options="suppliers"
                    optionLabel="supplier_name"
                    optionValue="id"
                    placeholder="Filter Lokasi Pemasok / Supplier"
                    class="w-full"
                    @change="setFilterOption('supplier', selectedFilterSupplier)"
                  />
                </div>
                <div>
                  <label for="return_status" class="mb-2 block font-semibold text-slate-500">Status Return</label>
                  <Select 
                    v-model="selectedFilterStatusReturn"
                    :options="[
                      { 'statusLabel': 'Pending' },
                      { 'statusLabel': 'Completed' },
                      { 'statusLabel': 'Rejected' }
                    ]"
                    optionLabel="statusLabel"
                    optionValue="statusLabel"
                    placeholder="Filter Status Return"
                    class="w-full"
                    @change="setFilterOption('return_status', selectedFilterStatusReturn)"
                  />
                </div>
                <div>
                  <label for="user" class="mb-2 block font-semibold text-slate-500">Pengguna (Karyawan)</label>
                  <Select 
                    v-model="selectedFilterUser"
                    :options="users"
                    optionLabel="name"
                    optionValue="name"
                    placeholder="Filter Pengguna"
                    class="w-full"
                    @change="setFilterOption('user', selectedFilterUser)"
                  />
                </div>
                <div>
                  <label for="return_date" class="mb-2 block font-semibold text-slate-500">Tanggal Return</label>
                  <DatePicker 
                    v-model="selectedFilterReturnDate"
                    selectionMode="range"
                    :manualInput="false"
                    showIcon
                    dateFormat="dd/mm/yy"
                    class="w-full"
                    placeholder="Filter Tanggal Return Pembelian"
                    @date-select="setFilterOption('return_date', selectedFilterReturnDate)"
                  />
                </div>
              </div>
            </div>
            <div
                class="main-content-purchased-product bg-white rounded-md shadow-lg w-full card p-4"
            >
                <span class="text-xl font-bold">Manajemen Return Pembelian Produk</span>
                <ContextMenu
                    ref="cm"
                    :model="menuModel"
                    @hide="selectedReturnPurchasedProduct = null"
                />
                <div v-if="return_purchased_products">
                    <DataTable
                        :value="return_purchased_products"
                        ref="dataTable"
                        dataKey="id"
                        :loading="loading"
                        :rows="rows"
                        :lazy="true"
                        tableStyle="min-width: 50rem;"
                        removableSort
                        showGridlines
                        resizableColumns
                        columnResizeMode="fit"
                        class="mt-4 custom-scrollbar"
                        contextMenu
                        @rowContextmenu="onRowContextMenu"
                        v-model:contextMenuSelection="selectedReturnPurchasedProduct"
                        v-model:expandedRows="expandedRows"
                        @rowExpand="onRowExpand" 
                        @rowCollapse="onRowCollapse"
                    >
                        <template #header>
                            <div
                                class="flex flex-wrap items-center justify-between gap-2"
                            >
                                <div class="flex items-center space-x-2">
                                    <Button
                                        icon="pi pi-file-excel"
                                        label="Excel"
                                        :loading="isExportingExcel"
                                        @click="exportExcel($event)"
                                    />
                                    <Button
                                        icon="pi pi-file-pdf"
                                        severity="danger"
                                        label="PDF"
                                        :loading="isExportingPDF"
                                        @click="exportPDF($event)"
                                    />
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-2">
                                        <Button
                                            type="button"
                                            icon="pi pi-filter-slash"
                                            label="Clear"
                                            outlined
                                            @click="clearFilter()"
                                        />
                                        <IconField>
                                            <InputIcon>
                                                <i class="pi pi-search" />
                                            </InputIcon>
                                            <InputText
                                                v-model="searchQuery"
                                                placeholder="Keyword pencarian"
                                            />
                                        </IconField>
                                    </div>
                                    <div>
                                      <router-link :to="{ name: 'return-purchased-product.add-data-return-purchased' }">
                                        <Button
                                            icon="pi pi-plus"
                                            raised
                                            label="Tambah Data"
                                            severity="info"
                                        />
                                      </router-link>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template #empty>Data return pembelian produk tidak tersedia.</template>
                        <template #loading>
                            <i
                                class="pi pi-spinner-dotted animate-spin"
                                style="font-size: 2rem"
                            ></i>
                        </template>
                        <Column
                            field="return_reference_number"
                            header="No. RET"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.return_reference_number }}
                            </template>
                        </Column>
                        <Column
                            field="supplier.supplier_name"
                            header="Supplier / Pemasok"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.supplier.supplier_name }}
                            </template>
                        </Column>
                        <Column
                            field="apotek.name_of_apotek"
                            header="Lokasi Apotek"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.apotek.name_of_apotek }}
                            </template>
                        </Column>
                        <Column
                            field="total_returned_items"
                            header="Total Item Return"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.total_returned_items }} Item
                            </template>
                        </Column>
                        <Column
                            field="return_amount"
                            header="Dana Return"
                        >
                            <template #body="slotProps">
                                {{ formatCurrencyIDR(slotProps.data.return_amount) }}
                            </template>
                        </Column>
                        <Column
                            field="return_date"
                            header="Tanggal Return"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.return_date }}
                            </template>
                        </Column>
                        <Column
                            field="return_status"
                            header="Status Return"
                        >
                          <template #body="slotProps">
                            <div class="text-center">
                              <Button
                                :severity="setColorInfoStatusReturn(slotProps.data.return_status)"
                                :label="setLabelStatusReturn(slotProps.data.return_status)"
                                size="small"
                                outlined
                              />
                            </div>
                          </template>
                        </Column>
                        <Column
                            field="action_by"
                            header="Ditambahkan oleh"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.action_by ?? '?' }}
                            </template>
                        </Column>
                        <Column
                            field="updated_by"
                            header="Diubah oleh"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.updated_by ?? "Belum Ada Perubahan" }}
                            </template>
                        </Column>
                    </DataTable>
                </div>
                <div v-else>
                    <DataTable
                        :value="return_purchased_products"
                        :totalRecords="totalRecords"
                        ref="dataTable"
                        :loading="loading"
                        class="mt-4"
                    >
                        <template #header>
                            <div
                                class="flex flex-wrap items-center justify-between gap-2"
                            >
                                <div class="flex items-center space-x-2">
                                    <Button
                                        icon="pi pi-file-excel"
                                        label="Excel"
                                        @click="exportExcel($event)"
                                    />
                                    <Button
                                        icon="pi pi-file-pdf"
                                        severity="danger"
                                        label="PDF"
                                        @click="exportPDF($event)"
                                    />
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-2">
                                        <Button
                                            type="button"
                                            icon="pi pi-filter-slash"
                                            label="Clear"
                                            outlined
                                            @click="clearFilter()"
                                        />
                                        <IconField>
                                            <InputIcon>
                                                <i class="pi pi-search" />
                                            </InputIcon>
                                            <InputText
                                                placeholder="Keyword pencarian"
                                            />
                                        </IconField>
                                    </div>
                                    <div>
                                        <router-link to="">
                                            <Button icon="pi pi-plus" raised />
                                        </router-link>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template #empty>Data return pembelian tidak tersedia.</template>
                        <template #loading>
                            <i
                                class="pi pi-spinner-dotted animate-spin"
                                style="font-size: 2rem"
                            ></i>
                        </template>
                    </DataTable>
                </div>
                <div class="btn-paginator mt-5  flex justify-between items-center">
                  <Button
                      icon="pi pi-arrow-left"
                      size="small"
                      label="Prev"
                      severity="contrast"
                      class="disabled:!cursor-not-allowed"
                      @click="getPrevPageContent"
                      :disabled="!prevPageUrl"
                  />
                  <Button
                      icon="pi pi-arrow-right"
                      size="small"
                      label="Next"
                      severity="contrast"
                      class="disabled:!cursor-not-allowed"
                      @click="getNextPageContent"
                      :disabled="!nextPageUrl"
                  />
                </div>
              </div>
              <Dialog
                header="Detail Return Pembelian Produk"
                v-model:visible="displayDetailReturnPurchasedProduct"
                :modal="true"
                :closable="true"
                :style="{ width: '50vw' }"
              >
                <div class="details">
                  <div v-for="d in detail_return_purchased_product.return_details" :key="d.id">
                    <div>
                      <router-link to="">
                        <Button
                          :label="d.reference_number"
                          icon="pi pi-tag"
                          size="small" 
                          class="hover:!bg-slate-700"
                          severity="contrast"
                        />
                      </router-link>
                      <div class="flex items-center space-x-2">
                        <div v-for="p in d.purchased_products_without_detail" :key="p.id" class="mt-2">
                          <div class="shadow-md p-4 w-fit">
                            <img :src="`/storage/product/${p.img_product}`" alt="product_image" loading="lazy" class="w-20 h-20 mx-auto" />
                            <div class="mt-2">
                              <p class="font-bold text-sm">{{ p.product_code }} - <span class="font-normal">{{ p.name }}</span></p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="detail_note mt-5">
                  <div>
                    <p class="font-semibold">Catatan Pengembalian :</p>
                    <Textarea
                      v-model="detail_return_purchased_product.return_note"
                      rows="5"
                      cols="50"
                      class="mt-2 read-only:!bg-slate-700 read-only:!text-white"
                      autoResize
                      readonly
                    />
                  </div>
                  <div>
                    <p class="font-semibold">Catatan Tambahan :</p>
                    <Textarea
                      v-model="detail_return_purchased_product.additional_note"
                      rows="5"
                      cols="50"
                      class="mt-2 read-only:!bg-slate-700 read-only:!text-white"
                      autoResize
                      readonly
                    />
                  </div>
                </div>
              </Dialog>
        </template>
    </Content>
</template>
<script>
import { ref, onMounted, watch, computed } from "vue";
import Content from "../../components/Layout/Content.vue";
import ContextMenu from "primevue/contextmenu";
import InputIcon from "primevue/inputicon";
import InputText from "primevue/inputtext";
import IconField from "primevue/iconfield";
import Dialog from "primevue/dialog";
import InputGroup from "primevue/inputgroup";
import InputGroupAddon from "primevue/inputgroupaddon";
import InputNumber from "primevue/inputnumber";
import DatePicker from "primevue/datepicker";
import FileUpload from "primevue/fileupload";
import Textarea from "primevue/textarea";
import { useReturnPurchasedProductsStore } from "../../stores/return_purchased_products";
import { useUserStore } from '../../stores/user';
import { formatCurrencyIDR } from "../../helpers/formatCurrency";
import { debounce } from "../../helpers/debounce";
import { useToast } from "primevue/usetoast";
import { useApotekStore } from "../../stores/apotek";

import { useRouter } from "vue-router";
import { useSupplierStore } from "../../stores/supplier";

export default {
    components: {
      Content,
      ContextMenu,
      InputIcon,
      InputText,
      IconField,
      Dialog,
      InputGroup,
      InputGroupAddon,
      InputNumber,
      DatePicker,
      FileUpload,
      Textarea,
    },
    setup() {
      const returnedPurchasedProduct = useReturnPurchasedProductsStore();
      const userStore = useUserStore();
      const apotekStore = useApotekStore();
      const supplierStore = useSupplierStore();
      const return_purchased_products = ref([]);
      const apoteks = ref([]);
      const suppliers = ref([]);
      const users = ref([]);
      const dataTable = ref([]);
      const nextPageUrl = ref('');
      const prevPageUrl = ref('');
      const loading = ref(false);
      const searchQuery = ref("");
      const rows = ref(1);
      const expandedRows = ref({});
      const rowsPerPageOptions = [10, 20, 50];
      const cm = ref(null);
      const isExportingExcel = ref(false);
      const isExportingPDF = ref(false);
      const selectedReturnPurchasedProduct = ref(null);
      const breadcrumbIcon = ref({
          icon: 'pi pi-chart-bar'
      })
      const breadcrumbItems = ref([
          { label: 'Dashboard' }, 
          { label: 'Data Return Pembelian' }, 
      ]);
      const toast = useToast();
      const POP_OVER_REF = ref()
      const STATUS_RETURN = ref([
        {
          'id': '1',
          'statusLabel': 'Proses',
          'value': 'Pending'
        },
        {
          'id': '2',
          'statusLabel': 'Dikirim',
          'value': 'Shipping'
        },
        {
          'id': '1',
          'statusLabel': 'Diterima',
          'value': 'Delivered'
        },
        {
          'id': '1',
          'statusLabel': 'Dibatalkan',
          'value': 'Cancelled'
        },
      ])
      const headers = ref([
        "No. RET",
        "Supplier / Pemasok",
        "Lokasi Apotek",
        "Total Item Return",
        "Dana Return",
        "Tanggal Return",
        "Status Return",
      ])
      const popoverStatusOrderData = ref(null)
      const selectedStatusOrder = ref(null)
      const selectedFilterApotek = ref(null)
      const selectedFilterSupplier = ref(null)
      const selectedFilterStatusReturn = ref('')
      const selectedFilterUser = ref('')
      const selectedFilterReturnDate = ref(null)
      const displayDetailReturnPurchasedProduct = ref(false)
      const router = useRouter()

      onMounted(async () => {
        clearFilterState()
        clearSelectedProduct()

        Promise.all([
          loadReturnPurchasedProducts(),
          loadApoteks(),
          loadSuppliers(),
          loadUsers()
        ])
      })

      const loadReturnPurchasedProducts = async (url) => {        
        loading.value = true;        

        await returnedPurchasedProduct.getListReturnPurchasedProducts(url);
        return_purchased_products.value = returnedPurchasedProduct.listReturnPurchasedProducts;
        nextPageUrl.value = returnedPurchasedProduct.NEXT_PAGE_URL;
        prevPageUrl.value = returnedPurchasedProduct.PREV_PAGE_URL;

        loading.value = false;
      };

      const loadApoteks = async () => {
        await apotekStore.getListApotekBySpecificColumn()
        apoteks.value = apotekStore.listApoteks
      }

      const loadSuppliers = async () => {
        await supplierStore.getListSupplierBySpecificColumn()
        suppliers.value = supplierStore.listSuppliers
      }

      const loadUsers = async () => {
        await userStore.getListNameOfUser()
        users.value = userStore.listUsers
      }

      const setFilterOption = debounce((field, value) => {
        if (field === 'return_date') {          
          const startDate = {
            'date':  value[0] ? new Date(value[0]).toLocaleDateString('en-CA') : '',
          }

          const endDate = {
            'date':  value[1] ? new Date(value[1]).toLocaleDateString('en-CA') : '',
          }
          
          returnedPurchasedProduct.setFilter('start_date', startDate.date)
          returnedPurchasedProduct.setFilter('end_date', endDate.date)

          if (startDate.date && endDate.date) {
            loadReturnPurchasedProducts()
          }
        } else {
          returnedPurchasedProduct.setFilter(field, value)
          loadReturnPurchasedProducts()
        }                
      }, 300)

      const getNextPageContent = async () => {
        const url = nextPageUrl.value;
        if (nextPageUrl) {
          await loadReturnPurchasedProducts(url);
        }
      }

      const getPrevPageContent = async () => {
        const url = prevPageUrl.value;
        if (prevPageUrl) {
          await loadReturnPurchasedProducts(url);
        }
      }

      const clearSelectedProduct = () => {
        // returnedPurchasedProduct.setNullListProductSelected()
        // returnedPurchasedProduct.setNullProductIds()
      }

      const clearFilterState = () => {
        returnedPurchasedProduct.setNullFilters()
      }

      const clearFilter = () => {
        searchQuery.value = ''
      };

      const debouncedSearch = debounce((value) => {
            returnedPurchasedProduct.setFilter('search', value)
            loadReturnPurchasedProducts()
      }, 500)

      watch(searchQuery, (newQuery, oldQuery) => {
          if (newQuery !== oldQuery && newQuery.trim() !== '') {
              debouncedSearch(newQuery);
          }
      })

      const exportExcel = async () => {
        isExportingExcel.value = true
        const { utils, writeFileXLSX } = await import("xlsx");

        if (dataTable.value) {
            const date = new Date()

            const data = return_purchased_products.value.map((rp) => ({
                "No. RET": rp.return_reference_number,
                "Supplier / Pemasok": rp.supplier.supplier_name,
                "Lokasi Apotek": rp.apotek.name_of_apotek,
                "Total Item Return": rp.total_returned_items + " Item",
                "Dana Return": formatCurrencyIDR(rp.return_amount),
                "Tanggal Return": rp.return_date,
                "Status Return": rp.return_status,
            }));

            const worksheetData = [
                headers.value,
                ...data.map((item) =>
                    headers.value.map((header) => item[header])
                ),
            ];

            const worksheet = utils.aoa_to_sheet(worksheetData)

            const calculateColumnWidths = (data) => {
                return data[0].map((_, colIndex) => {
                    const colValues = data.map(row => row[colIndex] || '');
                    const maxLength = Math.max(...colValues.map(val => val.toString().length));
                    return { wch: maxLength + 2 };
                });
            };

            worksheet['!cols'] = calculateColumnWidths(worksheetData);
            const workbook = utils.book_new();
            utils.book_append_sheet(workbook, worksheet, "Data");

            writeFileXLSX(workbook, `DataReturnOrderProduk_${date.getDate()}_${date.getMonth() + 1}_${date.getFullYear()}.xlsx`);
            isExportingExcel.value = false
        } else {
            isExportingExcel.value = false
            console.log(dataTable.value, "Datatable ref is null");
        }
      };
        
      const exportPDF = async () => {
        isExportingPDF.value = true
        const { default: jsPDF } = await import("jspdf")
        const { default: autoTable } = await import("jspdf-autotable")
        
        if (dataTable.value) {
            const date = new Date()
            const doc = new jsPDF()
            const data = return_purchased_products.value.map((rp) => ({
                "No. RET": rp.return_reference_number,
                "Supplier / Pemasok": rp.supplier.supplier_name,
                "Lokasi Apotek": rp.apotek.name_of_apotek,
                "Total Item Return": rp.total_returned_items + " Item",
                "Dana Return": formatCurrencyIDR(rp.return_amount),
                "Tanggal Return": rp.return_date,
                "Status Return": rp.return_status,
            }));

            const tableData = data.map(item => headers.value.map(header => item[header]))

            autoTable(doc, {
              head: [headers.value],
              body: tableData,
              styles: {
                fontSize: 8,
              }
            })

            doc.save(`DataReturnOrderProduk_${date.getDate()}_${date.getMonth() + 1}_${date.getFullYear()}.pdf`)
            isExportingPDF.value = false
        } else {
            isExportingPDF.value = false
            console.log(dataTable.value, "Datatable ref is null");
        }
      };

      const menuModel = computed(() => [
        {
            label: "Detail",
            icon: "pi pi-fw pi-eye",
            command: () => displayDetailReturnPurchasedProduct.value = true,
        },
        {
            label: "Edit",
            icon: "pi pi-pencil",
            command: () => router.push({
              name: "return-purchased-product.edit-data-return-purchased",
              params: { return_reference_number: returnedPurchasedProduct.detailReturnPurchasedProduct.return_reference_number }
            }),
        },
      ]);

      const onRowContextMenu = (event) => {
        returnedPurchasedProduct.setSelectedReturnPurchasedProduct(event.data);

        cm.value.show(event.originalEvent);
      };

      const onRowExpand = (event) => {
        expandedRows.value[event.data.id] = true;
      };

      const onRowCollapse = (event) => {
        delete expandedRows.value[event.data.id];
      };

      const setColorInfoStatusReturn = (status) => {
        switch (status) {
          case 'Pending':
            return 'warn'
          case 'Completed':
            return 'success'
          case 'Rejected':
            return 'danger'
          default:
            break;
        }
      }

      const setLabelStatusReturn = (status) => {
        switch (status) {
          case 'Pending':
            return 'Pending'
          case 'Completed':
            return 'Completed'
          case 'Rejected':
            return 'Rejected'
          default:
            break;
        }
      }

      const handleChangeStatusOrder = async () => {
        loading.value = true
        const statusOrder = selectedStatusOrder.value
        const orderId = popoverStatusOrderData.value.id

        await returnedPurchasedProduct.changeStatusOrder(statusOrder, orderId)

        if (returnedPurchasedProduct.errorSavedPayment) {
          loading.value = false
          toast.add({
            severity: "error",
            summary: "Change Status Order Failed",
            detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
            life: 3000,
          });
        } else {
          POP_OVER_REF.value.hide()
          loading.value = false
          selectedStatusOrder.value = null
          toast.add({
            severity: "success",
            summary: "Change Status Order Successfully",
            detail: "Status Order Berhasil Diubah",
            life: 3000,
          });
        } 
      }
  
      return {
        return_purchased_products,
        detail_return_purchased_product: computed(() => returnedPurchasedProduct.detailReturnPurchasedProduct),
        apoteks,
        suppliers,
        users,
        getNextPageContent,
        getPrevPageContent,
        nextPageUrl,
        prevPageUrl,
        loading,
        searchQuery,
        rows,
        rowsPerPageOptions,
        isExportingExcel,
        isExportingPDF,
        dataTable,
        selectedReturnPurchasedProduct,
        formatCurrencyIDR,
        breadcrumbIcon,
        breadcrumbItems,
        clearFilter,
        exportExcel,
        exportPDF,
        menuModel,
        expandedRows,
        onRowContextMenu,
        onRowExpand,
        onRowCollapse,
        setColorInfoStatusReturn,
        setLabelStatusReturn,
        cm,
        POP_OVER_REF,
        STATUS_RETURN,
        selectedStatusOrder,
        popoverStatusOrderData,
        handleChangeStatusOrder,
        selectedFilterApotek,
        selectedFilterSupplier,
        selectedFilterStatusReturn,
        selectedFilterUser,
        selectedFilterReturnDate,
        setFilterOption,
        displayDetailReturnPurchasedProduct
      }
    }
};
</script>
<style scoped>

</style>