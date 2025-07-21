// resources/js/services/api.js

const API_BASE_URL = '/api';

/**
 * Fetches studies from the backend, optionally filtered by selected keywords.
 * @param {Array<number>} selectedKeywords An array of keyword IDs.
 * @returns {Promise<Array>} A promise that resolves to an array of study objects.
 */
export async function fetchStudies(selectedKeywords = []) {
    let url = `${API_BASE_URL}/studies`;

    // If keywords are selected, add them as query parameters
    if (selectedKeywords.length > 0) {
        const params = new URLSearchParams();
        // Ensure that selectedKeywords are just IDs if they're not already
        selectedKeywords.forEach(keywordId => params.append('keywords[]', keywordId));
        url += `?${params.toString()}`;
    }

    console.log('api.js: Preparing to fetch studies from URL:', url); // For debugging

    const response = await fetch(url);

    if (!response.ok) {
        // If the response is not OK (e.g., 404, 500), throw an error
        const errorText = await response.text(); // Get response text for more detail
        throw new Error(`HTTP error! status: ${response.status}, response: ${errorText}`);
    }

    const result = await response.json();
    console.log('api.js: Raw studies API response:', result); // For debugging

    // The Laravel controller returns studies inside a 'data' key
    return result.data;
}

/**
 * Fetches the hierarchical keyword structure from the backend.
 * @returns {Promise<Array>} A promise that resolves to an array of keyword objects.
 */
export async function fetchKeywords() {
    const url = `${API_BASE_URL}/keywords-hierarchy`;
    console.log('api.js: Preparing to fetch keywords from URL:', url); // For debugging

    const response = await fetch(url);

    if (!response.ok) {
        const errorText = await response.text();
        throw new Error(`HTTP error! status: ${response.status}, response: ${errorText}`);
    }

    const result = await response.json();
    console.log('api.js: Raw keywords API response:', result); // For debugging

    // Assuming the keywords-hierarchy endpoint returns the array directly (not wrapped in 'data')
    return result;
}
