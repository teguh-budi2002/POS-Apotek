<template>
    <Content>
        <template v-slot:content>
            <Toast />
						<div>
                <Breadcrumb :home="breadcrumbIcon" :model="breadcrumbItems" />
            </div>
            <div
                class="main-content-purchased-product bg-white rounded-md shadow-md w-full card p-4"
            >
                <span class="text-xl font-bold">Manajemen Order Pembelian Produk</span>
                <ContextMenu
                    ref="cm"
                    :model="menuModel"
                    @hide="selectedPurchasedProduct = null"
                />
                <div v-if="purchased_products">
                    <DataTable
                        :value="purchased_products"
                        :totalRecords="totalRecords"
                        ref="dataTable"
                        dataKey="id"
                        paginator
                        @page="onPage"
                        @rows-change="onRowsChange"
                        :loading="loading"
                        :rows="rows"
                        :lazy="true"
                        :rowsPerPageOptions="rowsPerPageOptions"
                        tableStyle="min-width: 50rem;"
                        removableSort
                        showGridlines
                        resizableColumns
                        columnResizeMode="fit"
                        class="mt-4"
                        contextMenu
                        @rowContextmenu="onRowContextMenu"
                        v-model:contextMenuSelection="selectedPurchasedProduct"
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
                                        :loading="isExporting"
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
                                                v-model="searchQuery"
                                                placeholder="Keyword pencarian"
                                            />
                                        </IconField>
                                    </div>
                                    <div>
                                      <router-link :to="{ name: 'purchased-product.add-data-purchased' }">
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

                        <template #empty>Data Pembelian Produk tidak tersedia.</template>
                        <template #loading>
                            <i
                                class="pi pi-spinner-dotted animate-spin"
                                style="font-size: 2rem"
                            ></i>
                        </template>
                        <Column expander style="width: 5rem" />
                        <Column
                            field="reference_number"
                            header="Nomor REF"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.reference_number }}
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
                            field="supplier.supplier_name"
                            header="Supplier"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.supplier.supplier_name }}
                            </template>
                        </Column>
                        <Column
                            field="shipping_cost"
                            header="Biaya Pengiriman"
                        >
                            <template #body="slotProps">
                              <div v-if="slotProps.data.shipping_cost !== null">
                                {{ formatCurrencyIDR(slotProps.data.shipping_cost) }}
                              </div>
                              <div>
                                Rp 0
                              </div>
                            </template>
                        </Column>
                        <Column
                            field="grand_total"
                            header="Grand Total"
                        >
                            <template #body="slotProps">
                                {{ formatCurrencyIDR(slotProps.data.grand_total) }}
                            </template>
                        </Column>
                        <Column
                            field="order_date"
                            header="Tanggal Order"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.order_date }}
                            </template>
                        </Column>
                        <Column
                            field="status_order"
                            header="Status Order"
                        >
                            <template #body="slotProps">
                              <div class="text-center">
                                <Button
                                  :severity="setColorInfoStatusOrder(slotProps.data.status_order)"
                                  :label="slotProps.data.status_order"
                                  size="small"
                                />
                              </div>
                            </template>
                        </Column>
                        <Column
                            field="status_payment"
                            header="Status Pembayaran"
                        >
                            <template #body="slotProps">
                              <div class="text-center">
                                <Button
                                  :severity="setColorStatusPayment(slotProps.data.status_payment)"
                                  :label="setLabelStatusPayment(slotProps.data.status_payment)"
                                  size="small"
                                />
                              </div>
                            </template>
                        </Column>
                        <Column
                            field="action_by"
                            header="Ditambah oleh"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.action_by }}
                            </template>
                        </Column>
                        <template #expansion="slotProps">
                          <div class="p-2">
                            <p class="mb-4">Detail Produk Yang Dibeli | <span class="font-semibold">{{ slotProps.data.reference_number }}</span></p>
                            <DataTable :value="slotProps.data.purchased_products">
                              <Column header="#">
                                <template #body="slotProps">
                                  {{ slotProps.index + 1 }}
                                </template>
                              </Column>
                              <Column field="name" header="Nama Produk">
                                <template #body="slotProps">
                                   {{ slotProps.data.product_code }} - {{ slotProps.data.name }}
                                </template>
                              </Column>
                              <Column field="product_detail.qty" header="Jumlah"></Column>
                              <Column field="unit_product_name" header="Satuan"></Column>
                              <Column field="unit_price" header="Harga Modal">
                                <template #body="slotProps">
                                  {{ formatCurrencyIDR(slotProps.data.unit_price) }}
                                </template>
                              </Column>
                              <Column field="product_detail.tax" header="Pajak">
                                <template #body="slotProps">
                                  {{ slotProps.data.product_detail.tax }}%
                                </template>
                              </Column>
                              <Column field="product_detail.discount" header="Diskon">
                                <template #body="slotProps">
                                  {{ slotProps.data.product_detail.discount }}%
                                </template>
                              </Column>
                              <Column field="product_detail.price_after_discount" header="Harga Modal (Setelah Diskon)">
                                <template #body="slotProps">
                                  {{ formatCurrencyIDR(slotProps.data.product_detail.price_after_discount) }}
                                </template>
                              </Column>
                              <Column field="product_detail.profit_margin" header="Profit Margin">
                                <template #body="slotProps">
                                  {{ slotProps.data.product_detail.profit_margin.toFixed(2) }}%
                                </template>
                              </Column>
                              <Column field="product_detail.selling_price" header="Harga Jual">
                                <template #body="slotProps">
                                  {{ formatCurrencyIDR(slotProps.data.product_detail.selling_price) }}
                                </template>
                              </Column>
                              <Column field="product_detail.sub_total" header="Sub Total">
                                <template #body="slotProps">
                                  {{ formatCurrencyIDR(slotProps.data.product_detail.sub_total) }}
                                </template>
                              </Column>
                              <Column field="product_detail.expired_date_product" header="Tanggal Kadaluarsa">
                                <template #body="slotProps">
                                  {{ slotProps.data.product_detail.expired_date_product }}
                                </template>
                              </Column>
                            </DataTable>
                          </div>
                        </template>
                    </DataTable>
                </div>
                <div v-else>
                    <DataTable
                        :value="purchased_products"
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

                        <template #empty>Data Pembelian Produk tidak tersedia.</template>
                        <template #loading>
                            <i
                                class="pi pi-spinner-dotted animate-spin"
                                style="font-size: 2rem"
                            ></i>
                        </template>
                    </DataTable>
                </div>
              </div>
              <Dialog
                  header="Detail Pembelian Produk"
                  v-model:visible="displayDetailPurchaseProduct"
                  :modal="true"
                  :style="{ width: '80rem' }"
                  class="custom-scrollbar"
              >
                <div class="grid grid-cols-3 gap-5">
                  <div class="shadow-md p-3">
                    <p class="text-lg font-bold uppercase text-slate-600">Pemasok / Supplier</p>
                    <hr class="my-2">
                    <p class="font-semibold text-slate-600">{{ detailPurchasedProduct.supplier.supplier_name }}</p>
                    <p class="text-sm text-slate-500">{{ detailPurchasedProduct.supplier.address }}</p>
                    <p class="text-sm text-slate-500">+{{ detailPurchasedProduct.supplier.contact_phone }}</p>
                  </div>
                  <div class="shadow-md p-3">
                    <p class="text-lg font-bold uppercase text-slate-600">Lokasi Apotek</p>
                    <hr class="my-2">
                    <p class="font-semibold text-slate-600">{{ detailPurchasedProduct.apotek.name_of_apotek }}</p>
                    <p class="text-sm text-slate-500">{{ detailPurchasedProduct.apotek.address }}</p>
                    <p class="text-sm text-slate-500">+{{ detailPurchasedProduct.apotek.contact_phone }}</p>
                  </div>
                  <div class="shadow-md p-3">
                    <p class="font-semibold text-slate-600">Nomor REF: <span class="text-slate-500 font-normal">{{ detailPurchasedProduct.reference_number }}</span></p>
                    <p class="font-semibold text-slate-600">Tanggal: <span class="text-slate-500 font-normal">{{ detailPurchasedProduct.order_date }}</span></p>
                    <p class="font-semibold text-slate-600">Status Pembelian: <span class="text-slate-500 font-normal">{{ detailPurchasedProduct.status_order }}</span></p>
                    <p class="font-semibold text-slate-600">Status Pembayaran: <span class="text-slate-500 font-normal">{{ setLabelStatusPayment(detailPurchasedProduct.status_payment) }}</span></p>
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-5 mt-3">
                  <div class="payment_information shadow-md p-3">
                    <div class="max-w-full max-h-56 overflow-x-auto custom-scrollbar mx-auto">
                      <table class="w-full table-auto">
                        <thead class="bg-slate-200">
                          <tr>
                            <th class="p-2">
                              <p class="text-slate-700 font-semibold text-sm whitespace-nowrap">#</p>
                            </th>
                            <th class="p-2">
                              <p class="text-slate-700 font-semibold text-sm whitespace-nowrap">Metode Pembayaran</p>
                            </th>
                            <th class="p-2">
                              <p class="text-slate-700 font-semibold text-sm whitespace-nowrap">Jumlah Dibayar</p>
                            </th>
                            <th class="p-2">
                              <p class="text-slate-700 font-semibold text-sm whitespace-nowrap">Dibayar Pada</p>
                            </th>
                            <th class="p-2">
                              <p class="text-slate-700 font-semibold text-sm whitespace-nowrap">Catatan Pembayaran</p>
                            </th>
                          </tr>
                        </thead>
                        <tbody class="bg-gray-50">
                          <tr>
                            <td class="p-2">
                              <p class="text-slate-900 font-semibold text-center text-sm whitespace-nowrap">1</p>
                            </td>
                            <td class="p-2">
                              <p class="text-slate-900 font-semibold text-center text-sm whitespace-nowrap">{{ detailPurchasedProduct.payment_method }}</p>
                            </td>
                            <td class="p-2">
                              <p class="text-slate-900 font-semibold text-center text-sm whitespace-nowrap">{{ formatCurrencyIDR(detailPurchasedProduct.cash_paid) }}</p>
                            </td>
                            <td class="p-2">
                              <p class="text-slate-900 font-semibold text-center text-sm whitespace-nowrap">{{ formatCurrencyIDR(detailPurchasedProduct.paid_on) }}</p>
                            </td>
                            <td class="p-2">
                              <p class="text-slate-900 font-semibold text-center text-sm whitespace-nowrap">{{ setLabelStatusPayment(detailPurchasedProduct.status_payment) }}</p>
                            </td>
                          </tr>
                         </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="shadow-md p-3">
                    <p class="text-lg font-bold uppercase text-slate-600">Detail Transaksi</p>
                    <div class="grid grid-cols-2">
                      <div>
                         <p class="font-semibold text-slate-600">Metode Pembayaran</p>
                         <p class="font-semibold text-slate-600">Biaya Pengiriman</p>                     
                         <p class="font-semibold text-slate-600">Jumlah Total Item</p>
                         <p class="font-semibold text-slate-600">Satuan</p>
                         <p class="font-semibold text-slate-600">Jumlah Total Bersih</p>
                      </div>
                      <div>
                        <p class="text-slate-500 font-normal">: {{ detailPurchasedProduct.payment_method }}</p>
                        <p class="text-slate-500 font-normal">: {{ detailPurchasedProduct.shipping_cost ?? 'Rp 0' }}</p>
                        <p class="text-slate-500 font-normal">: {{ countTotalItemsPerOrder() }}</p>
                        <p class="text-slate-500 font-normal">: {{ setLabelUnitProduct() }}</p>
                        <p class="text-slate-500 font-normal">: {{ formatCurrencyIDR(detailPurchasedProduct.grand_total) }}</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="shadow-md p-3">
                  <p class="text-lg font-bold uppercase text-slate-600">Catatan</p>
                  <div class="grid grid-cols-2">
                    <div>
                      <p class="font-semibold text-slate-600">Catatan Pengiriman</p>
                      <p class="font-semibold text-slate-600">Catatan Pembelian</p>
                    </div>
                    <div>
                      <p class="text-slate-500 font-normal">: {{ detailPurchasedProduct.shipping_details ?? 'Tidak Ada' }}</p>
                      <p class="text-slate-500 font-normal">: {{ detailPurchasedProduct.order_note ?? 'Tidak Ada' }}</p>
                    </div>
                  </div>
                </div>
                <div class="shadow-md p-3">
                  <p class="text-lg font-bold uppercase text-slate-600">Riwayat Aktivitas</p>
                  <div class="grid grid-cols-2">
                    <div>
                      <p class="font-semibold text-slate-600">Dibuat Oleh</p>
                      <p class="font-semibold text-slate-600">Diubah Oleh</p>
                    </div>
                    <div>
                      <p class="text-slate-500 font-normal">: {{ detailPurchasedProduct.action_by }}</p>
                      <p class="text-slate-500 font-normal">: </p>
                    </div>
                  </div>
                </div>
                <template #footer>
                  <Button
                    label="Tutup"
                    severity="secondary"
                    @click="displayDetailPurchaseProduct = false"
                    autofocus
                  />
              </template>
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
import { formatCurrencyIDR } from "../../helpers/formatCurrency";
import { debounce } from "../../helpers/debounce";
import { useToast } from "primevue/usetoast";
import { usePurchasedProductStore } from '../../stores/purchased_product';

