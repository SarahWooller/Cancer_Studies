// resources/js/components/App.jsx

import React, { useState, useEffect } from 'react';
// --- START: React Router DOM Imports ---
import { Routes, Route, Link } from 'react-router-dom'; // Import Routes, Route, and Link
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

    // Logo source state for easy adjustment
    const appLogoSrc = "/images/your-cancer-research-uk-logo.png"; // <--- ADJUST THIS PATH TO YOUR LOGO

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

            <main className="main-content">
                <aside className="sidebar">
                    {keywords.length > 0 && (
                        <NavBar
                            keywords={keywords}
                            selectedKeywords={selectedKeywords}
                            onKeywordSelect={handleKeywordToggle}
                        />
                    )}
                </aside>

                <section className="content-area"> {/* Changed from studies-section to content-area for routing */}
                    {/* Define your routes here */}
                    <Routes>
                        {/* Route for the main studies list */}
                        <Route
                            path="/"
                            element={
                                <>
                                    <h2>CRUK Study Data Explorer</h2>
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
                                                            {/* Re-added these cells for consistency with headers */}

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
                <p>&copy; {new Date().getFullYear()} Study Data Explorer</p>
            </footer>
        </div>
    );
}

export default App;
