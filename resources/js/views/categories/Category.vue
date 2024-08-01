<template>
    <Content>
        <template v-slot:content>
            <Toast />
            <div
                class="main-content-category bg-white rounded-md shadow-md w-full card p-4"
            >
                <span class="text-xl font-bold">Manajemen Kategori</span>
                <ContextMenu
                    ref="cm"
                    :model="menuModel"
                    @hide="selectedCategory = null"
                />
                <div v-if="categories">
                    <DataTable
                        :value="categories"
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
                        v-model:contextMenuSelection="selectedCategory"
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
                                    <div v-show="trashed_categories.length">
                                        <Button icon="pi pi-trash" v-tooltip.top="{ value: 'Sampah' }" @click="showTrashedCategoriesDialog = true" class="!bg-rose-800 hover:!bg-rose-600 !border-none"/>
                                    </div>
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

                        <template #empty> Kategori tidak tersedia. </template>
                        <template #loading>
                            <i
                                class="pi pi-spinner-dotted animate-spin"
                                style="font-size: 2rem"
                            ></i>
                        </template>

                        <Column
                            field="name"
                            header="Nama Kategori"
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
                        :value="categories"
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

                        <template #empty>Kategori tidak tersedia. </template>
                        <template #loading>
                            <i
                                class="pi pi-spinner-dotted animate-spin"
                                style="font-size: 2rem"
                            ></i>
                        </template>
                    </DataTable>
                </div>
            </div>
            <Dialog v-model:visible="showTrashedCategoriesDialog" :style="{width: '35rem'}" header="Daftar Sampah" :modal="true">
                <div v-for="t in trashed_categories" :key="t.id">
                    <div class="flex items-center justify-between mb-2 mt-2">
                        <p>{{ t.name }}</p>
                        <Button size="small" @click="restoreCategory(t.id)" severity="success" label="Pulihkan"/>
                    </div>
                    <hr>
                </div>
                <template #footer>
                    <Button @click="showTrashedCategoriesDialog = false" icon="pi pi-times" label="Tutup" severity="secondary" text/>
                </template>
            </Dialog>
            <Drawer
                v-model:visible="openDrawer"
                header="Tambah Kategori"
                position="right"
                class="!w-96"
                style="height: 100vh"
            >
                <form @submit="submitCategory">
                    <div class="mt-1">
                        <label for="name" class="text-sm"
                            >Nama Kategori</label
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
import { ref, onMounted, watch, computed } from "vue";
import Content from "../../components/Layout/Content.vue";
import InputText from "primevue/inputtext";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import ContextMenu from "primevue/contextmenu";
import Drawer from "primevue/drawer";
import { useCategoryStore } from "../../stores/category";
import { formatCurrencyIDR } from "../../helpers/formatCurrency";
import { debounce } from "../../helpers/debounce";
import { useToast } from "primevue/usetoast";
import { useForm } from "vee-validate";
import * as Yup from "yup";

