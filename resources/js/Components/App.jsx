// resources/js/components/App.jsx

import React, { useState, useEffect } from 'react';
// --- START: React Router DOM Imports ---
import { Routes, Route, Link, useLocation } from 'react-router-dom'; // Import Routes, Route, and Link
// --- END: React Router DOM Imports ---

import { fetchStudies, fetchKeywords } from '../services/api';
import NavBar from './NavBar';
import StudyDetail from './StudyDetail'; // <--- Import the new StudyDetail component

function App() {
    const [studies, setStudies] = useState([]);
    const [keywords, setKeywords] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [selectedKeywords, setSelectedKeywords] = useState([]);
    const location = useLocation(); // Get the current location object
    // Determine if the Navbar should be shown based on the current path
    // It should be shown only on the main '/' path
    const showNavbar = location.pathname === '/';
    // Logo source state for easy adjustment
    const appLogoSrc = "../images/cruk_logo.svg"; // <--- ADJUST THIS PATH TO YOUR LOGO

    const handleKeywordToggle = (keywordToToggle) => {
        setSelectedKeywords(prevSelectedKeywords => {
            const isSelected = prevSelectedKeywords.some(kw => kw.id === keywordToToggle.id);

            if (isSelected) {
                return prevSelectedKeywords.filter(kw => kw.id !== keywordToToggle.id);
            } else {
                return [...prevSelectedKeywords, keywordToToggle];
            }
        });
    };

    const loadInitialData = async () => {
        try {
            console.log('App.jsx (Component): Attempting to load initial data...');

            console.log('App.jsx (Component): Starting keywords fetch...');
            const keywordsData = await fetchKeywords();
            console.log('App.jsx (Component): Keywords Data received:', keywordsData);
            setKeywords(keywordsData);

            console.log('App.jsx (Component): Starting studies fetch with selected keyword IDs:', selectedKeywords.map(kw => kw.id));
            const studiesData = await fetchStudies(selectedKeywords.map(kw => kw.id));
            console.log('App.jsx (Component): Studies Data received:', studiesData);

            setStudies(studiesData);
            setLoading(false);
        } catch (error) {
            console.error('App.jsx (Component): Error during initial data load:', error);
            setError('Error loading initial data: ' + error.message);
            setLoading(false);
        }
    };

    useEffect(() => {
        loadInitialData();
    }, [selectedKeywords]);

    // Render logic based on loading and error states
    if (loading) {
        return <div className="loading-message">Loading application data...</div>;
    }

    if (error) {
        return <div className="error-message">Error: {error}</div>;
    }

    return (
        <div id="app-container" className="app-container"> {/* Added id for clarity, ensure HTML root matches */}
            <header className="app-header">
                {/* Your logo image here */}
                <img src={appLogoSrc} alt="Cancer Research UK Logo" className="app-logo" />
                <h1>Together we are beating Cancer</h1>
            </header>

            {/* --- START: NEW NAVBAR PLACEMENT --- */}
            {/* The NavBar is now placed horizontally below the header */}
            {showNavbar && keywords.length > 0 && ( // Only render if showNavbar is true AND keywords exist
                <nav className="horizontal-navbar-container bg-white shadow-md z-10 relative">
                    <NavBar
                        keywords={keywords}
                        selectedKeywords={selectedKeywords}
                        onKeywordSelect={handleKeywordToggle}
                    />
                </nav>
            )}
            {/* --- END: NEW NAVBAR PLACEMENT --- */}

            {/* The main content area now directly follows the horizontal navbar */}
            <main className="main-content flex-grow"> {/* Removed flex-direction: column; from here, added flex-grow */}
                {/* The sidebar (aside) is removed from here as it's no longer needed */}
                {/* <aside className="sidebar">
                    {keywords.length > 0 && (
                        <NavBar
                            keywords={keywords}
                            selectedKeywords={selectedKeywords}
                            onKeywordSelect={handleKeywordToggle}
                        />
                    )}
                </aside> */}

                <section className="content-area flex-1 p-6"> {/* flex-1 allows it to take remaining space */}
                    {/* --- START: Selected Filters Display Area (moved inside content-area) --- */}
                     {showNavbar && selectedKeywords.length > 0 && ( // Also hide filters display when Navbar is hidden
                        <div className="selected-filters-display mb-4 p-3 bg-gray-50 rounded-lg shadow-sm">
                            <h3 className="text-lg font-semibold text-gray-700 mb-2">Active Filters:</h3>
                            <div className="flex flex-wrap gap-2 items-center">
                                {selectedKeywords.map(keyword => (
                                    <div
                                        key={keyword.id}
                                        className="filter-chip flex items-center bg-pink-100 text-pink-800 text-sm font-medium px-3 py-1 rounded-full shadow-sm"
                                    >
                                        <span>{keyword.keyword}</span>
                                        <button
                                            onClick={() => handleKeywordToggle(keyword)}
                                            className="ml-2 -mr-1 p-1 rounded-full hover:bg-pink-200 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-opacity-50"
                                            aria-label={`Remove filter: ${keyword.keyword}`}
                                        >
                                            <svg className="w-3 h-3 text-pink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                ))}
                                <button
                                    onClick={() => setSelectedKeywords([])}
                                    className="clear-all-filters-button ml-2 px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded-full hover:bg-gray-300 transition-colors"
                                >
                                    Clear All
                                </button>
                            </div>
                        </div>
                    )}
                    {/* --- END: NEW Selected Filters Display Area --- */}
                    {/* Define your routes here */}
                    <Routes>
                        {/* Route for the main studies list */}
                        <Route
                            path="/"
                            element={
                                <>
                                    <h2>CRUK Study Data Explorer</h2>
                                    {/* --- START: Filtered Studies Count --- */}
                                    {showNavbar && ( // Only show count when Navbar (and filters) are visible
                                        <p className="text-gray-600 mb-4">
                                            {studies.length} studies meet your criteria.
                                        </p>
                                    )}
                                    {/* --- END: Filtered Studies Count --- */}
                                    {studies.length > 0 ? (
                                        <div className="scrollable-table-container">
                                            <table className="studies-table">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Title</th>
                                                        <th>Keywords</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {studies.map(study => (
                                                        <tr key={study.id}>
                                                            <td>{study.id}</td>
                                                            <td>
                                                                {/* Use Link component to navigate to study detail */}
                                                                <Link to={`/studies/${study.id}`} className="study-title-link">
                                                                    {study.title}
                                                                </Link>
                                                            </td>
                                                            <td>
                                                                {study.keywords && Array.isArray(study.keywords) && study.keywords.length > 0
                                                                    ? study.keywords.map(keyword => keyword.keyword).join(', ')
                                                                    : 'N/A'}
                                                            </td>
                                                            {/* Removed Created At and Updated At cells to match your provided App.jsx */}
                                                        </tr>
                                                    ))}
                                                </tbody>
                                            </table>
                                         </div>
                                    ) : (
                                        <p>No studies found with matching filters.</p>
                                    )}
                                </>
                            }
                        />

                        {/* Route for the single study detail page */}
                        {/* The :id is a URL parameter that StudyDetail will read */}
                        <Route path="/studies/:id" element={<StudyDetail />} />
                    </Routes>
                </section>
            </main>

            <footer className="app-footer">
                <p>&copy; Study Data Explorer</p> {/* Removed dynamic year to match your provided App.jsx */}
            </footer>
        </div>
    );
}

export default App;
