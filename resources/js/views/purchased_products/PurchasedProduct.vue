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
                  <label for="status_order" class="mb-2 block font-semibold text-slate-500">Status Order</label>
                  <Select 
                    v-model="selectedFilterStatusOrder"
                    :options="[
                      { 'statusLabel': 'Proses', 'value': 'Pending' },
                      { 'statusLabel': 'Dikirim', 'value': 'Shipping' },
                      { 'statusLabel': 'Diterima', 'value': 'Delivered' },
                      { 'statusLabel': 'Dibatalkan', 'value': 'Cancelled' }
                    ]"
                    optionLabel="statusLabel"
                    optionValue="value"
                    placeholder="Filter Status Order"
                    class="w-full"
                    @change="setFilterOption('status_order', selectedFilterStatusOrder)"
                  />
                </div>
                <div>
                  <label for="status_payment" class="mb-2 block font-semibold text-slate-500">Status Pembayaran</label>
                  <Select 
                    v-model="selectedFilterStatusPayment"
                    :options="[
                      { 'statusLabel': 'Belum Lunas', 'value': 'Due' },
                      { 'statusLabel': 'Lunas', 'value': 'Paid' },
                      { 'statusLabel': 'Terlambat', 'value': 'Late' }
                    ]"
                    optionLabel="statusLabel"
                    optionValue="value"
                    placeholder="Filter Status Pembayaran"
                    class="w-full"
                    @change="setFilterOption('status_payment', selectedFilterStatusPayment)"
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
                  <label for="order_date" class="mb-2 block font-semibold text-slate-500">Tanggal Order</label>
                  <DatePicker 
                    v-model="selectedFilterOrderDate"
                    selectionMode="range"
                    :manualInput="false"
                    showIcon
                    dateFormat="dd/mm/yy"
                    class="w-full"
                    placeholder="Filter Tanggal Order"
                    @date-select="setFilterOption('order_date', selectedFilterOrderDate)"
                  />
                </div>
              </div>
            </div>
            <div
                class="main-content-purchased-product bg-white rounded-md shadow-lg w-full card p-4"
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
                                        :loading="isExportingExcel"
                                        @click="exportExcel($event)"
                                    />
                                    <Button
                                        icon="pi pi-file-pdf"
                                        severity="danger"
                                        :loading="isExportingPDF"
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
                            field="purchase_invoice"
                            header="Faktur Pembelian"
                        >
                            <template #body="slotProps">
                                <Image 
                                  :src="`/storage/purchase-product/invoice/${slotProps.data.purchase_invoice}`" 
                                  alt="Purchase Invoice Image" 
                                  width="100" 
                                  preview
                                />
                            </template>
                        </Column>
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
                                {{ slotProps.data.shipping_cost !== 0 ? formatCurrencyIDR(slotProps.data.shipping_cost) : 'Rp 0' }}
                              </div>
                              <div v-else>
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
                                :label="setLabelStatusOrder(slotProps.data.status_order)"
                                size="small"
                                outlined
                                @click="showPopoverStatusOrder($event, slotProps.data.id, slotProps.data.reference_number)"
                              />
                            </div>
                            <Popover ref="POP_OVER_REF" appendTo="body">
                              <div class="flex flex-col gap-4 w-[25rem]">
                                <p>Nomor: <b>{{ popoverStatusOrderData.ref_numb }}</b></p>
                                <div>
                                  <div class="flex items-center space-x-2 mb-2">
                                    <p class="font-medium">Edit Status Order</p>
                                    <i class="pi pi-truck"></i>
                                  </div>
                                  <div class="flex items-center space-x-2">
                                    <div v-for="status in STATUS_ORDER" :key="status.id">
                                      <Button 
                                        :label="status.statusLabel" 
                                        :value="status.value" 
                                        :severity="setColorInfoStatusOrder(status.value)"
                                        :outlined="selectedStatusOrder !== status.value"
                                        @click="selectedStatusOrder = status.value" 
                                      />
                                    </div>
                                  </div>  
                                </div>
                                <div>
                                    <Button 
                                      @click="handleChangeStatusOrder" 
                                      size="small" 
                                      severity="success" 
                                      raised 
                                      label="Ubah Status Order"
                                      class="disabled:!cursor-not-allowed"
                                      :disabled="selectedStatusOrder === null" 
                                    />
                                </div>
                              </div>
                            </Popover>
                          </template>
                        </Column>
                        <Column
                            field="payment.status_payment"
                            header="Status Pembayaran"
                        >
                            <template #body="slotProps">
                              <div class="text-center">
                                <Button
                                  :severity="setColorStatusPayment(slotProps.data.payment.status_payment)"
                                  :label="setLabelStatusPayment(slotProps.data.payment.status_payment)"
                                  size="small"
                                />
                              </div>
                            </template>
                        </Column>
                        <Column
                            field="action_by"
                            header="Ditambahkan oleh"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.action_by }}
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
                              <Column field="product_detail.batch_number" header="Batch Number">
                                <template #body="slotProps">
                                  {{ slotProps.data.product_detail.batch_number }}
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
                    <div class="space-y-2">
                      <p class="font-semibold text-slate-600">{{ detailPurchasedProduct.supplier.supplier_name }}</p>
                      <p class="text-sm text-slate-500">{{ detailPurchasedProduct.supplier.address }}</p>
                      <p class="text-sm text-slate-500">+{{ detailPurchasedProduct.supplier.contact_phone }}</p>
                      <p class="text-sm text-slate-500">{{ detailPurchasedProduct.supplier.email }}</p>
                    </div>
                  </div>
                  <div class="shadow-md p-3">
                    <p class="text-lg font-bold uppercase text-slate-600">Lokasi Apotek</p>
                    <hr class="my-2">
                    <div class="space-y-2">
                      <p class="font-semibold text-slate-600">{{ detailPurchasedProduct.apotek.name_of_apotek }}</p>
                      <p class="text-sm text-slate-500">{{ detailPurchasedProduct.apotek.address }}</p>
                      <p class="text-sm text-slate-500">+{{ detailPurchasedProduct.apotek.contact_phone }}</p>
                    </div>
                  </div>
                  <div class="shadow-md p-3">
                    <p class="font-semibold text-slate-600">Nomor REF: <span class="text-slate-500 font-normal">{{ detailPurchasedProduct.reference_number }}</span></p>
                    <p class="font-semibold text-slate-600">Tanggal: <span class="text-slate-500 font-normal">{{ detailPurchasedProduct.order_date }}</span></p>
                    <p class="font-semibold text-slate-600">Status Pembelian: <span class="text-slate-500 font-normal">{{ detailPurchasedProduct.status_order }}</span></p>
                    <p class="font-semibold text-slate-600">Status Pembayaran: <span class="text-slate-500 font-normal">{{ setLabelStatusPayment(detailPurchasedProduct.payment.status_payment) }}</span></p>
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-5 mt-3">
                  <div class="payment_information shadow-md p-3">
                    <p class="text-lg font-bold uppercase text-slate-600">Informasi Pembayaran</p>
                    <div class="max-w-full max-h-56 overflow-x-auto custom-scrollbar mx-auto mt-1">
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
                              <div v-if="detailPurchasedProduct.payment.is_the_nominal_cost_more_or_less === 'more' && detailPurchasedProduct.payment.nominal_cost_more_or_less > 0" >
                                <p class="text-slate-700 font-semibold text-sm whitespace-nowrap">Biaya Lebih</p>
                              </div>
                              <div v-else-if="detailPurchasedProduct.payment.is_the_nominal_cost_more_or_less === 'less' && detailPurchasedProduct.payment.nominal_cost_more_or_less > 0">
                                <p class="text-slate-700 font-semibold text-sm whitespace-nowrap">Biaya Kurang</p>
                              </div>
                            </th>
                            <th class="p-2">
                              <p class="text-slate-700 font-semibold text-sm whitespace-nowrap">Dibayar Pada</p>
                            </th>
                            <th class="p-2">
                              <p class="text-slate-700 font-semibold text-sm whitespace-nowrap">Status Pembayaran</p>
                            </th>
                          </tr>
                        </thead>
                        <tbody class="bg-gray-50">
                          <tr>
                            <td class="p-2">
                              <p class="text-slate-900 font-semibold text-center text-sm whitespace-nowrap">1</p>
                            </td>
                            <td class="p-2">
                              <p class="text-slate-900 font-semibold text-center text-sm whitespace-nowrap">{{ detailPurchasedProduct.payment.payment_method }}</p>
                            </td>
                            <td class="p-2">
                              <p class="text-slate-900 font-semibold text-center text-sm whitespace-nowrap">{{ detailPurchasedProduct.payment.cash_paid ? formatCurrencyIDR(detailPurchasedProduct.payment.cash_paid) : 'Rp 0' }}</p>
                            </td>
                            <td class="p-2">
                              <div v-if="detailPurchasedProduct.payment.is_the_nominal_cost_more_or_less && detailPurchasedProduct.payment.nominal_cost_more_or_less">
                                <p class="font-semibold text-center text-sm whitespace-nowrap" :class="[detailPurchasedProduct.payment.is_the_nominal_cost_more_or_less === 'more' ? 'text-green-600' : 'text-rose-600']" >{{ formatCurrencyIDR(detailPurchasedProduct.payment.nominal_cost_more_or_less) }}</p>
                              </div>
                            </td>
                            <td class="p-2">
                              <p class="text-slate-900 font-semibold text-center text-sm whitespace-nowrap">{{ detailPurchasedProduct.payment.paid_on ? detailPurchasedProduct.payment.paid_on : '-' }}</p>
                            </td>
                            <td class="p-2">
                              <p class="text-slate-900 font-semibold text-center text-sm whitespace-nowrap">{{ setLabelStatusPayment(detailPurchasedProduct.payment.status_payment) }}</p>
                            </td>
                          </tr>
                         </tbody>
                      </table>
                      <div class="mt-3 mb-2" v-if="detailPurchasedProduct.payment.status_payment === 'Due' || detailPurchasedProduct.payment.status_payment === 'Late'">
                        <Button
                          label="Tambahkan Pembayaran"
                          severity="info"
                          size="small"
                          icon="pi pi-plus"
                          class="!shadow-md"
                          @click="displayDetailPayment = true"
                        />
                      </div>
                      <div v-else class="mt-3">
                        <p class="text-lg font-bold uppercase text-slate-600">Riwayat Bukti Pembayaran</p>
                         <Image 
                          :src="`/storage/purchase-product/proof-of-payment/${detailPurchasedProduct.payment.proof_of_payment}`" 
                          alt="Proo Of Payment Image" 
                          width="100" 
                          preview 
                          class="mt-2"
                        />
                      </div>
                    </div>
                  </div>
                  <div class="h-fit shadow-md p-3">
                    <p class="text-lg font-bold uppercase text-slate-600">Detail Transaksi</p>
                    <div class="grid grid-cols-2">
                      <div>
                         <p class="font-semibold text-slate-600">Metode Pembayaran</p>
                         <p class="font-semibold text-slate-600">Jangka Waktu Pembayaran</p>
                         <p class="font-semibold text-slate-600">Biaya Pengiriman</p>                     
                         <p class="font-semibold text-slate-600">Jumlah Total Item</p>
                         <p class="font-semibold text-slate-600">Satuan</p>
                         <p class="font-semibold text-slate-600">Jumlah Total Bersih</p>
                      </div>
                      <div>
                        <p class="text-slate-500 font-normal">: {{ detailPurchasedProduct.payment.payment_method }}</p>
                        <p class="text-slate-500 font-normal">: {{ setLabelTerminStatus() }}</p>
                        <p class="text-slate-500 font-normal">: {{ detailPurchasedProduct.shipping_cost !== 0 ? formatCurrencyIDR(detailPurchasedProduct.shipping_cost) : 'Rp 0' }}</p>
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
                      <p class="font-semibold text-slate-600">Catatan Pembayaran</p>
                    </div>
                    <div>
                      <p class="text-slate-500 font-normal">: {{ detailPurchasedProduct.shipping_details ?? 'Tidak Ada' }}</p>
                      <p class="text-slate-500 font-normal">: {{ detailPurchasedProduct.order_note ?? 'Tidak Ada' }}</p>
                      <p class="text-slate-500 font-normal">: {{ detailPurchasedProduct.payment.payment_notes ?? 'Tidak Ada' }}</p>
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
              <Dialog
                  header="Pembayaran"
                  v-model:visible="displayDetailPayment"
                  :modal="true"
                  :style="{ width: '35rem' }"
              >
                <div class="cash mt-2">
                  <div class="flex items-center space-x-2 mb-2">
                    <label for="cash" class="font-semibold text-slate-500">Uang Tunai</label>
                    <i class="pi pi-question-circle bg-blue-300 text-white rounded-full cursor-pointer" v-tooltip.top="'Nominal uang tunai yang harus dibayar'" style="font-size: 1.2rem"></i>
                  </div>
                  <InputGroup>
                    <InputGroupAddon>
                      <i class="pi pi-money-bill"></i>
                    </InputGroupAddon>
                    <InputNumber
                      prefix="Rp "
                      placeholder="Nominal Uang Tunai Yang Dibayar"
                      v-model="cash_paid"
                      v-bind="cashPaidAttr"
                      :max="maxCashPaid"
                      disabled
                    />
                  </InputGroup>
                </div>
                <div class="paid_date mt-2">
                  <label for="paid_date" class="mb-2 block font-semibold text-slate-500">Dibayar Pada</label>
                  <DatePicker 
                    class="w-full"
                    placeholder="Tentukan tanggal pembayaran"
                    showIcon 
                    showButtonBar
                    fluid
                    dateFormat="dd/mm/yy"
                    showTime 
                    hourFormat="24"
                    v-model="paid_date"
                    v-bind="paidDateAttr"
                  />
                </div>
                <div class="mt-2">
                  <div class="flex items-center space-x-2 mb-2">
                    <label for="proof_of_payment" class="font-semibold text-slate-500">Bukti Pembayaran</label>
                    <i class="pi pi-question-circle bg-blue-300 text-white rounded-full cursor-pointer" v-tooltip.top="'Unggah Bukti Pembayaran'" style="font-size: 1.2rem"></i>
                  </div>
                  <div class="flex justify-start">
                    <FileUpload 
                      class="!bg-slate-900 hover:!bg-slate-700"
                      v-model="proof_of_payment"
                      chooseLabel="Unggah"
                      cancelLabel="Hapus"
                      chooseIcon="pi pi-upload"
                      :maxFileSize="2048000"
                      accept="image/png,image/webp"
                      invalidFileSizeMessage="Maksimal ukuran file: 2MB"
                      invalidFileTypeMessage="Ekstensi file yang diizinkan (png, webp)." 
                      :multiple="false"
                      :fileLimit="1"
                      @select="handleSelectOnFileUpload" 
                    />
                  </div>
                </div>
                <div class="paid_notes mt-2">
                  <label for="paid_notes" class="mb-2 block font-semibold text-slate-500">Catatan Pembayaran</label>
                  <Textarea 
                    rows="3"
                    placeholder="Catatan pembayaran"
                    v-model="paid_notes"
                    v-bind="paidNotesAttr"
                    class="w-full"
                  />
                </div>
                <template #footer>
                  <Button
                    label="Bayar Sekarang"
                    severity="success"
                    @click="handleSavePayment"
                  />
                  <Button
                    label="Tutup"
                    severity="secondary"
                    @click="displayDetailPayment = false"
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
import InputGroup from "primevue/inputgroup";
import InputGroupAddon from "primevue/inputgroupaddon";
import InputNumber from "primevue/inputnumber";
import DatePicker from "primevue/datepicker";
import FileUpload from "primevue/fileupload";
import Textarea from "primevue/textarea";
import Popover from 'primevue/popover';
import { formatCurrencyIDR } from "../../helpers/formatCurrency";
import { debounce } from "../../helpers/debounce";
import { useToast } from "primevue/usetoast";
import { usePurchasedProductStore } from '../../stores/purchased_product';
import { useUserStore } from '../../stores/user';
import { useForm } from "vee-validate";
import * as yup from "yup";
import { useRouter } from "vue-router";
import { useApotekStore } from "../../stores/apotek";
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
      Popover
    },
    setup() {
      const purchasedProductStore = usePurchasedProductStore();
      const userStore = useUserStore();
      const apotekStore = useApotekStore();
      const supplierStore = useSupplierStore();
      const purchased_products = ref([]);
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
      const selectedPurchasedProduct = ref(null);
      const breadcrumbIcon = ref({
          icon: 'pi pi-chart-bar'
      })
      const breadcrumbItems = ref([
          { label: 'Dashboard' }, 
          { label: 'Data Pembelian' }, 
      ]);
      const displayDetailPurchaseProduct = ref(false);
      const displayDetailPayment = ref(false);
      const toast = useToast();
      const maxCashPaid = computed(() => purchasedProductStore.detailPurchasedProduct.payment.nominal_cost_more_or_less || 0)
      const POP_OVER_REF = ref()
      const STATUS_ORDER = ref([
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
        "No. REF",
        "Supplier / Pemasok",
        "Lokasi Apotek",
        "Total Item",
        "Total Harga",
        "Biaya Pengiriman",
        "Tanggal Order",
        "Status Order",
        "Status Pembayaran",
      ])
      const popoverStatusOrderData = ref(null)
      const selectedStatusOrder = ref(null)
      const selectedFilterApotek = ref(null)
      const selectedFilterSupplier = ref(null)
      const selectedFilterStatusOrder = ref('')
      const selectedFilterStatusPayment = ref('')
      const selectedFilterUser = ref('')
      const selectedFilterOrderDate = ref(null)
      const router = useRouter()

      onMounted(async () => {
        clearFilterState()
        clearSelectedProduct()

        Promise.all([
          loadPurchasedProducts(),
          loadApoteks(),
          loadSuppliers(),
          loadUsers()
        ])
      })

      const loadPurchasedProducts = async (url) => {
        loading.value = true;        

        await purchasedProductStore.getListOrderPurchasedProduct(url);
        purchased_products.value = purchasedProductStore.listOrderPurchasedProduct;
        nextPageUrl.value = purchasedProductStore.NEXT_PAGE_URL;
        prevPageUrl.value = purchasedProductStore.PREV_PAGE_URL;

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
        if (field === 'order_date') {          
          const startDate = {
            'date':  value[0] ? new Date(value[0]).toLocaleDateString('en-CA') : '',
          }

          const endDate = {
            'date':  value[1] ? new Date(value[1]).toLocaleDateString('en-CA') : '',
          }
          
          purchasedProductStore.setFilter('start_date', startDate.date)
          purchasedProductStore.setFilter('end_date', endDate.date)

          if (startDate.date && endDate.date) {
            loadPurchasedProducts()
          }
        } else {
          purchasedProductStore.setFilter(field, value)
          loadPurchasedProducts()
        }                
      }, 300)

      const getNextPageContent = async () => {
        const url = nextPageUrl.value;
        if (nextPageUrl) {
          await loadPurchasedProducts(url);
        }
      }

      const getPrevPageContent = async () => {
        const url = prevPageUrl.value;
        if (prevPageUrl) {
          await loadPurchasedProducts(url);
        }
      }

      const clearSelectedProduct = () => {
        purchasedProductStore.setNullListProductSelected()
        purchasedProductStore.setNullProductIds()
      }

      const clearFilterState = () => {
        purchasedProductStore.setNullFilters()
      }

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

      const debouncedSearch = debounce((value) => {
            purchasedProductStore.setFilter('search', value)
            loadPurchasedProducts()
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

            const data = purchased_products.value.map((purch_product) => ({
              "No. REF": purch_product.reference_number,
              "Supplier / Pemasok": purch_product.supplier.supplier_name,
              "Lokasi Apotek": purch_product.apotek.name_of_apotek,
              "Total Item": countTotalItemsPerOrder() + ' Item',
              "Total Harga": formatCurrencyIDR(purch_product.grand_total),
              "Biaya Pengiriman": purch_product.shipping_cost ? formatCurrencyIDR(purch_product.shipping_cost) : 'Rp 0',
              "Tanggal Order": purch_product.order_date,
              "Status Order": setLabelStatusOrder(purch_product.status_order),
              "Status Pembayaran": setLabelStatusPayment(purch_product.payment.status_payment),
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

            writeFileXLSX(workbook, `DataOrderProduk_${date.getDate()}_${date.getMonth() + 1}_${date.getFullYear()}.xlsx`);
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
            const data = purchased_products.value.map((purch_product) => ({
              "No. REF": purch_product.reference_number,
              "Supplier / Pemasok": purch_product.supplier.supplier_name,
              "Lokasi Apotek": purch_product.apotek.name_of_apotek,
              "Total Item": countTotalItemsPerOrder() + ' Item',
              "Total Harga": formatCurrencyIDR(purch_product.grand_total),
              "Biaya Pengiriman": purch_product.shipping_cost ? formatCurrencyIDR(purch_product.shipping_cost) : 'Rp 0',
              "Tanggal Order": purch_product.order_date,
              "Status Order": setLabelStatusOrder(purch_product.status_order),
              "Status Pembayaran": setLabelStatusPayment(purch_product.payment.status_payment),
            }));

            const tableData = data.map(item => headers.value.map(header => item[header]))

            autoTable(doc, {
              head: [headers.value],
              body: tableData,
              styles: {
                fontSize: 8,
              }
              })

            doc.save(`DataOrderProduk_${date.getDate()}_${date.getMonth() + 1}_${date.getFullYear()}.pdf`)
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
            command: () => displayDetailPurchaseProduct.value = true,
        },
        {
            label: "Edit",
            icon: "pi pi-pencil",
            command: () => router.push({ name: "purchased-product.edit-data-purchased", params: { reference_number: purchasedProductStore.detailPurchasedProduct.reference_number } }),
        },
      ]);

      const onRowContextMenu = (event) => {
        purchasedProductStore.setSelectedPurchasedProduct(event.data);

        if (purchasedProductStore.detailPurchasedProduct.payment.is_the_nominal_cost_more_or_less === 'less') {
          cash_paid.value = purchasedProductStore.detailPurchasedProduct.payment.nominal_cost_more_or_less
        }

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

      const setLabelStatusOrder = (status) => {
        switch (status) {
          case 'Pending':
            return 'Proses'
          case 'Shipping':
            return 'Dikirim'
          case 'Delivered':
            return 'Diterima'
          case 'Cancelled':
            return 'Dibatalkan'
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

        return Array.from(new Set(unitProductLabel.join(", ").split(", "))).join(", ")
      }

      const setLabelTerminStatus = () => {
        const formatTerminDate = purchasedProductStore.detailPurchasedProduct.format_termin_date === 'Day' ? 'Hari' : 'Bulan'

        return purchasedProductStore.detailPurchasedProduct.termin_payment && purchasedProductStore.detailPurchasedProduct.format_termin_date
          ? `${purchasedProductStore.detailPurchasedProduct.termin_date} (${purchasedProductStore.detailPurchasedProduct.termin_payment} ${formatTerminDate})`
          : '-'
      }

      const { errors, handleSubmit, defineField } = useForm({
        validationSchema: {
          cash_paid: yup.number().required("Nominal uang tunai harus diisi"),
          paid_date: yup.date().required("Tanggal pembayaran harus diisi"),
          proof_of_payment: yup.string().required("Bukti pembayaran harus diunggah"),
          paid_notes: yup.string().required("Catatan pembayaran harus diisi")
        }
      })

      const [cash_paid, cashPaidAttr] = defineField("cash_paid")
      const [paid_date, paidDateAttr] = defineField("paid_date")
      const [proof_of_payment, proofOfPaymentAttr] = defineField("proof_of_payment")
      const [paid_notes, paidNotesAttr] = defineField("paid_notes")

      const handleSelectOnFileUpload = (event) => {
        if (event.files.length > 0) {
          proof_of_payment.value = event.files[0]
        } else {
          proof_of_payment.value = null
        }
      }

      const handleSavePayment = handleSubmit(async (datas, { resetForm }) => {  
        loading.value = true      
        await purchasedProductStore.paidOrder(datas)

        if (purchasedProductStore.errorSavedPayment) {
          loading.value = false
          toast.add({
            severity: "error",
            summary: "Paid Order Failed",
            detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
            life: 3000,
          });
        } else {
          displayDetailPayment.value = false
          loading.value = false
          resetForm()
          toast.add({
            severity: "success",
            summary: "Paid Order Successfully",
            detail: "Pembayaran Order Berhasil",
            life: 3000,
          });
        } 
      })
      
      const showPopoverStatusOrder = (event, orderId, refNumber) => {
        popoverStatusOrderData.value = {
          id: orderId,
          ref_numb: refNumber
        }
        POP_OVER_REF.value.toggle(event)
      }

      const handleChangeStatusOrder = async () => {
        loading.value = true
        const statusOrder = selectedStatusOrder.value
        const orderId = popoverStatusOrderData.value.id

        await purchasedProductStore.changeStatusOrder(statusOrder, orderId)

        if (purchasedProductStore.errorSavedPayment) {
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
        purchased_products,
        apoteks,
        suppliers,
        users,
        detailPurchasedProduct: computed(() => purchasedProductStore.detailPurchasedProduct),
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
        selectedPurchasedProduct,
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
        setColorInfoStatusOrder,
        setLabelStatusOrder,
        setColorStatusPayment,
        setLabelStatusPayment,
        setLabelUnitProduct,
        setLabelTerminStatus,
        cm,
        displayDetailPurchaseProduct,
        displayDetailPayment,
        countTotalItemsPerOrder,
        handleSelectOnFileUpload,
        handleSavePayment,
        maxCashPaid,
        cash_paid,
        cashPaidAttr,
        paid_date,
        paidDateAttr,
        proof_of_payment,
        proofOfPaymentAttr,
        paid_notes,
        paidNotesAttr,
        errors,
        POP_OVER_REF,
        showPopoverStatusOrder,
        STATUS_ORDER,
        selectedStatusOrder,
        popoverStatusOrderData,
        handleChangeStatusOrder,
        selectedFilterApotek,
        selectedFilterSupplier,
        selectedFilterStatusOrder,
        selectedFilterStatusPayment,
        selectedFilterUser,
        selectedFilterOrderDate,
        setFilterOption
      }
    }
};
</script>
<style scoped>
:deep(.p-fileupload) {
  width: 100% !important;
}
</style>