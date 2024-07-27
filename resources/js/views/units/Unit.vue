<template>
    <Content>
        <template v-slot:content>
            <Toast />
            <div
                class="main-content-unit bg-white rounded-md shadow-md w-full card p-4"
            >
                <span class="text-xl font-bold">Manajemen Satuan</span>
                <ContextMenu
                    ref="cm"
                    :model="menuModel"
                    @hide="selectedUnit = null"
                />
                <div v-if="units">
                    <DataTable
                        :value="units"
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
                        v-model:contextMenuSelection="selectedUnit"
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
                                        <div v-show="trashed_units.length">
                                            <Button icon="pi pi-trash" v-tooltip.top="{ value: 'Sampah' }" @click="showTrashedUnitDialog = true" class="!bg-rose-800 hover:!bg-rose-600 !border-none"/>
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

                        <template #empty> Satuan tidak tersedia. </template>
                        <template #loading>
                            <i
                                class="pi pi-spinner-dotted animate-spin"
                                style="font-size: 2rem"
                            ></i>
                        </template>

                        <Column
                            field="name"
                            header="Nama Satuan"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.name }}
                            </template>
                            <template #editor="{ data, field }">
                                <InputText v-model="data[field]" />
                            </template>
                        </Column>
                        <Column field="isActive" header="Status Aktif">
                            <template #body="slotProps">
                                <div
                                    v-if="slotProps.data.isActive"
                                    class="text-center"
                                >
                                    <Button severity="success" label="Active" />
                                </div>
                                <div v-else class="text-center">
                                    <Button severity="danger" label="Draft" />
                                </div>
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
                        :value="units"
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

                        <template #empty>Satuan tidak tersedia. </template>
                        <template #loading>
                            <i
                                class="pi pi-spinner-dotted animate-spin"
                                style="font-size: 2rem"
                            ></i>
                        </template>
                    </DataTable>
                </div>
            </div>
            <Dialog v-model:visible="showTrashedUnitDialog" :style="{width: '35rem'}" header="Daftar Sampah" :modal="true">
                <div v-for="t in trashed_units" :key="t.id">
                    <div class="flex items-center justify-between mb-2 mt-2">
                        <p>{{ t.name }}</p>
                        <Button size="small" @click="restoreUnit(t.id)" severity="success" label="Pulihkan"/>
                    </div>
                    <hr>
                </div>
                <template #footer>
                    <Button @click="showTrashedUnitDialog = false" icon="pi pi-times" label="Tutup" severity="secondary" text/>
                </template>
            </Dialog>
            <Drawer
                v-model:visible="openDrawer"
                header="Tambah Satuan"
                position="right"
                class="!w-96"
                style="height: 100vh"
            >
                <form @submit="submitUnit">
                    <div class="mt-1">
                        <label for="name" class="text-sm"
                            >Nama Satuan</label
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
                    <div class="mt-3">
                        <label for="isActive" class="text-sm"
                            >Status Aktif</label
                        >
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.isActive }}
                        </p>
                        <Select
                            id="isActive"
                            v-model="selectedIsActiveOption"
                            v-bind="isActiveAttr"
                            :options="[{ name: 'Active', value: 1 }, { name: 'Draft', value: 0}]"
                            optionLabel="name"
                            placeholder="Status Aktif"
                            class="!w-full mt-2"
                            @change="updateSelectedIsActiveOption"
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
import { ref, onMounted, watchEffect, computed } from "vue";
import Content from "../../components/Layout/Content.vue";
import InputText from "primevue/inputtext";
import InputNumber from "primevue/inputnumber";
import InputGroup from "primevue/inputgroup";
import InputGroupAddon from "primevue/inputgroupaddon";
import Textarea from "primevue/textarea";
import FileUpload from "primevue/fileupload";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import ContextMenu from "primevue/contextmenu";
import Drawer from "primevue/drawer";
import { useUnitProductStore } from "../../stores/unit_product";
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
        InputGroup,
        InputGroupAddon,
        IconField,
        InputIcon,
        ContextMenu,
        Drawer,
        Textarea,
        FileUpload,
    },
    setup() {
        const unitStore = useUnitProductStore();
        const units = ref([])
        const trashed_units = ref([])
        const totalRecords = ref(0);
        const selectedUnit = ref();
        const selectedDialogUnit = ref();
        const selectedIsActiveOption = ref(1)
        const showTrashedUnitDialog = ref(false)
        const toast = useToast();
        const dataTable = ref([]);
        const searchQuery = ref('')
        const cm = ref();
        const expandedRows = ref({});
        const loading = ref(false);
        const isExporting = ref(false)
        const openDrawer = ref(false);
        const headers = ref([
            "Nama Satuan",
        ])
        const rows = ref(10)
        const rowsPerPageOptions = ref([5, 10, 20, 30, 40, 50, 100])

        onMounted(async () => {
            await loadUnits();
            await loadTrashedUnits();
        });

        const loadUnits = async (page = 1, rowsPerPage = rows.value) => {
            loading.value = true;

            await unitStore.getUnitPerPage(page, rowsPerPage);
            units.value = unitStore.units;
            totalRecords.value = unitStore.totalRecords;
            loading.value = false;
        };

        const loadTrashedUnits = async () => {
            loading.value = true;

            await unitStore.getTrashedUnit();
            trashed_units.value = unitStore.trashed_units;
            loading.value = false;
        };

        const clearFilter = () => {
            searchQuery.value = ''
        };

        const onPage = (e) => {
            loadUnits(e.page + 1, e.rows)
        };

        const onRowsChange = (event) => {
            rows.value = event.rows;
            loadUnits(1, event.rows);
        };

        const debouncedSearch = debounce((value) => {
            unitStore.setFilter('search', value)
            loadUnits()
        }, 500)

        watchEffect(() => {
            debouncedSearch(searchQuery.value)
        })

        const exportExcel = async () => {
            isExporting.value = true
            const { utils, writeFileXLSX } = await import("xlsx");

            if (dataTable.value) {
                const date = new Date()

                const data = units.value.map((unit) => ({
                    "Nama Satuan": unit.name,
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

                writeFileXLSX(workbook, `DataSatuan_${date.getDate()}_${date.getMonth() + 1}_${date.getFullYear()}.xlsx`);
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
                const data = units.value.map((unit) => ({
                    "Nama Satuan": unit.name,
                }));

                const tableData = data.map(item => headers.value.map(header => item[header]))

                autoTable(doc, {
                    head: [headers.value],
                    body: tableData
                })
    
                doc.save(`DataSatuan_${date.getDate()}_${date.getMonth() + 1}_${date.getFullYear()}.pdf`)
                isExporting.value = false
            } else {
                isExporting.value = false
                console.log(dataTable.value, "Datatable ref is null");
            }
        };

        const { errors, handleSubmit, defineField } = useForm({
            validationSchema: Yup.object().shape({
                name: Yup.string().required("Nama Satuan wajib diisi."),
                isActive: Yup.number().required("Pilih status aktif."),
            }),
        });

        const [name, nameAttr] = defineField("name");
        const [isActive, isActiveAttr] = defineField("isActive");

        const updateSelectedIsActiveOption = (event) => {
            isActive.value = event.value ? event.value.value : null
        }
        const submitUnit = handleSubmit(async (datas, { resetForm }) => {
            loading.value = true;
            await unitStore.addUnit(datas);

            if (unitStore.errorAddedData) {
                loading.value = false;
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Added Unit Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                openDrawer.value = false;
                loading.value = false;
                resetForm();
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Added Unit Successfully",
                    detail: "Tambah Satuan Berhasil",
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

            await unitStore.updateDataUnit(filteredNewData, data.id);

            if (unitStore.errorUpdateData) {
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Update Unit Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Update Unit Successfully",
                    detail: "Update Satuan Berhasil",
                });
            }

            loading.value = false;
        };

        const labelStatusUnit = ref('')
        const iconStatusUnit = ref('')
        const menuModel = computed(() => [
            {
                label: "Delete",
                icon: "pi pi-fw pi-times",
                command: () => deleteUnit(selectedUnit),
            },
            {
                label: labelStatusUnit.value,
                icon: iconStatusUnit.value,
                command: () => setStatusUnit(selectedUnit),
            },
        ]);
        const onRowContextMenu = (event) => {
            if (event.data.isActive) {
                labelStatusUnit.value = "Draft"
                iconStatusUnit.value = "pi pi-fw pi-lock"
            } else {
                labelStatusUnit.value = "Active"
                iconStatusUnit.value = "pi pi-fw pi-lock-open"
            }
            cm.value.show(event.originalEvent);
        };

        const deleteUnit = async (cat) => {
            loading.value = true;
            await unitStore.deleteUnit(cat.value.id);

            if (unitStore.errorDeleteUnit) {
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Delete Unit Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Delete Unit Successfully",
                    detail: "Satuan Berhasil Di Hapus",
                });
            }

            loading.value = false;
        };

        const restoreUnit = async (unitId) => {
            loading.value = true;
            await unitStore.restoreUnit(unitId)

            if (unitStore.errorAddedData) {
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Restore Unit Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                showTrashedUnitDialog.value = false
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Restore Unit Successfully",
                    detail: "Satuan Berhasil Di Pulihkan",
                });
            }
            loading.value = false;
        }

        const setStatusUnit = async (cat) => {
            loading.value = true;
            const status = ref()
            if (cat.value.isActive) {
                status.value = 0
            } else {
                status.value = 1
            }
            await unitStore.setStatus(status.value, cat.value.id);

            if (unitStore.errorUpdateData) {
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Update Status Unit Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Update Status Unit Successfully",
                    detail: "Update Status Satuan Berhasil",
                });
            }

            loading.value = false;
        }

        return {
            units,
            trashed_units,
            totalRecords,
            selectedUnit,
            selectedDialogUnit,
            selectedUnit,
            units,
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
            isActive,
            isActiveAttr,
            selectedIsActiveOption,
            updateSelectedIsActiveOption,
            errors,
            submitUnit,
            rows,
            rowsPerPageOptions,
            onRowsChange,
            showTrashedUnitDialog,
            restoreUnit
        };
    },
};
</script>
