import './bootstrap'; // Your existing bootstrap import

import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom'; // <--- Import BrowserRouter

import App from './components/App'; // Your main App component

// Your main application CSS
import '../css/app.css';

const app = document.getElementById('app');

if (app) {
    createRoot(app).render(
        <React.StrictMode>
            <BrowserRouter> {/* <--- Wrap your App component with BrowserRouter */}
                <App />
            </BrowserRouter>
        </React.StrictMode>
    );
}
