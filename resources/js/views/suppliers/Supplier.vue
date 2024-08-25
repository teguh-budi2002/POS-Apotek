<template>
    <Content>
        <template v-slot:content>
            <Toast />
            <div>
                <Breadcrumb :home="breadcrumbIcon" :model="breadcrumbItems" class="!bg-transparent" />
            </div>
            <div
                class="main-content-supplier bg-white rounded-md shadow-md w-full card p-4"
            >
                <span class="text-xl font-bold">Manajemen Supplier</span>
                <ContextMenu
                    ref="cm"
                    :model="menuModel"
                    @hide="selectedSupplier = null"
                />
                <div v-if="suppliers">
                    <DataTable
                        :value="suppliers"
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
                        editMode="row"
                        v-model:editingRows="editingRows"
                        @row-edit-save="onRowEditSave"
                        contextMenu
                        @rowContextmenu="onRowContextMenu"
                        v-model:contextMenuSelection="selectedSupplier"
                        v-model:expandedRows="expandedRows"
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
                                        <div v-show="trashed_suppliers.length">
                                            <Button icon="pi pi-trash" v-tooltip.top="{ value: 'Sampah' }" @click="showTrashedSupplierDialog = true" class="!bg-rose-800 hover:!bg-rose-600 !border-none"/>
                                        </div>
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
                                        <Button
                                            icon="pi pi-plus"
                                            @click="openDrawer = true"
                                            raised
                                            severity="info"
                                        />
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template #empty> Supplier tidak tersedia. </template>
                        <template #loading>
                            <i
                                class="pi pi-spinner-dotted animate-spin"
                                style="font-size: 2rem"
                            ></i>
                        </template>

                        <Column
                            field="supplier_name"
                            header="Nama Supplier"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.supplier_name }}
                            </template>
                            <template #editor="{ data, field }">
                                <InputText v-model="data[field]" />
                            </template>
                        </Column>
                        <Column
                            field="email"
                            header="Email"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.email }}
                            </template>
                            <template #editor="{ data, field }">
                                <InputText v-model="data[field]" />
                            </template>
                        </Column>
                        <Column
                            field="contact_phone"
                            header="Nomor Kontak"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.contact_phone }}
                            </template>
                            <template #editor="{ data, field }">
                                <InputText v-model="data[field]" />
                            </template>
                        </Column>
                        <Column
                            field="city"
                            header="Kota"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.city }}
                            </template>
                            <template #editor="{ data, field }">
                                <InputText v-model="data[field]" />
                            </template>
                        </Column>
                        <Column
                            field="province"
                            header="Provinsi"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.province }}
                            </template>
                            <template #editor="{ data, field }">
                                <InputText v-model="data[field]" />
                            </template>
                        </Column>
                        <Column
                            field="zip_code"
                            header="Kode Pos"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.zip_code }}
                            </template>
                            <template #editor="{ data, field }">
                                <InputText v-model="data[field]" />
                            </template>
                        </Column>
                        <Column
                            field="address"
                            header="Alamat Lengkap"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.address }}
                            </template>
                            <template #editor="{ data, field }">
                                <InputText v-model="data[field]" />
                            </template>
                        </Column>
                        <Column
                            field="description"
                            header="Description"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.description }}
                            </template>
                            <template #editor="{ data, field }">
                                <InputText v-model="data[field]" />
                            </template>
                        </Column>
                        <Column
                            :rowEditor="true"
                            style="width: 10%; min-width: 8rem"
                            bodyStyle="text-align:center"
                        ></Column>
                    </DataTable>
                </div>
                <div v-else>
                    <DataTable
                        :value="suppliers"
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

                        <template #empty>Supplier tidak tersedia. </template>
                        <template #loading>
                            <i
                                class="pi pi-spinner-dotted animate-spin"
                                style="font-size: 2rem"
                            ></i>
                        </template>
                    </DataTable>
                </div>
            </div>
            <Dialog v-model:visible="showTrashedSupplierDialog" :style="{width: '35rem'}" header="Daftar Sampah" :modal="true">
                <div v-for="t in trashed_suppliers" :key="t.id">
                    <div class="flex items-center justify-between mb-2 mt-2">
                        <p>{{ t.supplier_name }}</p>
                        <Button size="small" @click="restoreSupplier(t.id)" severity="success" label="Pulihkan"/>
                    </div>
                    <hr>
                </div>
                <template #footer>
                    <Button @click="showTrashedSupplierDialog = false" icon="pi pi-times" label="Tutup" severity="secondary" text/>
                </template>
            </Dialog>
            <Drawer
                v-model:visible="openDrawer"
                header="Tambah Supplier"
                position="right"
                class="!w-96"
                style="height: 100vh"
            >
                <form @submit="submitSupplier">
                    <div class="mt-1">
                        <label for="supplier_name" class="text-sm"
                            >Nama Supplier</label
                        >
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.supplier_name }}
                        </p>
                        <InputText
                            id="supplier_name"
                            v-model="supplier_name"
                            v-bind="nameOfSupplierAttr"
                            class="!w-full mt-2"
                        />
                    </div>
                    <div class="mt-1">
                        <label for="email" class="text-sm block">
                            Email Supplier
                            <span class="text-xs text-rose-500">(opsional)</span>
                        </label>
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.email }}
                        </p>
                        <InputText
                            id="email"
                            v-model="email"
                            v-bind="emailAttr"
                            class="!w-full mt-2"
                        />
                    </div>
                    <div class="mt-1">
                        <label for="contact_phone" class="text-sm"
                            >Kontak Supplier</label
                        >
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.contact_phone }}
                        </p>
                        <InputNumber
                            id="contact_phone"
                            :maxFractionDigits="14"
                            :useGrouping="false"
                            v-model="contact_phone"
                            v-bind="contactPhoneAttr"
                            class="!w-full mt-2"
                        />
                    </div>
                    <div class="mt-1">
                        <label for="city" class="text-sm"
                            >Kota</label
                        >
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.city }}
                        </p>
                        <InputText
                            id="city"
                            v-model="city"
                            v-bind="cityAttr"
                            class="!w-full mt-2"
                        />
                    </div>
                    <div class="mt-1">
                        <label for="province" class="text-sm"
                            >Provinsi</label
                        >
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.province }}
                        </p>
                        <InputText
                            id="province"
                            v-model="province"
                            v-bind="provinceAttr"
                            class="!w-full mt-2"
                        />
                    </div>
                    <div class="mt-1">
                        <label for="zip_code" class="text-sm"
                            >Kode Pos</label
                        >
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.zip_code }}
                        </p>
                        <InputNumber
                            id="zip_code"
                            :maxFractionDigits="5"
                            :useGrouping="false"
                            v-model="zip_code"
                            v-bind="zipCodeAttr"
                            class="!w-full mt-2"
                        />
                    </div>
                    <div class="mt-1">
                        <label for="address" class="text-sm"
                            >Alamat Lengkap</label
                        >
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.address }}
                        </p>
                        <Textarea
                            id="address"
                            v-model="address"
                            v-bind="addressAttr"
                            class="!w-full mt-2"
                        />
                    </div>
                    <div class="mt-1">
                        <label for="description" class="text-sm block">
                            Description Supplier
                            <span class="text-xs text-rose-500">(opsional)</span>
                        </label>
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.description }}
                        </p>
                        <Textarea
                            id="description"
                            v-model="description"
                            v-bind="descriptionAttr"
                            class="!w-full mt-2"
                        />
                    </div>
                    <div class="mt-5 flex items-center space-x-3">
                        <Button
                            severity="info"
                            class="!w-full"
                            label="Tambah"
                            type="submit"
                        />
                        <Button
                            severity="secondary"
                            label="Tutup"
                            @click="openDrawer = false"
                        />
                    </div>
                </form>
            </Drawer>
        </template>
    </Content>
