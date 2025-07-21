// resources/js/components/App.jsx

import React, { useState, useEffect } from 'react';
import { fetchStudies, fetchKeywords } from '../services/api';
import NavBar from './NavBar'; // Import the NavBar component

function App() {
    const [studies, setStudies] = useState([]);
    const [keywords, setKeywords] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [selectedKeywords, setSelectedKeywords] = useState([]); // State to hold selected keyword objects

    // Function to handle keyword selection/deselection from NavBar
    const handleKeywordToggle = (keywordToToggle) => {
        setSelectedKeywords(prevSelectedKeywords => {
            // Check if the keyword is already selected based on its ID
            const isSelected = prevSelectedKeywords.some(kw => kw.id === keywordToToggle.id);

            if (isSelected) {
                // If already selected, remove it
                return prevSelectedKeywords.filter(kw => kw.id !== keywordToToggle.id);
            } else {
                // If not selected, add it to the list
                return [...prevSelectedKeywords, keywordToToggle];
            }
        });
    };

    const loadInitialData = async () => {
        try {
            console.log('App.jsx (Component): Attempting to load initial data...');

            // Fetch Keywords (only once, or less frequently, as they are static)
            // We fetch keywords every time loadInitialData is called for simplicity,
            // but in a real app, you might fetch them only on mount if they rarely change.
            console.log('App.jsx (Component): Starting keywords fetch...');
            const keywordsData = await fetchKeywords();
            console.log('App.jsx (Component): Keywords Data received:', keywordsData);
            setKeywords(keywordsData);

            // Fetch Studies, passing only the IDs of selected keywords for filtering
            console.log('App.jsx (Component): Starting studies fetch with selected keyword IDs:', selectedKeywords.map(kw => kw.id));
            const studiesData = await fetchStudies(selectedKeywords.map(kw => kw.id));
            console.log('App.jsx (Component): Studies Data received:', studiesData);
            console.log('App.jsx (Component): Type of studiesData:', typeof studiesData);

            setStudies(studiesData);

            setLoading(false);
        } catch (error) {
            console.error('App.jsx (Component): Error during initial data load:', error);
            setError('Error loading initial data: ' + error.message);
            setLoading(false);
        }
    };

    // This useEffect will run on initial mount AND whenever selectedKeywords changes
    useEffect(() => {
        loadInitialData();
    }, [selectedKeywords]); // Re-run loadInitialData when selectedKeywords state changes

    // Render logic based on loading and error states
    if (loading) {
        return <div>Loading data...</div>;
    }

    if (error) {
        return <div>Error: {error}</div>;
    }

    return (
        <div className="app-container"> {/* Main container for layout */}
            <header className="app-header">
                <h1>Study Data Explorer</h1>
                {/* You can add general navigation links here if you had them */}
            </header>

            <main className="app-main-content"> {/* Main content area, structured with sidebar */}
                <aside className="sidebar">
                    {/* Render the NavBar component here, which acts as our Keyword Filter */}
                    {/* Pass the full keyword hierarchy, current selections, and toggle handler */}
                    {keywords.length > 0 && (
                        <NavBar
                            keywords={keywords}
                            selectedKeywords={selectedKeywords}
                            onKeywordSelect={handleKeywordToggle} // Pass the handler
                        />
                    )}
                </aside>

                <section className="studies-section">
                    <h2>Studies List</h2>
                    {studies.length > 0 ? (
                        <table className="studies-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    {/* Add more headers if you want to display other study fields */}
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                </tr>
                            </thead>
                            <tbody>
                                {studies.map(study => (
                                    <tr key={study.id}>
                                        <td>{study.id}</td>
                                        <td>{study.title}</td>
                                        {/* Add more cells for other study fields */}
                                        <td>{new Date(study.created_at).toLocaleDateString()}</td>
                                        <td>{new Date(study.updated_at).toLocaleDateString()}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    ) : (
                        <p>No studies found with matching filters.</p>
                    )}
                </section>
            </main>

            <footer className="app-footer">
                <p>&copy; {new Date().getFullYear()} Study Data Explorer</p>
            </footer>
        </div>
    );
}

export default App;