export default {
    components: {
        Content,
        InputText,
        IconField,
        InputIcon,
        ContextMenu,
        Drawer,
    },
    setup() {
        const categoryStore = useCategoryStore();
        const categories = ref([])
        const trashed_categories = ref([])
        const totalRecords = ref(0);
        const selectedCategory = ref();
        const selectedDialogCategory = ref();
        const showTrashedCategoriesDialog = ref(false)
        const selectedIsActiveOption = ref(1)
        const toast = useToast();
        const dataTable = ref([]);
        const searchQuery = ref(categoryStore.filters.search || '')
        const cm = ref();
        const expandedRows = ref({});
        const loading = ref(false);
        const isExporting = ref(false)
        const openDrawer = ref(false);
        const headers = ref([
            "Nama Kategori",
        ])
        const rows = ref(10)
        const rowsPerPageOptions = ref([5, 10, 20, 30, 40, 50, 100])

        onMounted(async () => {
            searchQuery.value = categoryStore.filters.search || ''
            await loadCategories();
            await loadTrashedCategories();
        });

        const loadCategories = async (page = 1, rowsPerPage = rows.value) => {
            loading.value = true;

            await categoryStore.getCategoryPerPage(page, rowsPerPage);
            categories.value = categoryStore.categories;
            totalRecords.value = categoryStore.totalRecords;
            loading.value = false;
        };

        const loadTrashedCategories = async () => {
            loading.value = true;

            await categoryStore.getTrashedCategories();
            trashed_categories.value = categoryStore.trashed_categories;
            loading.value = false;
        };

        const clearFilter = () => {
            searchQuery.value = ''
        };

        const onPage = (e) => {
            loadCategories(e.page + 1, e.rows)
        };

        const onRowsChange = (event) => {
            rows.value = event.rows;
            loadCategories(1, event.rows);
        };

        const debouncedSearch = debounce((value) => {
            categoryStore.setFilter('search', value)
            loadCategories()
        }, 500)

        watch(searchQuery, (newQuery, oldQuery) => {
            if (newQuery !== oldQuery) {
                debouncedSearch(newQuery)
            }
        })

        const exportExcel = async () => {
            isExporting.value = true
            const { utils, writeFileXLSX } = await import("xlsx");

            if (dataTable.value) {
                const date = new Date()

                const data = categories.value.map((category) => ({
                    "Nama Kategori": category.name,
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

                writeFileXLSX(workbook, `DataKategori_${date.getDate()}_${date.getMonth() + 1}_${date.getFullYear()}.xlsx`);
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
                const data = categories.value.map((category) => ({
                    "Nama Kategori": category.name,
                }));

                const tableData = data.map(item => headers.value.map(header => item[header]))

                autoTable(doc, {
                    head: [headers.value],
                    body: tableData
                })
    
                doc.save(`DataKategori_${date.getDate()}_${date.getMonth() + 1}_${date.getFullYear()}.pdf`)
                isExporting.value = false
            } else {
                isExporting.value = false
                console.log(dataTable.value, "Datatable ref is null");
            }
        };

        const { errors, handleSubmit, defineField } = useForm({
            validationSchema: Yup.object().shape({
                name: Yup.string().required("Nama Kategori wajib diisi."),
                isActive: Yup.number().required("Pilih status aktif."),
            }),
        });

        const [name, nameAttr] = defineField("name");
        const [isActive, isActiveAttr] = defineField("isActive");

        const updateSelectedIsActiveOption = (event) => {
            isActive.value = event.value ? event.value.value : null
        }
        const submitCategory = handleSubmit(async (datas, { resetForm }) => {
            loading.value = true;
            await categoryStore.addCategory(datas);

            if (categoryStore.errorAddedData) {
                loading.value = false;
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Added Category Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                openDrawer.value = false;
                loading.value = false;
                resetForm();
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Added Category Successfully",
                    detail: "Tambah Kategori Berhasil",
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

            await categoryStore.updateDataCategory(filteredNewData, data.id);

            if (categoryStore.errorUpdateData) {
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Update Category Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Update Category Successfully",
                    detail: "Update Kategori Berhasil",
                });
            }

            loading.value = false;
        };

        const labelStatusCategory = ref('')
        const iconStatusCategory = ref('')
        const menuModel = computed(() => [
            {
                label: "Delete",
                icon: "pi pi-fw pi-times",
                command: () => deleteCategory(selectedCategory),
            },
            {
                label: labelStatusCategory.value,
                icon: iconStatusCategory.value,
                command: () => setStatusCategory(selectedCategory),
            },
        ]);
        const onRowContextMenu = (event) => {
            if (event.data.isActive) {
                labelStatusCategory.value = "Draft"
                iconStatusCategory.value = "pi pi-fw pi-lock"
            } else {
                labelStatusCategory.value = "Active"
                iconStatusCategory.value = "pi pi-fw pi-lock-open"
            }
            cm.value.show(event.originalEvent);
        };

        const deleteCategory = async (cat) => {
            loading.value = true;
            await categoryStore.deleteCategory(cat.value.id);

            if (categoryStore.errorDeleteCategory) {
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Delete Category Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Delete Category Successfully",
                    detail: "Kategori Berhasil Di Hapus",
                });
            }

            loading.value = false;
        };

        const restoreCategory = async (categoryId) => {
            loading.value = true;
            await categoryStore.restoreCategory(categoryId)

            if (categoryStore.errorAddedData) {
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Restore Category Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                showTrashedCategoriesDialog.value = false
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Restore Category Successfully",
                    detail: "Kategori Berhasil Di Pulihkan",
                });
            }
            loading.value = false;
        }

        const setStatusCategory = async (cat) => {
            loading.value = true;
            const status = ref()
            if (cat.value.isActive) {
                status.value = 0
            } else {
                status.value = 1
            }
            await categoryStore.setStatus(status.value, cat.value.id);

            if (categoryStore.errorUpdateData) {
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Update Status Category Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Update Status Category Successfully",
                    detail: "Update Status Kategori Berhasil",
                });
            }

            loading.value = false;
        }

        return {
            categories,
            trashed_categories,
            totalRecords,
            selectedCategory,
            selectedDialogCategory,
            selectedCategory,
            categories,
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
            submitCategory,
            rows,
            rowsPerPageOptions,
            onRowsChange,
            showTrashedCategoriesDialog,
            restoreCategory
        };
    },
};
</script>
