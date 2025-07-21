import './bootstrap'; // Laravel's default JS utilities
import '../css/app.css'; // Your main CSS file

import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './components/App'; // Import your main App component

// Ensure the root element exists in your public/index.html
const container = document.getElementById('app');
if (container) {
    const root = createRoot(container);
    root.render(
        <React.StrictMode>
            <App />
        </React.StrictMode>
    );
} else {
    console.error('Root element #app not found in the DOM.');
}