export default {
    components: {
      Content,
      ContextMenu,
      InputIcon,
      InputText,
      IconField,
      Dialog
    },
    setup() {
      const purchasedProductStore = usePurchasedProductStore();
      const purchased_products = ref([]);
      const totalRecords = ref(0);
      const loading = ref(false);
      const searchQuery = ref("");
      const rows = ref(10);
      const expandedRows = ref({});
      const rowsPerPageOptions = [10, 20, 50];
      const cm = ref(null);
      const isExporting = ref(false);
      const selectedPurchasedProduct = ref(null);
      const breadcrumbIcon = ref({
          icon: 'pi pi-chart-bar'
      })
      const breadcrumbItems = ref([
          { label: 'Dashboard' }, 
          { label: 'Data Pembelian' }, 
      ]);
      const displayDetailPurchaseProduct = ref(false);

      onMounted(async () => {
        await loadPurchasedProducts()
        console.log(purchased_products.value);
      })

      const loadPurchasedProducts = async (rowsPerPage = rows.value) => {
        loading.value = true;

        await purchasedProductStore.getListOrderPurchasedProduct(rowsPerPage);
        purchased_products.value = purchasedProductStore.listOrderPurchasedProduct;
        totalRecords.value = purchasedProductStore.totalRecords;

        loading.value = false;
      };

      const countTotalItemsPerOrder = () => {
        let total = 0;
        purchasedProductStore.detailPurchasedProduct.purchased_products.forEach((product) => {          
          total += product.product_detail.qty
        })

        return total
      }

      const clearFilter = () => {
        searchQuery.value = ''
      };

      const onPage = (e) => {
        loadPurchasedProducts(e.rows)
      };

      const onRowsChange = (event) => {
        rows.value = event.rows;
        loadPurchasedProducts(event.rows);
      };

      const debouncedSearch = debounce((value) => {
            purchasedProductStore.setFilter('search', value)
            loadPurchasedProducts()
        }, 500)

      watch(searchQuery, (newQuery, oldQuery) => {
          if (newQuery !== oldQuery) {
              debouncedSearch(newQuery);
          }
      })

      const exportExcel = async () => {
        isExporting.value = true
        const { utils, writeFileXLSX } = await import("xlsx");

        if (dataTable.value) {
            const date = new Date()

            const data = purchased_products.value.map((purch_product) => ({
                "Nama Satuan": purch_product.name,
            }));

            const worksheetData = [
                headers.value,
                ...data.map((item) =>
                    headers.value.map((header) => item[header])
                ),
            ];

            const worksheet = utils.aoa_to_sheet(worksheetData)
            const workbook = utils.book_new();
            utils.book_append_sheet(workbook, worksheet, "Data");

            writeFileXLSX(workbook, `DataOrderProduk_${date.getDate()}_${date.getMonth() + 1}_${date.getFullYear()}.xlsx`);
            isExporting.value = false
        } else {
            isExporting.value = false
            console.log(dataTable.value, "Datatable ref is null");
        }
      };
        
      const exportPDF = async () => {
        isExporting.value = true
        const { default: jsPDF } = await import("jspdf")
        const { default: autoTable } = await import("jspdf-autotable")
        
        if (dataTable.value) {
            const date = new Date()
            const doc = new jsPDF()
            const data = purchased_products.value.map((purch_product) => ({
                "Nama Satuan": purch_product.name,
            }));

            const tableData = data.map(item => headers.value.map(header => item[header]))

            autoTable(doc, {
                head: [headers.value],
                body: tableData
            })

            doc.save(`DataOrderProduk_${date.getDate()}_${date.getMonth() + 1}_${date.getFullYear()}.pdf`)
            isExporting.value = false
        } else {
            isExporting.value = false
            console.log(dataTable.value, "Datatable ref is null");
        }
      };

      const menuModel = computed(() => [
        {
            label: "Detail",
            icon: "pi pi-fw pi-eye",
            command: () => displayDetailPurchaseProduct.value = true,
        },
      ]);

      const onRowContextMenu = (event) => {
        purchasedProductStore.setSelectedPurchasedProduct(event.data);
        cm.value.show(event.originalEvent);
      };

      const onRowExpand = (event) => {
        expandedRows.value[event.data.id] = true;
      };

      const onRowCollapse = (event) => {
        delete expandedRows.value[event.data.id];
      };

      const setColorInfoStatusOrder = (status) => {
        switch (status) {
          case 'Pending':
            return 'warn'
          case 'Shipping':
            return 'info'
          case 'Delivered':
            return 'success'
          case 'Cancelled':
            return 'danger'
          default:
            break;
        }
      }

      const setColorStatusPayment = (status) => {
        switch (status) {
          case 'Paid':
            return 'success'
          case 'Due':
            return 'warn'
          case 'Late':
            return 'danger'
          default:
            break;
        }
      }

      const setLabelStatusPayment = (status) => {
        switch (status) {
          case 'Paid':
            return 'Sudah Dibayar'
          case 'Due':
            return 'Tempo'
          case 'Late':
            return 'Terlambat'
          default:
            break;
        }
      }

      const setLabelUnitProduct = () => {
        let unitProductLabel = [];
        purchasedProductStore.detailPurchasedProduct.purchased_products.forEach((product) => {          
          unitProductLabel.push(product.unit_product_name)
        })

        return unitProductLabel.join(", ")
      }

      const showDetailPurchasedProduct = (purchasedProduct) => {
        selectedPurchasedProduct.value = purchasedProduct;
        console.log(selectedPurchasedProduct.value, purchasedProduct);
        
        displayDetailPurchaseProduct.value = true;
      }
  
      return {
        purchased_products,
        detailPurchasedProduct: computed(() => purchasedProductStore.detailPurchasedProduct),
        totalRecords,
        loading,
        searchQuery,
        rows,
        rowsPerPageOptions,
        isExporting,
        selectedPurchasedProduct,
        formatCurrencyIDR,
        breadcrumbIcon,
        breadcrumbItems,
        clearFilter,
        onPage,
        exportExcel,
        exportPDF,
        onRowsChange,
        menuModel,
        expandedRows,
        onRowContextMenu,
        onRowExpand,
        onRowCollapse,
        setColorInfoStatusOrder,
        setColorStatusPayment,
        setLabelStatusPayment,
        setLabelUnitProduct,
        cm,
        displayDetailPurchaseProduct,
        countTotalItemsPerOrder
      }
    }
};
</script>