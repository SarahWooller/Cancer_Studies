// In resources/js/services/api.js

const API_BASE_URL = '/api';

export async function fetchStudies(selectedKeywords = []) {
    // ... (your existing fetchStudies code)
}

// MAKE SURE 'export' IS HERE:
export async function fetchKeywords() { // <--- ENSURE 'export' IS PRESENT HERE
    const response = await fetch(`${API_BASE_URL}/keywords-hierarchy`);
    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }
    const result = await response.json();
    return result;
}
