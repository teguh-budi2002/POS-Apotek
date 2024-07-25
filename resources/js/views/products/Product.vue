<template>
    <Content>
        <template v-slot:content>
            <Toast />
            <div
                class="main-content-product bg-white rounded-md shadow-md w-full card p-4"
            >
                <span class="text-xl font-bold">Manajemen Produk</span>
                <ContextMenu
                    ref="cm"
                    :model="menuModel"
                    @hide="selectedProduct = null"
                />
                <div v-if="products">
                    <DataTable
                        :value="products"
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
                        v-model:contextMenuSelection="selectedProduct"
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

                        <template #empty> Produk tidak tersedia. </template>
                        <template #loading>
                            <i
                                class="pi pi-spinner-dotted animate-spin"
                                style="font-size: 2rem"
                            ></i>
                        </template>

                        <Column
                            field="product_code"
                            header="Kode Produk"
                            exportHeader="Kode Produk"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.product_code }}
                            </template>
                            <template #editor="{ data, field }">
                                <InputText v-model="data[field]" />
                            </template>
                        </Column>
                        <Column
                            field="name"
                            header="Nama Produk"
                            exportHeader="Nama Produk"
                        >
                            <template #body="slotProps">
                                {{ slotProps.data.name }}
                            </template>
                            <template #editor="{ data, field }">
                                <InputText v-model="data[field]" />
                            </template>
                        </Column>
                        <Column
                            field="unit_price"
                            header="Harga Produk"
                            exportHeader="Harga Produk"
                        >
                            <template #body="slotProps">
                                {{
                                    formatCurrencyIDR(slotProps.data.unit_price)
                                }}
                            </template>
                            <template #editor="{ data, field }">
                                <InputText v-model="data[field]" />
                            </template>
                        </Column>
                        <Column
                            field="category_product_id"
                            header="Kategori Produk"
                            exportHeader="Kategori"
                        >
                            <template #body="slotProps">
                                <div v-if="slotProps.data.category">
                                    {{ slotProps.data.category.name }}
                                </div>
                                <div v-else>
                                    <p class="text-xs text-rose-500">Kategori Di Nonaktifkan</p>
                                </div>
                            </template>
                            <template #editor="{ data, field }">
                                <Select
                                    v-model="data[field]"
                                    :options="categories"
                                    optionLabel="name"
                                    optionValue="id"
                                    checkmark
                                    :highlightOnSelect="false"
                                    class="w-full md:w-56"
                                />
                            </template>
                        </Column>
                        <Column
                            field="stock.name"
                            header="Stock"
                            exportHeader="Stock"
                        >
                            <template #body="slotProps">
                                <div v-if="slotProps.data.stock">
                                    <p class="text-center">
                                        {{ slotProps.data.stock.stock }}
                                    </p>
                                </div>
                                <div v-else>
                                    <p class="text-center">0</p>
                                </div>
                            </template>
                            <template #editor="slotProps">
                                <div v-if="slotProps.data.stock">
                                    <p class="text-center">
                                        {{ slotProps.data.stock.stock }}
                                    </p>
                                </div>
                                <div v-else>
                                    <p class="text-center">0</p>
                                </div>
                                <router-link to="">
                                    <Button
                                        label="Update Stok"
                                        size="small"
                                        severity="help"
                                        class="mt-2"
                                    />
                                </router-link>
                            </template>
                        </Column>
                        <Column
                            field="unit_product_id"
                            header="Satuan"
                            exportHeader="Satuan"
                        >
                            <template #body="slotProps">
                                <div v-if="slotProps.data.unit">
                                    {{ slotProps.data.unit.name }}
                                </div>
                                <div v-else>
                                    <p class="text-xs text-rose-500">Satuan Di Nonaktifkan</p>
                                </div>
                            </template>
                            <template #editor="{ data, field }">
                                <Select
                                    v-model="data[field]"
                                    :options="units"
                                    optionLabel="name"
                                    optionValue="id"
                                    checkmark
                                    :highlightOnSelect="false"
                                    class="w-full md:w-56"
                                />
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
                        <Column header="Detail">
                            <template #body="slotProps">
                                <div class="text-center">
                                    <Button
                                        icon="pi pi-eye"
                                        text
                                        raised
                                        severity="secondary"
                                        rounded
                                        class="!text-blue-500"
                                        @click="
                                            showDetailProduct(slotProps.data)
                                        "
                                    />
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
                        :value="products"
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

                        <template #empty> Produk tidak tersedia. </template>
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
                v-model:visible="showDialogDetailProduct"
                modal
                header="Detail Produk"
                :style="{ width: '45rem' }"
                :breakpoints="{}"
            >
                <div class="grid grid-cols-3 gap-4">
                    <div class="img_product">
                        <Image
                            :src="`/storage/product/${selectedDialogProduct.img_product}`"
                            alt="product image"
                            preview
                        />
                    </div>
                    <div class="detail_product col-span-2">
                        <p>
                            Nama Produk:
                            {{ selectedDialogProduct.name }}
                        </p>
                        <div v-if="selectedDialogProduct.category" class="flex items-center space-x-1">
                            <p>Kategori Produk:</p>
                            <p>{{ selectedDialogProduct.category.name }}</p>
                        </div>
                        <div v-else class="flex items-center space-x-1">
                            <p>Kategori Produk:</p>
                            <p class="text-rose-500">Kategori Di Nonaktifkan</p>
                        </div>
                        <p>
                            Harga Produk:
                            {{
                                formatCurrencyIDR(
                                    selectedDialogProduct.unit_price
                                )
                            }}
                        </p>
                        <div v-if="selectedDialogProduct.unit" class="flex items-center space-x-1">
                            <p>Satuan Produk:</p>
                            <p>{{ selectedDialogProduct.unit.name }}</p>
                        </div>
                        <div v-else class="flex items-center space-x-1">
                            <p>Satuan Produk:</p>
                            <p class="text-rose-500">Satuan Di Nonaktifkan</p>
                        </div>
                        <div v-if="selectedDialogProduct.stock">
                            <p>
                                Stok Produk:
                                {{ selectedDialogProduct.stock.stock }}
                            </p>
                            <p>
                                Minimum Level Stok:
                                {{
                                    selectedDialogProduct.stock
                                        .minimum_stock_level
                                }}
                            </p>
                            <p>
                                Maximum Level Stok:
                                {{
                                    selectedDialogProduct.stock
                                        .maximum_stock_level
                                }}
                            </p>
                        </div>
                        <div v-else>
                            <p>Stok Produk: 0</p>
                        </div>
                        <p>
                            Deskripsi Produk:
                            {{ selectedDialogProduct.description ? selectedDialogProduct.description : 'Tidak Ada' }}
                        </p>
                    </div>
                </div>
                <template #footer>
                    <Button
                        label="Tutup"
                        text
                        severity="secondary"
                        raised
                        @click="showDialogDetailProduct = false"
                        autofocus
                    />
                </template>
            </Dialog>
            <Drawer
                v-model:visible="openDrawer"
                header="Tambah Produk"
                position="right"
                class="!w-96"
                style="height: 100vh"
            >
                <form @submit="submitProduct" enctype="multipart/form-data">
                    <div class="mt-1">
                        <label for="code_product" class="text-sm"
                            >Kode Produk</label
                        >
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.product_code }}
                        </p>
                        <InputText
                            id="code_product"
                            v-model="product_code"
                            v-bind="productCodeAttr"
                            class="!w-full mt-2"
                        />
                    </div>
                    <div class="mt-3">
                        <label for="name_product" class="text-sm"
                            >Nama Produk</label
                        >
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.name }}
                        </p>
                        <InputText
                            id="name_product"
                            v-model="name"
                            v-bind="nameAttr"
                            class="!w-full mt-2"
                        />
                    </div>
                    <div class="mt-3">
                        <label for="price" class="text-sm">Harga Produk</label>
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.unit_price }}
                        </p>
                        <InputGroup class="mt-2">
                            <InputGroupAddon>IDR</InputGroupAddon>
                            <InputNumber
                                id="price"
                                inputId="locale-id"
                                prefix="Rp "
                                v-model="unit_price"
                                v-bind="unitPriceAttr"
                            />
                        </InputGroup>
                    </div>
                    <div class="mt-3">
                        <label for="category_product" class="text-sm"
                            >Kategori Produk</label
                        >
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.category_product_id }}
                        </p>
                        <Select
                            id="category_product"
                            v-model="selectedCategory"
                            v-bind="categoryProductIdAttr"
                            :options="categories"
                            optionLabel="name"
                            placeholder="Pilih Kategori"
                            class="!w-full mt-2"
                            @change="updateCategoryProductId"
                        />
                    </div>
                    <div class="mt-3">
                        <label for="unit_product" class="text-sm">Satuan</label>
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.unit_product_id }}
                        </p>
                        <Select
                            id="unit_product"
                            v-model="selectedUnit"
                            v-bind="unitProductIdAttr"
                            :options="units"
                            optionLabel="name"
                            placeholder="Pilih Satuan"
                            class="!w-full mt-2"
                            @change="updateUnitProductId"
                        />
                    </div>
                    <div class="mt-3">
                        <label for="stock" class="text-sm block">Stock</label>
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.stock }}
                        </p>
                        <InputNumber
                            id="stock"
                            v-model="stock"
                            v-bind="stockAttr"
                            class="mt-2"
                        />
                    </div>
                    <div class="mt-3">
                        <label for="min_stock" class="text-sm block"
                            >Min Stock</label
                        >
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.minimum_stock_level }}
                        </p>
                        <InputNumber
                            id="min_stock"
                            v-model="minimum_stock_level"
                            v-bind="minimumStockLevelAttr"
                            class="mt-2"
                        />
                    </div>
                    <div class="mt-3">
                        <label for="max_stock" class="text-sm block"
                            >Max Stock
                            <span class="text-xs text-rose-500"
                                >(opsional)</span
                            ></label
                        >
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.maximum_stock_level }}
                        </p>
                        <InputNumber
                            id="max_stock"
                            v-model="maximum_stock_level"
                            v-bind="maximumStockLevelAttr"
                            class="mt-2"
                        />
                    </div>
                    <div class="mt-3 flex flex-col items-start">
                        <label for="img+product" class="text-sm block"
                            >Foto Produk</label
                        >
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.img_product }}
                        </p>
                        <FileUpload
                            ref="fileupload"
                            mode="basic"
                            accept="image/*"
                            class="mt-2 !bg-slate-900 hover:!bg-slate-700"
                            :maxFileSize="2048000"
                            v-bind="imgProductAttr"
                            @select="onFileSelect"
                        />
                    </div>
                    <div class="mt-3">
                        <label for="description" class="text-sm block"
                            >Deskripsi
                            <span class="text-xs text-rose-500"
                                >(opsional)</span
                            ></label
                        >
                        <p class="mt-2 text-rose-500 text-xs">
                            {{ errors.description }}
                        </p>
                        <Textarea
                            id="description"
                            autoResize
                            v-model="description"
                            v-bind="descriptionAttr"
                            class="mt-2 !w-full !min-h-24"
                        />
                    </div>
                    <div class="mt-2 flex items-center space-x-3">
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
import { useProductStore } from "../../stores/product";
import { useCategoryStore } from "../../stores/category";
import { formatCurrencyIDR } from "../../helpers/formatCurrency";
import { debounce } from "../../helpers/debounce";
import { useUnitProductStore } from "../../stores/unit_product";
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
        const productStore = useProductStore();
        const products = ref([]);
        const selectedProduct = ref();
        const selectedDialogProduct = ref();
        const totalRecords = ref(0);
        const selectedCategory = ref();
        const selectedUnit = ref();
        const fileupload = ref();
        const { getAllCategoryProduct, categories } = useCategoryStore();
        const { getAllUnitProduct, units } = useUnitProductStore();
        const toast = useToast();
        const showDialogDetailProduct = ref(false);
        const dataTable = ref([]);
        const searchQuery = ref('')
        const cm = ref();
        const expandedRows = ref({});
        const loading = ref(false);
        const isExporting = ref(false)
        const openDrawer = ref(false);
        const headers = ref([
            "Kode Produk",
            "Nama Produk",
            "Kategori",
            "Harga",
            "Satuan",
            "Stok",
            "Min Stok Level",
            "Max Stok Level",
        ])
        const rows = ref(10)
        const rowsPerPageOptions = ref([5, 10, 20, 30, 40, 50, 100])

        onMounted(async () => {
            await loadProducts();
            await loadCategoryProduct();
            await loadUnitProduct();
        });

        const loadProducts = async (page = 1, rowsPerPage = rows.value) => {
            loading.value = true;

            await productStore.getProductsPerPage(page, rowsPerPage);
            products.value = productStore.products;
            totalRecords.value = productStore.totalRecords;

            loading.value = false;
        };

        const loadCategoryProduct = async () => {
            await getAllCategoryProduct();
        };

        const loadUnitProduct = async () => {
            await getAllUnitProduct();
        };

        const clearFilter = () => {
            searchQuery.value = ''
        };

        const onPage = (e) => {
            loadProducts(e.page + 1, e.rows)
        };

        const onRowsChange = (event) => {
            rows.value = event.rows;
            loadProducts(1, event.rows);
        };

        const debouncedSearch = debounce((value) => {
            productStore.setFilter('search', value)
            loadProducts()
        }, 500)

        watchEffect(() => {
            debouncedSearch(searchQuery.value)
        })

        const exportExcel = async () => {
            isExporting.value = true
            const { utils, writeFileXLSX } = await import("xlsx");

            if (dataTable.value) {
                const date = new Date()

                const data = products.value.map((product) => ({
                    "Kode Produk": product.product_code,
                    "Nama Produk": product.name,
                    "Kategori": product.category?.name,
                    "Harga": formatCurrencyIDR(product.unit_price),
                    "Satuan": product.unit.name,
                    "Stok": product.stock?.stock,
                    "Min Stok Level": product.stock?.minimum_stock_level,
                    "Max Stok Level": product.stock?.maximum_stock_level,
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

                writeFileXLSX(workbook, `DataProduk_${date.getDate()}_${date.getMonth() + 1}_${date.getFullYear()}.xlsx`);
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
                const data = products.value.map((product) => ({
                    "Kode Produk": product.product_code,
                    "Nama Produk": product.name,
                    "Kategori": product.category?.name,
                    "Harga": formatCurrencyIDR(product.unit_price),
                    "Satuan": product.unit.name,
                    "Stok": product.stock?.stock,
                    "Min Stok Level": product.stock?.minimum_stock_level,
                    "Max Stok Level": product.stock?.maximum_stock_level,
                }));

                const tableData = data.map(item => headers.value.map(header => item[header]))

                autoTable(doc, {
                    head: [headers.value],
                    body: tableData
                })
    
                doc.save(`DataProduk_${date.getDate()}_${date.getMonth() + 1}_${date.getFullYear()}.pdf`)
                isExporting.value = false
            } else {
                isExporting.value = false
                console.log(dataTable.value, "Datatable ref is null");
            }
        };

        const { errors, handleSubmit, defineField } = useForm({
            validationSchema: Yup.object().shape({
                name: Yup.string().required("Nama wajib diisi."),
                product_code: Yup.string()
                    .required("Kode produk wajib diisi.")
                    .max(6, "Maksimal Kode Produk 6 karakter"),
                category_product_id: Yup.string().required(
                    "Pilih kategori produk"
                ),
                unit_product_id: Yup.string().required("Pilih satuan produk"),
                unit_price: Yup.number().required("Harga produk wajib diisi."),
                stock: Yup.number("Stock harus berupa angka").required(
                    "Stock produk wajib diisi."
                ),
                minimum_stock_level: Yup.number(
                    "Min stock harus berupa angka"
                ).required("Stock minimum produk wajib diisi."),
                maximum_stock_level: Yup.number(
                    "Max stock harus berupa angka"
                ).nullable(),
                img_product: Yup.string().required("Foto produk wajib diisi."),
                description: Yup.string().nullable(),
            }),
        });

        const [name, nameAttr] = defineField("name");
        const [product_code, productCodeAttr] = defineField("product_code");
        const [category_product_id, categoryProductIdAttr] = defineField(
            "category_product_id"
        );
        const [unit_product_id, unitProductIdAttr] =
            defineField("unit_product_id");
        const [stock, stockAttr] = defineField("stock");
        const [minimum_stock_level, minimumStockLevelAttr] = defineField(
            "minimum_stock_level"
        );
        const [maximum_stock_level, maximumStockLevelAttr] = defineField(
            "maximum_stock_level"
        );
        const [unit_price, unitPriceAttr] = defineField("unit_price");
        const [img_product, imgProductAttr] = defineField("img_product");
        const [description, descriptionAttr] = defineField("description");

        const updateCategoryProductId = (event) => {
            category_product_id.value = event.value ? event.value.id : null;
        };

        const updateUnitProductId = (event) => {
            unit_product_id.value = event.value ? event.value.id : null;
        };

        const onFileSelect = (event) => {
            if (event.files && event.files.length > 0) {
                img_product.value = event.files[0];
            } else {
                img_product.value = null;
            }
        };

        const submitProduct = handleSubmit(async (datas, { resetForm }) => {
            loading.value = true;
            await productStore.addProduct(datas);

            if (productStore.errorAddedData) {
                loading.value = false;
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Added Product Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                openDrawer.value = false;
                loading.value = false;
                resetForm();
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Added Product Successfully",
                    detail: "Tambah Produk Berhasil",
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

            await productStore.updateDataProduct(filteredNewData, data.id);

            if (productStore.errorUpdateData) {
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Update Product Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Update Product Successfully",
                    detail: "Update Produk Berhasil",
                });
            }

            loading.value = false;
        };

        const labelStatusProduct = ref('')
        const iconStatusProduct = ref('')
        const menuModel = computed(() => [
            {
                label: "Delete",
                icon: "pi pi-fw pi-times",
                command: () => deleteProduct(selectedProduct),
            },
            {
                label: labelStatusProduct.value,
                icon: iconStatusProduct.value,
                command: () => setStatusProduct(selectedProduct),
            },
        ]);
        const onRowContextMenu = (event) => {
            if (event.data.isActive) {
                labelStatusProduct.value = "Draft"
                iconStatusProduct.value = "pi pi-fw pi-lock"
            } else {
                labelStatusProduct.value = "Active"
                iconStatusProduct.value = "pi pi-fw pi-lock-open"
            }
            cm.value.show(event.originalEvent);
        };

        const deleteProduct = async (prod) => {
            loading.value = true;
            await productStore.deleteProduct(prod.value.id);

            if (productStore.errorDeleteProduct) {
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Delete Product Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Delete Product Successfully",
                    detail: "Produk Berhasil Di Hapus",
                });
            }

            loading.value = false;
        };

        const setStatusProduct = async (prod) => {
            loading.value = true;
            const status = ref()
            if (prod.value.isActive) {
                status.value = 0
            } else {
                status.value = 1
            }
            await productStore.setStatus(status.value, prod.value.id);

            if (productStore.errorUpdateData) {
                toast.add({
                    severity: "error",
                    life: 3000,
                    summary: "Update Status Product Failed",
                    detail: "Ada kesalahan pada sisi server, mohon refresh browser.",
                });
            } else {
                toast.add({
                    severity: "success",
                    life: 3000,
                    summary: "Update Status Product Successfully",
                    detail: "Update Status Produk Berhasil",
                });
            }

            loading.value = false;
        }

        const showDetailProduct = (product) => {
            selectedDialogProduct.value = product;
            showDialogDetailProduct.value = true;
        };

        return {
            products,
            totalRecords,
            showDialogDetailProduct,
            showDetailProduct,
            selectedProduct,
            selectedDialogProduct,
            selectedCategory,
            selectedUnit,
            fileupload,
            categories,
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
            product_code,
            productCodeAttr,
            category_product_id,
            categoryProductIdAttr,
            unit_product_id,
            unitProductIdAttr,
            unit_price,
            unitPriceAttr,
            stock,
            stockAttr,
            minimum_stock_level,
            minimumStockLevelAttr,
            maximum_stock_level,
            maximumStockLevelAttr,
            img_product,
            imgProductAttr,
            description,
            descriptionAttr,
            errors,
            submitProduct,
            updateCategoryProductId,
            updateUnitProductId,
            onFileSelect,
            rows,
            rowsPerPageOptions,
            onRowsChange
        };
    },
};
</script>
