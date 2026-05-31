import { useEffect, useState } from "react";
import axios from "axios";

function OrderApp() {
    const [products, setProducts] = useState([]);
    const [cart, setCart] = useState({});
    const [message, setMessage] = useState("");

    const fetchProducts = async () => {
        try {
            const response = await axios.get("/api/products");

            const productList = response.data?.data?.data || response.data?.data || [];

            setProducts(Array.isArray(productList) ? productList : []);
        } catch (error) {
            console.error(error);
            setProducts([]);
            setMessage("Failed to fetch products");
        }
    };
    useEffect(() => {
        fetchProducts();
    }, []);

    const updateQuantity = (productId, quantity) => {
        setCart({
            ...cart,
            [productId]: Number(quantity),
        });
    };

    const placeOrder = async () => {
        const items = Object.entries(cart)
            .filter(([_, quantity]) => quantity > 0)
            .map(([productId, quantity]) => ({
                product_id: Number(productId),
                quantity,
            }));

        if (items.length === 0) {
            setMessage("Please select at least one product");
            return;
        }

        try {
            const response = await axios.post("/api/orders", {
                payment_method: "cod",
                items,
            });

            setMessage(`Order created successfully. Order ID: ${response.data.data.id}`);
            setCart({});
            fetchProducts();
        } catch (error) {
            setMessage(error.response?.data?.message || "Order failed");
        }
    };

    return (
        <div className="min-h-screen bg-gray-100 px-6 py-10">
            <div className="mx-auto max-w-6xl rounded-2xl bg-white p-8 shadow-lg">
                <div className="mb-6 flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900">
                            Order Products
                        </h1>
                        <p className="mt-1 text-sm text-gray-500">
                            Select product quantity and place your order
                        </p>
                    </div>

                    <button
                        onClick={fetchProducts}
                        className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                    >
                        Refresh
                    </button>
                </div>

                {message && (
                    <div className="mb-5 rounded-lg bg-blue-50 px-4 py-3 text-sm font-medium text-blue-700">
                        {message}
                    </div>
                )}

                <div className="overflow-hidden rounded-xl border border-gray-200">
                    <table className="w-full border-collapse bg-white">
                        <thead className="bg-gray-900 text-white">
                            <tr>
                                <th className="px-5 py-4 text-left text-sm font-semibold">
                                    Name
                                </th>
                                <th className="px-5 py-4 text-left text-sm font-semibold">
                                    Price
                                </th>
                                <th className="px-5 py-4 text-left text-sm font-semibold">
                                    Stock
                                </th>
                                <th className="px-5 py-4 text-left text-sm font-semibold">
                                    Quantity
                                </th>
                            </tr>
                        </thead>

                        <tbody className="divide-y divide-gray-200">
                            {Array.isArray(products) && products.length > 0 ? (
                                products.map((product) => (
                                    <tr
                                        key={product.id}
                                        className="hover:bg-gray-50"
                                    >
                                        <td className="px-5 py-4 text-sm font-medium text-gray-900">
                                            {product.name}
                                        </td>

                                        <td className="px-5 py-4 text-sm text-gray-700">
                                            ₹{product.price}
                                        </td>

                                        <td className="px-5 py-4 text-sm">
                                            <span className="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                                {product.stock} available
                                            </span>
                                        </td>

                                        <td className="px-5 py-4">
                                            <input
                                                type="number"
                                                min="0"
                                                max={product.stock}
                                                value={cart[product.id] || ""}
                                                onChange={(e) =>
                                                    updateQuantity(
                                                        product.id,
                                                        e.target.value
                                                    )
                                                }
                                                className="w-24 rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                            />
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td
                                        colSpan="4"
                                        className="px-5 py-8 text-center text-sm text-gray-500"
                                    >
                                        No products found
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                <div className="mt-6 flex justify-end">
                    <button
                        onClick={placeOrder}
                        className="rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300"
                    >
                        Place Order
                    </button>
                </div>
            </div>
        </div>
    );
}

export default OrderApp;