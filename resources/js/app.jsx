
import React from 'react';
import { createRoot } from 'react-dom/client';
import OrderApp from './components/OrderApp';
import "../css/app.css";
createRoot(document.getElementById('app')).render(
    <React.StrictMode>
        <OrderApp />
    </React.StrictMode>
);