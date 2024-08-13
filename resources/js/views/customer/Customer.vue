<template>
    <Content>
        <template v-slot:content>
            <Toast />
            <div>
                <Breadcrumb :home="breadcrumbIcon" :model="breadcrumbItems" />
            </div>
            <div
                class="main-content-customer bg-white rounded-md shadow-md w-full card p-4"
            >
                <span class="text-xl font-bold">Manajemen Pelanggan</span>
                <ContextMenu
                    ref="cm"
                    :model="menuModel"
                    @hide="selectedCustomer = null"
                />
                <div v-if="customers">
                    <DataTable
                        :value="customers"
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
                        v-model:contextMenuSelection="selectedCustomer"
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
                                        <div v-show="trashed_customers.length">
                                            <Button icon="pi pi-trash" v-tooltip.top="{ value: 'Sampah' }" @click="showTrashedCustomerDialog = true" class="!bg-rose-800 hover:!bg-rose-600 !border-none"/>
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

                        <template #empty> Customer tidak tersedia. </template>
                        <template #loading>
                            <i
                                class="pi pi-spinner-dotted animate-spin"
                                style="font-size: 2rem"
                            ></i>
                        </template>

                        <Column
                            field="name"
                            header="Nama Customer"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.name }}
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
                            field="gender"
                            header="Jenis Kelamin"
                        >
                            <template #body="slotProps">
                              <div v-if="slotProps.data.gender === 'L'" class="text-center">
                                <Button label="Laki-Laki" severity="contrast" raised />
                              </div>
                              <div v-else class="text-center">
                                <Button label="Perempuan" severity="contrast" raised />
                              </div>
                            </template>
                            <template #editor="{ data, field }">
                                <Select 
                                  v-model="data[field]"   
                                  :options="[{ name : 'Laki-Laki', value: 'L' }, { name: 'Perempuan', value: 'P'}]"
                                  optionLabel="name" 
                                  optionValue="value"
                                  checkmark
                                />
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
                            :rowEditor="true"
                            style="width: 10%; min-width: 8rem"
                            bodyStyle="text-align:center"
                        ></Column>
                    </DataTable>
                </div>
                <div v-else>
                    <DataTable
                        :value="customers"
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

                        <template #empty>Customer tidak tersedia. </template>
                        <template #loading>
                            <i
                                class="pi pi-spinner-dotted animate-spin"
                                style="font-size: 2rem"
                            ></i>
                        </template>
                    </DataTable>
                </div>
            </div>
            <Dialog v-model:visible="showTrashedCustomerDialog" :style="{width: '35rem'}" header="Daftar Sampah" :modal="true">
                <div v-for="t in trashed_customers" :key="t.id">
                    <div class="flex items-center justify-between mb-2 mt-2">
                        <p>{{ t.name }}</p>
                        <Button size="small" @click="restoreCustomer(t.id)" severity="success" label="Pulihkan"/>
                    </div>
                    <hr>
                </div>
                <template #footer>
                    <Button @click="showTrashedCustomerDialog = false" icon="pi pi-times" label="Tutup" severity="secondary" text/>
                </template>
            </Dialog>
            <Drawer
                v-model:visible="openDrawer"
                header="Tambah Customer"
                position="right"
                class="!w-96"
                style="height: 100vh"
            >
                <form @submit="submitCustomer">
                    <div class="mt-1">
                        <label for="name" class="text-sm"
                            >Nama Customer</label
                        >
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.name }}
                        </p>
                        <InputText
                            id="name"
                            v-model="name"
                            v-bind="nameAttr"
                            class="!w-full mt-2"
                        />
                    </div>
                    <div class="mt-1">
                        <label for="contact_phone" class="text-sm"
                            >Kontak Customer</label
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
                    <div class="mt-3">
                        <label for="gender" class="text-sm"
                            >Jenis Kelamin</label
                        >
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.gender }}
                        </p>
                        <Select
                            id="gender"
                            v-model="selectedGender"
                            v-bind="genderAttr"
                            :options="[{ name : 'Laki-Laki', value: 'L' }, { name: 'Perempuan', value: 'P'}]"
                            optionLabel="name"
                            placeholder="Pilih Jenis Kelamin"
                            class="!w-full mt-2"
                            @change="updateGender"
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
import { useCustomerStore } from "../../stores/customer";
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
        const customerStore = useCustomerStore();
        const customers = ref([])
        const trashed_customers = ref([])
        const totalRecords = ref(0);
        const selectedCustomer = ref();
        const selectedGender = ref()
        const showTrashedCustomerDialog = ref(false)
        const toast = useToast();
        const dataTable = ref([]);
        const searchQuery = ref(customerStore.filters.search || '')
        const cm = ref();
        const expandedRows = ref({});
        const loading = ref(false);
        const isExporting = ref(false)
        const openDrawer = ref(false);
        const headers = ref([
            "Nama Customer",
            "Nomor Kontak",
            "Jenis Kelamin",
            "Alamat Lengkap",
        ])
        const rows = ref(10)
        const rowsPerPageOptions = ref([5, 10, 20, 30, 40, 50, 100])
        const breadcrumbIcon = ref({
            icon: 'pi pi-chart-bar'
        })
        const breadcrumbItems = ref([
            { label: 'Dashboard' }, 
            { label: 'Data Customer' }, 
        ]);

        onMounted(async () => {
            searchQuery.value = customerStore.filters.search || ''
            await loadCustomers();
            await loadTrashedCustomers();
        });

        const loadCustomers = async (page = 1, rowsPerPage = rows.value) => {
            loading.value = true;

            await customerStore.getCustomerPerPage(page, rowsPerPage);
            customers.value = customerStore.customers;
            totalRecords.value = customerStore.totalRecords;
            loading.value = false;
        };

        const loadTrashedCustomers = async () => {
            loading.value = true;

            await customerStore.getTrashedCustomer();
            trashed_customers.value = customerStore.trashed_customers;
            loading.value = false;
        };

        const clearFilter = () => {
            searchQuery.value = ''
        };

        const onPage = (e) => {
            loadCustomers(e.page + 1, e.rows)
        };

        const onRowsChange = (event) => {
            rows.value = event.rows;
            loadCustomers(1, event.rows);
        };

        const debouncedSearch = debounce((value) => {
            customerStore.setFilter('search', value)
            loadCustomers()
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

                const data = customers.value.map((customer) => ({
                    "Nama Customer": customer.name,
                    "Nomor Kontak": customer.contact_phone,
                    "Jenis Kelamin": customer.gender === 'L' ? 'Laki-Laki' : 'Perempuan',
                    "Alamat Lengkap": customer.address,
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

                writeFileXLSX(workbook, `DataCustomer_${date.getDate()}_${date.getMonth() + 1}_${date.getFullYear()}.xlsx`);
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
                const data = customers.value.map((customer) => ({
                    "Nama Customer": customer.name,
                    "Nomor Kontak": customer.contact_phone,
                    "Jenis Kelamin": customer.gender === 'L' ? 'Laki-Laki' : 'Perempuan',
                    "Alamat Lengkap": customer.address,
                }));

                const tableData = data.map(item => headers.value.map(header => item[header]))

                autoTable(doc, {
                    head: [headers.value],
                    body: tableData
                })
    
                doc.save(`DataCustomer_${date.getDate()}_${date.getMonth() + 1}_${date.getFullYear()}.pdf`)
                isExporting.value = false
            } else {
                isExporting.value = false
                console.log(dataTable.value, "Datatable ref is null");
            }
        };

        const { errors, handleSubmit, defineField } = useForm({
            validationSchema: Yup.object().shape({
                name: Yup.string().required("Nama Customer wajib diisi."),
                contact_phone: Yup.string().matches(/^62[0-9]+$/, "No HP berawalan 62").required("No HP customer wajib diisi."),
                gender: Yup.string().required("Jenis kelamin wajib dipilih."),
                address: Yup.string().required("Alamat lengkap wajib diisi."),
            }),
        });

        const [name, nameAttr] = defineField("name");
        const [contact_phone, contactPhoneAttr] = defineField("contact_phone");
        const [gender, genderAttr] = defineField("gender");
        const [address, addressAttr] = defineField("address");

        const updateGender = (event) => {
          gender.value = event.value.value === "L" ? "L" : "P"
        }

        const submitCustomer = handleSubmit(async (datas, { resetForm }) => {
            loading.value = true;
            await customerStore.addCustomer(datas);

            if (customerStore.errorAddedData) {
                loading.value = false;
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Added Customer Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                openDrawer.value = false;
                loading.value = false;
                resetForm();
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Added Customer Successfully",
                    detail: "Tambah Customer Berhasil",
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

            await customerStore.updateDataCustomer(filteredNewData, data.id);

            if (customerStore.errorUpdateData) {
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Update Customer Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Update Customer Successfully",
                    detail: "Update Customer Berhasil",
                });
            }

            loading.value = false;
        };

        const menuModel = computed(() => [
            {
                label: "Delete",
                icon: "pi pi-fw pi-times",
                command: () => deleteCustomer(selectedCustomer),
            },
        ]);
        const onRowContextMenu = (event) => {
            cm.value.show(event.originalEvent);
        };

        const deleteCustomer = async (cat) => {
            loading.value = true;
            await customerStore.deleteCustomer(cat.value.id);

            if (customerStore.errorDeleteCustomer) {
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Delete Customer Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Delete Customer Successfully",
                    detail: "Customer Berhasil Di Hapus",
                });
            }

            loading.value = false;
        };

        const restoreCustomer = async (customerId) => {
            loading.value = true;
            await customerStore.restoreCustomer(customerId)

            if (customerStore.errorAddedData) {
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Restore Customer Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                showTrashedCustomerDialog.value = false
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Restore Customer Successfully",
                    detail: "Customer Berhasil Di Pulihkan",
                });
            }
            loading.value = false;
        }

        return {
            customers,
            trashed_customers,
            totalRecords,
            selectedCustomer,
            selectedGender,
            customers,
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
            name,
            nameAttr,
            contact_phone,
            contactPhoneAttr,
            gender,
            genderAttr,
            address,
            addressAttr,
            errors,
            submitCustomer,
            rows,
            rowsPerPageOptions,
            onRowsChange,
            showTrashedCustomerDialog,
            restoreCustomer,
            updateGender,
            breadcrumbIcon,
            breadcrumbItems,
        };
    },
};
</script>