</template>
<script>
import { ref, onMounted, watch, computed } from "vue";
import Content from "../../components/Layout/Content.vue";
import InputText from "primevue/inputtext";
import InputNumber from "primevue/inputnumber";
import Textarea from "primevue/textarea";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import ContextMenu from "primevue/contextmenu";
import Drawer from "primevue/drawer";
import { useSupplierStore } from "../../stores/supplier";
import { formatCurrencyIDR } from "../../helpers/formatCurrency";
import { debounce } from "../../helpers/debounce";
import { useToast } from "primevue/usetoast";
import { useForm } from "vee-validate";
import * as Yup from "yup";

export default {
    components: {
        Content,
        InputText,
        InputNumber,
        IconField,
        InputIcon,
        ContextMenu,
        Drawer,
        Textarea,
    },
    setup() {
        const supplierStore = useSupplierStore();
        const suppliers = ref([])
        const trashed_suppliers = ref([])
        const totalRecords = ref(0);
        const selectedSupplier = ref();
        const showTrashedSupplierDialog = ref(false)
        const toast = useToast();
        const dataTable = ref([]);
        const searchQuery = ref(supplierStore.filters.search || '')
        const cm = ref();
        const expandedRows = ref({});
        const loading = ref(false);
        const isExporting = ref(false)
        const openDrawer = ref(false);
        const headers = ref([
            "Nama Supplier",
            "Email",
            "Nomor Kontak",
            "Kota",
            "Provinsi",
            "Kode Pos",
            "Alamat Lengkap",
        ])
        const rows = ref(10)
        const rowsPerPageOptions = ref([5, 10, 20, 30, 40, 50, 100])
        const breadcrumbIcon = ref({
            icon: 'pi pi-chart-bar'
        })
        const breadcrumbItems = ref([
            { label: 'Dashboard' }, 
            { label: 'Data Supplier' }, 
        ]);

        onMounted(async () => {
            searchQuery.value = supplierStore.filters.search || ''
            await loadSuppliers();
            await loadTrashedSuppliers();
        });

        const loadSuppliers = async (page = 1, rowsPerPage = rows.value) => {
            loading.value = true;

            await supplierStore.getSupplierPerPage(page, rowsPerPage);
            suppliers.value = supplierStore.suppliers;
            totalRecords.value = supplierStore.totalRecords;
            loading.value = false;
        };

        const loadTrashedSuppliers = async () => {
            loading.value = true;

            await supplierStore.getTrashedSupplier();
            trashed_suppliers.value = supplierStore.trashed_suppliers;
            loading.value = false;
        };

        const clearFilter = () => {
            searchQuery.value = ''
        };

        const onPage = (e) => {
            loadSuppliers(e.page + 1, e.rows)
        };

        const onRowsChange = (event) => {
            rows.value = event.rows;
            loadSuppliers(1, event.rows);
        };

        const debouncedSearch = debounce((value) => {
            supplierStore.setFilter('search', value)
            loadSuppliers()
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

                const data = suppliers.value.map((supplier) => ({
                    "Nama Supplier": supplier.supplier_name,
                    "Email": supplier.email,
                    "Nomor Kontak": supplier.contact_phone,
                    "Kota": supplier.city,
                    "Provinsi": supplier.province,
                    "Kode Pos": supplier.zip_code,
                    "Alamat Lengkap": supplier.address,
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

                writeFileXLSX(workbook, `DataSupplier_${date.getDate()}_${date.getMonth() + 1}_${date.getFullYear()}.xlsx`);
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
                const data = suppliers.value.map((supplier) => ({
                    "Nama Supplier": supplier.supplier_name,
                    "Email": supplier.email,
                    "Nomor Kontak": supplier.contact_phone,
                    "Kota": supplier.city,
                    "Provinsi": supplier.province,
                    "Kode Pos": supplier.zip_code,
                    "Alamat Lengkap": supplier.address,
                }));

                const tableData = data.map(item => headers.value.map(header => item[header]))

                autoTable(doc, {
                    head: [headers.value],
                    body: tableData
                })
    
                doc.save(`DataSupplier_${date.getDate()}_${date.getMonth() + 1}_${date.getFullYear()}.pdf`)
                isExporting.value = false
            } else {
                isExporting.value = false
                console.log(dataTable.value, "Datatable ref is null");
            }
        };

        const { errors, handleSubmit, defineField } = useForm({
            validationSchema: Yup.object().shape({
                supplier_name: Yup.string().required("Nama Supplier wajib diisi."),
                email: Yup.string().email("Alamat email harus valid"),
                contact_phone: Yup.string().matches(/^62[0-9]+$/, "No HP berawalan 62").required("No HP supplier wajib diisi."),
                city: Yup.string().required("Kota wajib diisi."),
                province: Yup.string().required("Provinsi wajib diisi."),
                zip_code: Yup.string().required("Kode Pos wajib diisi."),
                address: Yup.string().required("Alamat lengkap wajib diisi."),
                description: Yup.string(),
            }),
        });

        const [supplier_name, nameOfSupplierAttr] = defineField("supplier_name");
        const [email, emailAttr] = defineField("email");
        const [contact_phone, contactPhoneAttr] = defineField("contact_phone");
        const [city, cityAttr] = defineField("city");
        const [province, provinceAttr] = defineField("province");
        const [zip_code, zipCodeAttr] = defineField("zip_code");
        const [address, addressAttr] = defineField("address");
        const [description, descriptionAttr] = defineField("description");

        const submitSupplier = handleSubmit(async (datas, { resetForm }) => {
            loading.value = true;
            await supplierStore.addSupplier(datas);

            if (supplierStore.errorAddedData) {
                loading.value = false;
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Added Supplier Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                openDrawer.value = false;
                loading.value = false;
                resetForm();
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Added Supplier Successfully",
                    detail: "Tambah Supplier Berhasil",
                });
            }
        });

        const editingRows = ref([]);
        const onRowEditSave = async (event) => {
            loading.value = true;
            let { newData, data } = event;
            const updatedData = newData;
            const filteredNewData = {};

            Object.keys(updatedData).forEach((key) => {
                if (updatedData[key] !== data[key]) {
                    filteredNewData[key] = updatedData[key];
                }
            });

            await supplierStore.updateDataSupplier(filteredNewData, data.id);

            if (supplierStore.errorUpdateData) {
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Update Supplier Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Update Supplier Successfully",
                    detail: "Update Supplier Berhasil",
                });
            }

            loading.value = false;
        };

        const menuModel = computed(() => [
            {
                label: "Delete",
                icon: "pi pi-fw pi-times",
                command: () => deleteSupplier(selectedSupplier),
            },
        ]);
        const onRowContextMenu = (event) => {
            cm.value.show(event.originalEvent);
        };

        const deleteSupplier = async (cat) => {
            loading.value = true;
            await supplierStore.deleteSupplier(cat.value.id);

            if (supplierStore.errorDeleteSupplier) {
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Delete Supplier Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Delete Supplier Successfully",
                    detail: "Supplier Berhasil Di Hapus",
                });
            }

            loading.value = false;
        };

        const restoreSupplier = async (supplierId) => {
            loading.value = true;
            await supplierStore.restoreSupplier(supplierId)

            if (supplierStore.errorAddedData) {
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Restore Supplier Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                showTrashedSupplierDialog.value = false
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Restore Supplier Successfully",
                    detail: "Supplier Berhasil Di Pulihkan",
                });
            }
            loading.value = false;
        }

        return {
            suppliers,
            trashed_suppliers,
            totalRecords,
            selectedSupplier,
            selectedSupplier,
            suppliers,
            clearFilter,
            exportExcel,
            exportPDF,
            editingRows,
            onRowEditSave,
            menuModel,
            onRowContextMenu,
            cm,
            expandedRows,
            dataTable,
            formatCurrencyIDR,
            loading,
            isExporting,
            onPage,
            searchQuery,
            openDrawer,
            supplier_name,
            nameOfSupplierAttr,
            email,
            emailAttr,
            contact_phone,
            contactPhoneAttr,
            city,
            cityAttr,
            province,
            provinceAttr,
            zip_code,
            zipCodeAttr,
            address,
            addressAttr,
            description,
            descriptionAttr,
            errors,
            submitSupplier,
            rows,
            rowsPerPageOptions,
            onRowsChange,
            showTrashedSupplierDialog,
            restoreSupplier,
            breadcrumbIcon,
            breadcrumbItems,
        };
    },
};
</script>
