import { createWebHistory, createRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";

const routes = [
    {
        path: "/dashboard",
        name: "dashboard",
        component: () => import("../views/DashboardView.vue"),
        meta: { requiresAuth: true },
        children: [
            {
                path: "apotek",
                name: "apotek",
                component: () => import("../views/apoteks/Apotek.vue"),
            },
            {
                path: "pelanggan",
                name: "customer",
                component: () => import("../views/customer/Customer.vue"),
            },
            {
                path: "supplier",
                name: "supplier",
                component: () => import("../views/suppliers/Supplier.vue"),
            },
            {
                path: "produk",
                name: "product",
                component: () => import("../views/products/Product.vue"),
            },
            {
                path: "kategori",
                name: "category",
                component: () => import("../views/categories/Category.vue"),
            },
            {
                path: "satuan",
                name: "unit",
                component: () => import("../views/units/Unit.vue"),
            },
            {
                path: "pembelian-produk",
                name: "purchased-product",
                children: [
                    {
                        path: 'data-pembelian',
                        name: 'purchased-product.data-purchased',
                        component: () => import("../views/purchased_products/PurchasedProduct.vue")
                    },
                    {
                        path: 'tambah-data-pembelian',
                        name: 'purchased-product.add-data-purchased',
                        component: () => import("../views/purchased_products/AddPurchasedProduct.vue")
                    },
                    {
                        path: 'edit-data-pembelian/:reference_number',
                        name: 'purchased-product.edit-data-purchased',
                        component: () => import("../views/purchased_products/EditPurchasedProduct.vue")
                    }
                ]
            },
        ],
    },
    {
        path: "/login",
        name: "login",
        component: () => import("../views/auth/LoginView.vue"),
        meta: { requiresAuth: false },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();

    if (!authStore.isLoggedin && to.meta.requiresAuth) {
        localStorage.setItem("checkAuth", "userNotAuthenticated");

        next({ name: "login" });
        return;
    } else if (authStore.isLoggedin && to.path === "/login") {
        next({ name: "dashboard" });
        return;
    }

    next();
});

export default router;
