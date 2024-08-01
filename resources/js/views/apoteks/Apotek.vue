<template>
    <Content>
        <template v-slot:content>
            <Toast />
            <div
                class="main-content-apotek bg-white rounded-md shadow-md w-full card p-4"
            >
                <span class="text-xl font-bold">Manajemen Apotek</span>
                <ContextMenu
                    ref="cm"
                    :model="menuModel"
                    @hide="selectedApotek = null"
                />
                <div v-if="apoteks">
                    <DataTable
                        :value="apoteks"
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
                        v-model:contextMenuSelection="selectedApotek"
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
                                        <div v-show="trashed_apoteks.length">
                                            <Button icon="pi pi-trash" v-tooltip.top="{ value: 'Sampah' }" @click="showTrashedApotekDialog = true" class="!bg-rose-800 hover:!bg-rose-600 !border-none"/>
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

                        <template #empty> Apotek tidak tersedia. </template>
                        <template #loading>
                            <i
                                class="pi pi-spinner-dotted animate-spin"
                                style="font-size: 2rem"
                            ></i>
                        </template>

                        <Column
                            field="name_of_apotek"
                            header="Nama Apotek"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.name_of_apotek }}
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
                            field="bio"
                            header="Bio"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.bio }}
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
                        :value="apoteks"
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

                        <template #empty>Apotek tidak tersedia. </template>
                        <template #loading>
                            <i
                                class="pi pi-spinner-dotted animate-spin"
                                style="font-size: 2rem"
                            ></i>
                        </template>
                    </DataTable>
                </div>
            </div>
            <Dialog v-model:visible="showTrashedApotekDialog" :style="{width: '35rem'}" header="Daftar Sampah" :modal="true">
                <div v-for="t in trashed_apoteks" :key="t.id">
                    <div class="flex items-center justify-between mb-2 mt-2">
                        <p>{{ t.name_of_apotek }}</p>
                        <Button size="small" @click="restoreApotek(t.id)" severity="success" label="Pulihkan"/>
                    </div>
                    <hr>
                </div>
                <template #footer>
                    <Button @click="showTrashedApotekDialog = false" icon="pi pi-times" label="Tutup" severity="secondary" text/>
                </template>
            </Dialog>
            <Drawer
                v-model:visible="openDrawer"
                header="Tambah Apotek"
                position="right"
                class="!w-96"
                style="height: 100vh"
            >
                <form @submit="submitApotek">
                    <div class="mt-1">
                        <label for="name_of_apotek" class="text-sm"
                            >Nama Apotek</label
                        >
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.name_of_apotek }}
                        </p>
                        <InputText
                            id="name_of_apotek"
                            v-model="name_of_apotek"
                            v-bind="nameOfApotekAttr"
                            class="!w-full mt-2"
                        />
                    </div>
                    <div class="mt-1">
                        <label for="email" class="text-sm"
                            >Email Apotek</label
                        >
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
                            >Kontak Apotek</label
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
                        <label for="bio" class="text-sm"
                            >Bio Apotek</label
                        >
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.bio }}
                        </p>
                        <Textarea
                            id="bio"
                            v-model="bio"
                            v-bind="bioAttr"
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
import { useApotekStore } from "../../stores/apotek";
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
        const apotekStore = useApotekStore();
        const apoteks = ref([])
        const trashed_apoteks = ref([])
        const totalRecords = ref(0);
        const selectedApotek = ref();
        const showTrashedApotekDialog = ref(false)
        const toast = useToast();
        const dataTable = ref([]);
        const searchQuery = ref(apotekStore.filters.search || '')
        const cm = ref();
        const expandedRows = ref({});
        const loading = ref(false);
        const isExporting = ref(false)
        const openDrawer = ref(false);
        const headers = ref([
            "Nama Apotek",
            "Email",
            "Nomor Kontak",
            "Kota",
            "Provinsi",
            "Kode Pos",
            "Alamat Lengkap",
        ])
        const rows = ref(10)
        const rowsPerPageOptions = ref([5, 10, 20, 30, 40, 50, 100])
        const isMounted = ref(false);

        onMounted(async () => {
            searchQuery.value = apotekStore.filters.search || ''
            await loadApoteks();
            await loadTrashedApoteks();
            isMounted.value = true;
        });

        const loadApoteks = async (page = 1, rowsPerPage = rows.value) => {
            loading.value = true;

            await apotekStore.getApotekPerPage(page, rowsPerPage);
            apoteks.value = apotekStore.apoteks;
            totalRecords.value = apotekStore.totalRecords;
            loading.value = false;
        };

        const loadTrashedApoteks = async () => {
            loading.value = true;

            await apotekStore.getTrashedApotek();
            trashed_apoteks.value = apotekStore.trashed_apoteks;
            loading.value = false;
        };

        const clearFilter = () => {
            searchQuery.value = ''
        };

        const onPage = (e) => {
            loadApoteks(e.page + 1, e.rows)
        };

        const onRowsChange = (event) => {
            rows.value = event.rows;
            loadApoteks(1, event.rows);
        };

        const debouncedSearch = debounce((value) => {
            apotekStore.setFilter('search', value)
            loadApoteks()
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

                const data = apoteks.value.map((apotek) => ({
                    "Nama Apotek": apotek.name_of_apotek,
                    "Email": apotek.email,
                    "Nomor Kontak": apotek.contact_phone,
                    "Kota": apotek.city,
                    "Provinsi": apotek.province,
                    "Kode Pos": apotek.zip_code,
                    "Alamat Lengkap": apotek.address,
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

                writeFileXLSX(workbook, `DataApotek_${date.getDate()}_${date.getMonth() + 1}_${date.getFullYear()}.xlsx`);
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
                const data = apoteks.value.map((apotek) => ({
                    "Nama Apotek": apotek.name_of_apotek,
                    "Email": apotek.email,
                    "Nomor Kontak": apotek.contact_phone,
                    "Kota": apotek.city,
                    "Provinsi": apotek.province,
                    "Kode Pos": apotek.zip_code,
                    "Alamat Lengkap": apotek.address,
                }));

                const tableData = data.map(item => headers.value.map(header => item[header]))

                autoTable(doc, {
                    head: [headers.value],
                    body: tableData
                })
    
                doc.save(`DataApotek_${date.getDate()}_${date.getMonth() + 1}_${date.getFullYear()}.pdf`)
                isExporting.value = false
            } else {
                isExporting.value = false
                console.log(dataTable.value, "Datatable ref is null");
            }
        };

        const { errors, handleSubmit, defineField } = useForm({
            validationSchema: Yup.object().shape({
                name_of_apotek: Yup.string().required("Nama Apotek wajib diisi."),
                email: Yup.string().email("Alamat email harus valid").required("Alamat email wajib diisi."),
                contact_phone: Yup.string().matches(/^62[0-9]+$/, "No HP berawalan 62").required("No HP apotek wajib diisi."),
                city: Yup.string().required("Kota wajib diisi."),
                province: Yup.string().required("Provinsi wajib diisi."),
                zip_code: Yup.string().required("Kode Pos wajib diisi."),
                address: Yup.string().required("Alamat lengkap wajib diisi."),
                bio: Yup.string().required("Bio wajib diisi."),
            }),
        });

        const [name_of_apotek, nameOfApotekAttr] = defineField("name_of_apotek");
        const [email, emailAttr] = defineField("email");
        const [contact_phone, contactPhoneAttr] = defineField("contact_phone");
        const [city, cityAttr] = defineField("city");
        const [province, provinceAttr] = defineField("province");
        const [zip_code, zipCodeAttr] = defineField("zip_code");
        const [address, addressAttr] = defineField("address");
        const [bio, bioAttr] = defineField("bio");

        const submitApotek = handleSubmit(async (datas, { resetForm }) => {
            loading.value = true;
            await apotekStore.addApotek(datas);

            if (apotekStore.errorAddedData) {
                loading.value = false;
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Added Apotek Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                openDrawer.value = false;
                loading.value = false;
                resetForm();
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Added Apotek Successfully",
                    detail: "Tambah Apotek Berhasil",
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

            await apotekStore.updateDataApotek(filteredNewData, data.id);

            if (apotekStore.errorUpdateData) {
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Update Apotek Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Update Apotek Successfully",
                    detail: "Update Apotek Berhasil",
                });
            }

            loading.value = false;
        };

        const menuModel = computed(() => [
            {
                label: "Delete",
                icon: "pi pi-fw pi-times",
                command: () => deleteApotek(selectedApotek),
            },
        ]);
        const onRowContextMenu = (event) => {
            cm.value.show(event.originalEvent);
        };

        const deleteApotek = async (cat) => {
            loading.value = true;
            await apotekStore.deleteApotek(cat.value.id);

            if (apotekStore.errorDeleteApotek) {
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Delete Apotek Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Delete Apotek Successfully",
                    detail: "Apotek Berhasil Di Hapus",
                });
            }

            loading.value = false;
        };

        const restoreApotek = async (apotekId) => {
            loading.value = true;
            await apotekStore.restoreApotek(apotekId)

            if (apotekStore.errorAddedData) {
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Restore Apotek Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                showTrashedApotekDialog.value = false
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Restore Apotek Successfully",
                    detail: "Apotek Berhasil Di Pulihkan",
                });
            }
            loading.value = false;
        }

        return {
            apoteks,
            trashed_apoteks,
            totalRecords,
            selectedApotek,
            selectedApotek,
            apoteks,
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
            name_of_apotek,
            nameOfApotekAttr,
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
            bio,
            bioAttr,
            errors,
            submitApotek,
            rows,
            rowsPerPageOptions,
            onRowsChange,
            showTrashedApotekDialog,
            restoreApotek
        };
    },
};
</script>
